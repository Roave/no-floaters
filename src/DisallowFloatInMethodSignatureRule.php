<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\VerbosityLevel;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function sprintf;

/**
 * @implements Rule<ClassMethod>
 */
final class DisallowFloatInMethodSignatureRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * {@inheritDoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            throw new ShouldNotHappenException();
        }

        $classReflection = $scope->getClassReflection();
        $methodName      = $node->name->toString();
        $method          = $classReflection->getNativeMethod($methodName);

        $errors = [];

        foreach ($method->getVariants() as $methodVariant) {
            $errors[] = $this->violationsForParameters($methodVariant, $method);
            $errors[] = $this->returnTypeViolations($methodVariant, $method);
        }

        return array_filter(array_merge([], ...$errors));
    }

    /** @return RuleError[] */
    private function returnTypeViolations(
        ParametersAcceptor $method,
        MethodReflection $methodReflection
    ): array {
        if (! FloatTypeHelper::isFloat($method->getReturnType())) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Method %s::%s() cannot have %s as its return type - floats are not allowed.',
                $methodReflection->getDeclaringClass()->getDisplayName(),
                $methodReflection->getName(),
                $method->getReturnType()->describe(VerbosityLevel::typeOnly())
            ))->build(),
        ];
    }

    /** @return RuleError[]|null[] */
    private function violationsForParameters(
        ParametersAcceptor $function,
        MethodReflection $methodReflection
    ): array {
        $parameters = $function->getParameters();

        return array_map(
            static function (ParameterReflection $parameter, int $index) use ($methodReflection): ?RuleError {
                if (! FloatTypeHelper::isFloat($parameter->getType())) {
                    return null;
                }

                return RuleErrorBuilder::message(sprintf(
                    'Parameter #%d $%s of method %s::%s() cannot have %s as its type - floats are not allowed.',
                    $index + 1,
                    $parameter->getName(),
                    $methodReflection->getDeclaringClass()->getDisplayName(),
                    $methodReflection->getName(),
                    $parameter->getType()->describe(VerbosityLevel::typeOnly())
                ))->build();
            },
            $parameters,
            array_keys($parameters)
        );
    }
}
