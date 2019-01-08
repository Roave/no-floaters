<?php

namespace DisallowFloatsInProperties;

class Foo
{

	/** @var float */
	private $foo;

	/** @var float|int */
	private $bar;

    /** @var int */
    private $baz;

    /** @var mixed */
    private $taz;

}
