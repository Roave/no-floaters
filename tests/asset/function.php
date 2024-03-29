<?php

namespace DisallowFloatsInFunctionSignatures;

/**
 * @param float $float
 * @param int|float $intOrFloat
 * @param string $string
 * @return float
 */
function doFoo(
    float $float,
    $intOrFloat,
    string $string,
    $mixed
): float
{

}

function doBar(): string
{

}

/**
 * @return never
 */
function withNever()
{
    throw new \RuntimeException();
}
