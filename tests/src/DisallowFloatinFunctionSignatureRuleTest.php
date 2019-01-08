<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class DisallowFloatinFunctionSignatureRuleTest extends RuleTestCase
{
    protected function getRule() : Rule
    {
        return new DisallowFloatInFunctionSignatureRule($this->createBroker());
    }

    public function testRule() : void
    {
        require_once __DIR__ . '/data/function.php';
        $this->analyse([__DIR__ . '/data/function.php'], [
            [
                'Parameter #1 $float of function DisallowFloatsInFunctionSignatures\doFoo() cannot have float as its type - floats are not allowed.',
                11,
            ],
            [
                'Parameter #2 $intOrFloat of function DisallowFloatsInFunctionSignatures\doFoo() cannot have float|int as its type - floats are not allowed.',
                11,
            ],
            [
                'Function DisallowFloatsInFunctionSignatures\doFoo() cannot have float as its return type - floats are not allowed.',
                11,
            ],
        ]);
    }

    public function testRuleWithoutNamespace() : void
    {
        require_once __DIR__ . '/data/functionWithoutNamespace.php';
        $this->analyse([__DIR__ . '/data/functionWithoutNamespace.php'], [
            [
                'Parameter #1 $float of function doFoo() cannot have float as its type - floats are not allowed.',
                9,
            ],
            [
                'Parameter #2 $intOrFloat of function doFoo() cannot have float|int as its type - floats are not allowed.',
                9,
            ],
            [
                'Function doFoo() cannot have float as its return type - floats are not allowed.',
                9,
            ],
        ]);
    }

    public function testRuleShowsAllFloatParametersAsViolations() : void
    {
        require_once __DIR__ . '/data/functionWithInterpolatedFloatAndNonFloatParameters.php';
        $this->analyse([__DIR__ . '/data/functionWithInterpolatedFloatAndNonFloatParameters.php'], [
            [
                'Parameter #1 $a of function DisallowFloatsInFunctionSignatures\functionWithInterpolatedFloatAndNonFloatParameters() cannot have float as its type - floats are not allowed.',
                6,
            ],
            [
                'Parameter #3 $c of function DisallowFloatsInFunctionSignatures\functionWithInterpolatedFloatAndNonFloatParameters() cannot have float as its type - floats are not allowed.',
                6,
            ],
            [
                'Parameter #5 $e of function DisallowFloatsInFunctionSignatures\functionWithInterpolatedFloatAndNonFloatParameters() cannot have float as its type - floats are not allowed.',
                6,
            ],
        ]);
    }

    public function testNotAutoloadedFunction() : void
    {
        $this->analyse([__DIR__ . '/data/functionNotAutoloaded.php'], []);
    }
}
