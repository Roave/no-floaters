<?php declare(strict_types = 1);

namespace Roave\PHPStan\Rules\Floats;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class DisallowFloatPropertyTypeRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new DisallowFloatPropertyTypeRule();
	}

	public function testRule(): void
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

}
