<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;
use function assert;
use function sprintf;

class DisallowFloatEverywhereRule implements Rule
{
    public function getNodeType() : string
    {
        return Expr::class;
    }

    /**
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
    {
        if ($node instanceof Node\Expr\AssignOp
            || $node instanceof Node\Expr\Assign
        ) {
            return [];
        }

        assert($node instanceof Expr);

        $nodeType = $scope->getType($node);
        if (! FloatTypeHelper::isFloat($nodeType)) {
            return [];
        }

        return [
            sprintf(
                'Cannot have %s as a result type of this expression - floats are not allowed.',
                $nodeType->describe(VerbosityLevel::typeOnly())
            ),
        ];
    }
}
