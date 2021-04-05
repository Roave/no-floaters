# roave/no-floaters

![`roave/no-floaters`](logo/roave-no-floaters.png)

[![Build Status](https://badgen.net/github/checks/Roave/no-floaters/master)](https://github.com/Roave/no-floaters/actions)
[![Latest Stable Version](https://poser.pugx.org/roave/no-floaters/v/stable.png)](https://packagist.org/packages/roave/no-floaters)

This library is a [PHPStan](https://github.com/phpstan/phpstan) plugin
that disallows:

 * declaration of `float` properties
 * `float` method parameters
 * `float` method return types
 * assignment of `float` values to variables or properties

The reason for this restriction is that rounding errors coming
from floating point arithmetic operations are not acceptable in
certain business logic scenario, such as dealing with money,
evaluating exam results, rocket science, etc.

An example of such problems can be seen with the following typical
[example](https://3v4l.org/MJqJe):

```php
var_dump((0.7 + 0.1) === 0.8); // output: bool(false)
```

This can mean no trouble at all, or a lot of trouble, depending
on how many numbers you are running through your system, so it
is advisable to avoid `float` for domains where rounding can
potentially lead to trouble.

`float` is still perfectly acceptable in many programming contexts,
and this ruleset should only be applied where it is critical not
to introduce rounding errors.

## Installation

```sh
composer require --dev roave/no-floaters
```

## Configuration

In your `phpstan.neon` configuration, add following section:

```neon
includes:
	- vendor/roave/no-floaters/rules.neon
```

Optionally, you can configure the library to disallow any
`float`-producing expression at all, by adding following to your
`phpstan.neon`:

```neon
parameters:
	disallowFloatsEverywhere: true
```

If the above is enabled, given the following `example-file.php`
contents:

```php
<?php

$a = 1 / 3;
```

You should get something like following:

```
vendor/bin/phpstan analyse example-file.php -l 7
 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 ------ -----------------------------------------------------
  Line   example-file.php
 ------ -----------------------------------------------------
  3      Cannot assign float to $a - floats are not allowed.
 ------ -----------------------------------------------------


 [ERROR] Found 1 error

```
## roave/no-floaters for enterprise

Available as part of the Tidelift Subscription.

The maintainers of roave/no-floaters and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-roave-no-floaters?utm_source=packagist-roave-no-floaters&utm_medium=referral&utm_campaign=enterprise&utm_term=repo).


If you need help with setting up this library in your project,
you can contact us at team@roave.com for consulting/support.
