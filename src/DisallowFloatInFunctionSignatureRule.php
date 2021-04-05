<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VerbosityLevel;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function sprintf;

/**
 * @implements Rule<Function_>
 */
final class DisallowFloatInFunctionSignatureRule implements Rule
{
    private Broker $broker;

    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function getNodeType(): string
    {
        return Function_::class;
    }

    /**
     * {@inheritDoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $functionName = new Name($node->name->toString());
        if (! $this->broker->hasFunction($functionName, $scope)) {
            return [];
        }

        $functionReflection = $this->broker->getFunction($functionName, $scope);

        $errors = [];

        foreach ($functionReflection->getVariants() as $functionVariant) {
            $errors[] = $this->violationsForParameters($functionVariant, $functionReflection);
            $errors[] = $this->returnTypeViolations($functionVariant, $functionReflection);
        }

        return array_filter(array_merge([], ...$errors));
    }

    /** @return RuleError[] */
    private function returnTypeViolations(
        ParametersAcceptor $function,
        FunctionReflection $functionReflection
    ): array {
        if (! FloatTypeHelper::isFloat($function->getReturnType())) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Function %s() cannot have %s as its return type - floats are not allowed.',
                $functionReflection->getName(),
                $function->getReturnType()->describe(VerbosityLevel::typeOnly())
            ))->build(),
        ];
    }

    /** @return RuleError[]|null[] */
    private function violationsForParameters(
        ParametersAcceptor $function,
        FunctionReflection $functionReflection
    ): array {
        $parameters = $function->getParameters();

        return array_map(
            static function (ParameterReflection $parameter, int $index) use ($functionReflection): ?RuleError {
                if (! FloatTypeHelper::isFloat($parameter->getType())) {
                    return null;
                }

                return RuleErrorBuilder::message(sprintf(
                    'Parameter #%d $%s of function %s() cannot have %s as its type - floats are not allowed.',
                    $index + 1,
                    $parameter->getName(),
                    $functionReflection->getName(),
                    $parameter->getType()->describe(VerbosityLevel::typeOnly())
                ))->build();
            },
            $parameters,
            array_keys($parameters)
        );
    }
}
