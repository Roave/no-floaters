<?php

namespace DisallowFloatsEverywhere;

function () {
	$foo = 1.3;
	$foo += 1.3;

	foo(
		3.14
	);
};
