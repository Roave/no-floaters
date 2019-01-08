<?php

declare(strict_types=1);

namespace Roave\PHPStan\Rules\Floats;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorWithPhpDocs;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function sprintf;

class DisallowFloatInFunctionSignatureRule implements Rule
{
    /** @var Broker */
    private $broker;

    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function getNodeType() : string
    {
        return Node\Stmt\Function_::class;
    }

    /**
     * @param Function_ $node
     *
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
    {
        $functionName = new Name($node->name->toString());
        if (! $this->broker->hasCustomFunction($functionName, $scope)) {
            return [];
        }

        $functionReflection = $this->broker->getCustomFunction($functionName, $scope);

        $errors = [];

        foreach ($functionReflection->getVariants() as $functionVariant) {
            $errors[] = $this->violationsForParameters($functionVariant, $functionReflection);
            $errors[] = $this->returnTypeViolations($functionVariant, $functionReflection);
        }

        return array_filter(array_merge([], ...$errors));
    }

    /** @return string[] */
    private function returnTypeViolations(
        ParametersAcceptorWithPhpDocs $function,
        FunctionReflection $functionReflection
    ) : array {
        if (! FloatTypeHelper::isFloat($function->getReturnType())) {
            return [];
        }

        return [sprintf(
            'Function %s() cannot have %s as its return type - floats are not allowed.',
            $functionReflection->getName(),
            $function->getReturnType()->describe(VerbosityLevel::typeOnly())
        ),
        ];
    }

    /** @return string[]|null[] */
    private function violationsForParameters(
        ParametersAcceptorWithPhpDocs $function,
        FunctionReflection $functionReflection
    ) : array {
        $parameters = $function->getParameters();

        return array_map(
            static function (ParameterReflection $parameter, int $index) use ($functionReflection) : ?string {
                if (! FloatTypeHelper::isFloat($parameter->getType())) {
                    return null;
                }

                return sprintf(
                    'Parameter #%d $%s of function %s() cannot have %s as its type - floats are not allowed.',
                    $index + 1,
                    $parameter->getName(),
                    $functionReflection->getName(),
                    $parameter->getType()->describe(VerbosityLevel::typeOnly())
                );
            },
            array_values($parameters),
            array_keys($parameters)
        );
    }
}
