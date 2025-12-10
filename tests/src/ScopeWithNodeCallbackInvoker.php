<?php

declare(strict_types=1);

namespace Roave\PHPStanTest\Rules\Floats;

use PHPStan\Analyser\NodeCallbackInvoker;
use PHPStan\Analyser\Scope;

/** @phpstan-ignore phpstanApi.interface */
interface ScopeWithNodeCallbackInvoker extends Scope, NodeCallbackInvoker
{
}
