<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

class DisallowFloatAssignedToVariableRule implements Rule
{
    /** @var Standard */
    private $printer;

    public function __construct(Standard $printer)
    {
        $this->printer = $printer;
    }

    public function getNodeType() : string
    {
        return Node::class;
    }

    /**
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
    {
        if (! $node instanceof Node\Expr\AssignOp && ! $node instanceof Node\Expr\Assign) {
            return [];
        }

        $resultType = $scope->getType($node);
        if (! FloatTypeHelper::isFloat($resultType)) {
            return [];
        }

        return [
            sprintf(
                'Cannot assign %s to %s - floats are not allowed.',
                $resultType->describe(VerbosityLevel::typeOnly()),
                $this->printer->prettyPrintExpr($node->var)
            ),
        ];
    }
}
