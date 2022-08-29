<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VerbosityLevel;

use function sprintf;

/** @implements Rule<Node> */
final class DisallowFloatAssignedToVariableRule implements Rule
{
    public function __construct(private Standard $printer)
    {
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * {@inheritDoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof Node\Expr\AssignOp && ! $node instanceof Node\Expr\Assign) {
            return [];
        }

        $resultType = $scope->getType($node);
        if (! FloatTypeHelper::isFloat($resultType)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Cannot assign %s to %s - floats are not allowed.',
                $resultType->describe(VerbosityLevel::typeOnly()),
                $this->printer->prettyPrintExpr($node->var),
            ))->build(),
        ];
    }
}
