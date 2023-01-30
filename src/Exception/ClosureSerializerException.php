<?php

namespace PingYi\JsonSerializer\Exception;

use PingYi\JsonSerializer\Exception\ClosureSerializerExceptionInterface;

class ClosureSerializerException extends \Exception implements ClosureSerializerExceptionInterface
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function forSerializeFail(): ClosureSerializerException
    {
        return new self("Serialize Data fail, please try again.");
    }

    public static function forNotClosure(): ClosureSerializerException
    {
        return new self("The serialized data type is not closure, please put in the closure type data.");
    }

    public static function forUnserializeFail(): ClosureSerializerException
    {
        return new self("Unserialize Data fail, please try again.");
    }
}
