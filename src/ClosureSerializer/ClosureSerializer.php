<?php

namespace Pingyi\JsonSerializer\ClosureSerializer;

use Closure;
use SuperClosure\SerializerInterface;
use Laravel\SerializableClosure\SerializableClosure;
use Pingyi\JsonSerializer\Exception\ClosureSerializerException;

class ClosureSerializer implements SerializerInterface
{
    public function __construct(string $secretKey = "secret")
    {
        SerializableClosure::setSecretKey($secretKey);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(\Closure $closure)
    {
        try {
            $serialized = serialize(new SerializableClosure($closure));
        } catch (\Throwable $th) {
            throw ClosureSerializerException::forSerializeFail();
        }

        return $serialized;
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        try {
            $closure = unserialize($serialized)->getClosure();

            if (!$closure instanceof Closure) {
                throw ClosureSerializerException::forNotClosure();
            };
        } catch (\Throwable $th) {
            throw ClosureSerializerException::forUnserializeFail();
        }

        return $closure;
    }

    /**
     * @deprecated v0.1
     */
    public function getData(\Closure $closure, $forSerialization = false)
    {
        return;
    }
}
