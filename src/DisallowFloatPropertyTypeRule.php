<?php declare(strict_types = 1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;

class DisallowFloatPropertyTypeRule implements Rule
{

	public function getNodeType(): string
	{
		return Node\Stmt\PropertyProperty::class;
	}

	/**
	 * @param \PhpParser\Node\Stmt\PropertyProperty $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!$scope->isInClass()) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		$classReflection = $scope->getClassReflection();
		$propertyName = $node->name->toString();
		$property = $classReflection->getNativeProperty($node->name->toString());
		$propertyType = $property->getType();
		if (!FloatTypeHelper::isFloat($propertyType)) {
			return [];
		}

		return [
			sprintf(
				'Property %s::$%s cannot have %s as its type - floats are not allowed.',
				$property->getDeclaringClass()->getDisplayName(),
				$propertyName,
				$propertyType->describe(VerbosityLevel::typeOnly())
			),
		];
	}

}
