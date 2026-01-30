<?php

namespace DisallowFloatsInMethodSignatures;

use Exception;

use function gettype;

/**
 * @template TObject of object
 */
interface DenormalizerInterface
{
    /**
     * @param class-string<TObject>|string $type
     * @return ($type is class-string<TObject> ? TObject : mixed)
     */
    public function denormalize(mixed $data, string $type): mixed;
}

class Denormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type): mixed
    {
        return new \stdClass();
    }
}

class DummyTypeConverter {
    /**
     * @template RequestedType of 'float'|'int'
     * @param RequestedType $type
     * @return (RequestedType is 'float' ? float : int)
     */
    function convertToNumber(mixed $input, string $type): mixed {
        throw new Exception('irrelevant - ' . gettype($input) . ' - ' . $type);
    }

    /**
     * @template RequestedType of 'float'|'int'
     * @param RequestedType $type
     * @return (RequestedType is 'int' ? int : float)
     */
    function convertToNumber2(mixed $input, string $type): mixed {
        throw new Exception('irrelevant - ' . gettype($input) . ' - ' . $type);
    }
}