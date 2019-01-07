<?php declare(strict_types = 1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;

class DisallowFloatInFunctionSignatureRule implements Rule
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getNodeType(): string
	{
		return Node\Stmt\Function_::class;
	}

	/**
	 * @param \PhpParser\Node\Stmt\Function_ $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$functionName = new Name($node->name->toString());
		if (!$this->broker->hasCustomFunction($functionName, $scope)) {
			return [];
		}

		$functionReflection = $this->broker->getCustomFunction($functionName, $scope);

		$errors = [];
		foreach ($functionReflection->getVariants() as $functionVariant) {
			foreach ($functionVariant->getParameters() as $i => $parameter) {
				if (!FloatTypeHelper::isFloat($parameter->getType())) {
					continue;
				}

				$errors[] = sprintf(
					'Parameter #%d $%s of function %s() cannot have %s as its type - floats are not allowed.',
					$i + 1,
					$parameter->getName(),
					$functionReflection->getName(),
					$parameter->getType()->describe(VerbosityLevel::typeOnly())
				);
			}

			if (!FloatTypeHelper::isFloat($functionVariant->getReturnType())) {
				continue;
			}

			$errors[] = sprintf(
				'Function %s() cannot have %s as its return type - floats are not allowed.',
				$functionReflection->getName(),
				$functionVariant->getReturnType()->describe(VerbosityLevel::typeOnly())
			);
		}

		return $errors;
	}

}
