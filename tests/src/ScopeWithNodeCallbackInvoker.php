<?php

declare(strict_types=1);

namespace Roave\PHPStanTest\Rules\Floats;

use PHPStan\Analyser\NodeCallbackInvoker;
use PHPStan\Analyser\Scope;

interface ScopeWithNodeCallbackInvoker extends Scope, NodeCallbackInvoker
{
}
