<?php

namespace DisallowFloatsInMethodSignatures;

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
