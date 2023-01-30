<?php

namespace Pingyi\ClosureSerializer;

use Closure;
use SuperClosure\SerializerInterface;
use Laravel\SerializableClosure\SerializableClosure;

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
            //throw something...
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
                //throw something...
            }
        } catch (\Throwable $th) {
            //throw something...
        }

        return $closure;
    }

    /**
     * @deprecated v1.0
     */
    public function getData(\Closure $closure, $forSerialization = false)
    {
        return;
    }
}
