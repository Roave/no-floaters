<?php

declare(strict_types=1);

namespace Roave\PHPStanTest\Rules\Floats;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Roave\PHPStan\Rules\Floats\DisallowFloatInMethodSignatureRule;

/**
 * @extends RuleTestCase<DisallowFloatInMethodSignatureRule>
 */
final class DisallowFloatInMethodSignatureRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DisallowFloatInMethodSignatureRule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../asset/method.php'], [
            [
                'Parameter #1 $float of method DisallowFloatsInMethodSignatures\Foo::doFoo() cannot have float as its type - floats are not allowed.',
                14,
            ],
            [
                'Parameter #2 $intOrFloat of method DisallowFloatsInMethodSignatures\Foo::doFoo() cannot have float|int as its type - floats are not allowed.',
                14,
            ],
            [
                'Method DisallowFloatsInMethodSignatures\Foo::doFoo() cannot have float as its return type - floats are not allowed.',
                14,
            ],
        ]);
    }

    public function testRuleShowsAllFloatParametersAsViolations(): void
    {
        $this->analyse([__DIR__ . '/../asset/methodWithInterpolatedFloatAndNotFloatParameters.php'], [
            [
                'Parameter #1 $a of method DisallowFloatsInMethodSignatures\Bar::doFoo() cannot have float as its type - floats are not allowed.',
                8,
            ],
            [
                'Parameter #3 $c of method DisallowFloatsInMethodSignatures\Bar::doFoo() cannot have float as its type - floats are not allowed.',
                8,
            ],
            [
                'Parameter #5 $e of method DisallowFloatsInMethodSignatures\Bar::doFoo() cannot have float as its type - floats are not allowed.',
                8,
            ],
        ]);
    }

    /**
     * Verifies that the impossible scenario of a method signature is not declared in a class method
     */
    public function testRuleWillNotWorkWhenNotInClassScope(): void
    {
        $rule = new DisallowFloatInMethodSignatureRule();

        $node  = $this->createMock(ClassMethod::class);
        $scope = $this->createMock(Scope::class);

        $scope
            ->method('isInClass')
            ->willReturn(false);

        $this->expectException(ShouldNotHappenException::class);
        $this->expectExceptionMessage('Internal error.');

        $rule->processNode($node, $scope);
    }
}
