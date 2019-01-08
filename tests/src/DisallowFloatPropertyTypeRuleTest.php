<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;

class DisallowFloatPropertyTypeRuleTest extends RuleTestCase
{
    protected function getRule() : Rule
    {
        return new DisallowFloatPropertyTypeRule();
    }

    public function testRule() : void
    {
        $this->analyse([__DIR__ . '/data/property.php'], [
            [
                'Property DisallowFloatsInProperties\Foo::$foo cannot have float as its type - floats are not allowed.',
                9,
            ],
            [
                'Property DisallowFloatsInProperties\Foo::$bar cannot have float|int as its type - floats are not allowed.',
                12,
            ],
        ]);
    }

    /**
     * Verifies that the impossible scenario of a method signature is not declared in a class method
     */
    public function testRuleWillNotWorkWhenNotInClassScope() : void
    {
        $rule = new DisallowFloatPropertyTypeRule();

        $node  = $this->createMock(Node::class);
        $scope = $this->createMock(Scope::class);

        $scope
            ->method('isInClass')
            ->willReturn(false);

        $this->expectException(ShouldNotHappenException::class);

        $rule->processNode($node, $scope);
    }
}
