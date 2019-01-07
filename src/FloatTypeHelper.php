<?php declare(strict_types = 1);

namespace Roave\PHPStan\Rules\Floats;

use PHPStan\Type\FloatType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

class FloatTypeHelper
{

	public static function isFloat(Type $type): bool
	{
		if ($type instanceof MixedType) {
			return false;
		}

		return !(new FloatType())->isSuperTypeOf($type)->no();
	}

}
