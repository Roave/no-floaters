<?php

namespace DisallowFloatsInFunctionSignatures;

/** @param mixed $d */
function functionWithInterpolatedFloatAndNonFloatParameters(
	float $a,
	string $b,
	float $c,
	$d,
	float $e
) {

}
