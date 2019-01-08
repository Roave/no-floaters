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
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\VerbosityLevel;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function sprintf;

final class DisallowFloatInMethodSignatureRule implements Rule
{
    public function getNodeType() : string
    {
        return Node\Stmt\ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     *
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
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

    /** @return string[] */
    private function returnTypeViolations(
        ParametersAcceptor $method,
        MethodReflection $methodReflection
    ) : array {
        if (! FloatTypeHelper::isFloat($method->getReturnType())) {
            return [];
        }

        return [sprintf(
            'Method %s::%s() cannot have %s as its return type - floats are not allowed.',
            $methodReflection->getDeclaringClass()->getDisplayName(),
            $methodReflection->getName(),
            $method->getReturnType()->describe(VerbosityLevel::typeOnly())
        ),
        ];
    }

    /** @return string[]|null[] */
    private function violationsForParameters(
        ParametersAcceptor $function,
        MethodReflection $methodReflection
    ) : array {
        $parameters = $function->getParameters();

        return array_map(
            static function (ParameterReflection $parameter, int $index) use ($methodReflection) : ?string {
                if (! FloatTypeHelper::isFloat($parameter->getType())) {
                    return null;
                }

                return sprintf(
                    'Parameter #%d $%s of method %s::%s() cannot have %s as its type - floats are not allowed.',
                    $index + 1,
                    $parameter->getName(),
                    $methodReflection->getDeclaringClass()->getDisplayName(),
                    $methodReflection->getName(),
                    $parameter->getType()->describe(VerbosityLevel::typeOnly())
                );
            },
            array_values($parameters),
            array_keys($parameters)
        );
    }
}
