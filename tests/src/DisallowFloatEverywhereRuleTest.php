<?php

declare(strict_types=1);

namespace Roave\PHPStanTest\Rules\Floats;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Roave\PHPStan\Rules\Floats\DisallowFloatEverywhereRule;

/**
 * @extends RuleTestCase<DisallowFloatEverywhereRule>
 */
class DisallowFloatEverywhereRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DisallowFloatEverywhereRule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../asset/expr.php'], [
            [
                'Cannot have float as a result type of this expression - floats are not allowed.',
                6,
            ],
            [
                'Cannot have float as a result type of this expression - floats are not allowed.',
                7,
            ],
            [
                'Cannot have float as a result type of this expression - floats are not allowed.',
                7,
            ],
            [
                'Cannot have float as a result type of this expression - floats are not allowed.',
                10,
            ],
        ]);
    }
}
