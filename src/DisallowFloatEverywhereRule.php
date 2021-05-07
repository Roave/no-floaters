<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VerbosityLevel;

use function sprintf;

/**
 * @implements Rule<Expr>
 */
class DisallowFloatEverywhereRule implements Rule
{
    public function getNodeType(): string
    {
        return Expr::class;
    }

    /**
     * {@inheritDoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            $node instanceof Node\Expr\AssignOp
            || $node instanceof Node\Expr\Assign
        ) {
            return [];
        }

        $nodeType = $scope->getType($node);
        if (! FloatTypeHelper::isFloat($nodeType)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Cannot have %s as a result type of this expression - floats are not allowed.',
                $nodeType->describe(VerbosityLevel::typeOnly())
            ))->build(),
        ];
    }
}
