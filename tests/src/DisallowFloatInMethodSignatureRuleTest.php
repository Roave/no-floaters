<?php declare(strict_types = 1);

namespace Roave\PHPStan\Rules\Floats;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class DisallowFloatInMethodSignatureRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new DisallowFloatInMethodSignatureRule();
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/method.php'], [
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

}
