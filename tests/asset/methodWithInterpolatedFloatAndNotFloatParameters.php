<?php

namespace DisallowFloatsInMethodSignatures;

class Bar
{
    /** @param mixed $d */
    public function doFoo(
        float $a,
        string $b,
        float $c,
        $d,
        float $e
    ) {

    }
}
