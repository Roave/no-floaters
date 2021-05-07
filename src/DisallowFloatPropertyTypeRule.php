<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\VerbosityLevel;

use function sprintf;

/**
 * @implements Rule<PropertyProperty>
 */
final class DisallowFloatPropertyTypeRule implements Rule
{
    public function getNodeType(): string
    {
        return PropertyProperty::class;
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
        $propertyName    = $node->name->toString();
        $property        = $classReflection->getNativeProperty($node->name->toString());
        $propertyType    = $property->getReadableType();
        if (! FloatTypeHelper::isFloat($propertyType)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Property %s::$%s cannot have %s as its type - floats are not allowed.',
                $property->getDeclaringClass()->getDisplayName(),
                $propertyName,
                $propertyType->describe(VerbosityLevel::typeOnly())
            ))->build(),
        ];
    }
}
