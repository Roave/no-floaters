<?php

declare(strict_types=1);

namespace Roave\PHPStanTest\Rules\Floats;

use PhpParser\PrettyPrinter\Standard;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Roave\PHPStan\Rules\Floats\DisallowFloatAssignedToVariableRule;

/**
 * @extends RuleTestCase<DisallowFloatAssignedToVariableRule>
 */
final class DisallowFloatAssignedToVariableRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DisallowFloatAssignedToVariableRule(new Standard());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../asset/assign.php'], [
            [
                'Cannot assign float to $test - floats are not allowed.',
                18,
            ],
            [
                'Cannot assign float|int to $test2 - floats are not allowed.',
                19,
            ],
            [
                'Cannot assign float to $this->foo - floats are not allowed.',
                21,
            ],
            [
                'Cannot assign float to $this->bar[\'test\'] - floats are not allowed.',
                22,
            ],
            [
                'Cannot assign float to $test - floats are not allowed.',
                31,
            ],
        ]);
    }
}
