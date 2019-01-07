<?php

namespace DisallowFloatsInMethodSignatures;

class Foo
{

	/**
	 * @param float $float
	 * @param int|float $intOrFloat
	 * @param string $string
	 * @return float
	 */
	public function doFoo(
		float $float,
		$intOrFloat,
		string $string,
		$mixed
	): float
	{

	}

	public function doBar(): string
	{

	}

}
