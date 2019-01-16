<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

final class DisallowFloatPropertyTypeRule implements Rule
{
    public function getNodeType() : string
    {
        return PropertyProperty::class;
    }

    /**
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
    {
        assert($node instanceof PropertyProperty);
        
        if (! $scope->isInClass()) {
            throw new ShouldNotHappenException();
        }

        $classReflection = $scope->getClassReflection();
        $propertyName    = $node->name->toString();
        $property        = $classReflection->getNativeProperty($node->name->toString());
        $propertyType    = $property->getType();
        if (! FloatTypeHelper::isFloat($propertyType)) {
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
