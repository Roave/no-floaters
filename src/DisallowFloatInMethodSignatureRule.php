<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

class DisallowFloatInMethodSignatureRule implements Rule
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
            foreach ($methodVariant->getParameters() as $i => $parameter) {
                if (! FloatTypeHelper::isFloat($parameter->getType())) {
                    continue;
                }

                $errors[] = sprintf(
                    'Parameter #%d $%s of method %s::%s() cannot have %s as its type - floats are not allowed.',
                    $i + 1,
                    $parameter->getName(),
                    $method->getDeclaringClass()->getDisplayName(),
                    $method->getName(),
                    $parameter->getType()->describe(VerbosityLevel::typeOnly())
                );
            }

            if (! FloatTypeHelper::isFloat($methodVariant->getReturnType())) {
                continue;
            }

            $errors[] = sprintf(
                'Method %s::%s() cannot have %s as its return type - floats are not allowed.',
                $method->getDeclaringClass()->getDisplayName(),
                $method->getName(),
                $methodVariant->getReturnType()->describe(VerbosityLevel::typeOnly())
            );
        }

        return $errors;
    }
}
