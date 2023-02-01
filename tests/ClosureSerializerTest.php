<?php

namespace Pingyi\JsonSerializer\Test;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/ClosureSerializer/ClosureSerializer.php';

use Closure;
use Pingyi\JsonSerializer\ClosureSerializer\ClosureSerializer;
use Zumba\JsonSerializer\JsonSerializer;
use Laravel\SerializableClosure\SerializableClosure;
use PHPUnit\Framework\TestCase;

class ClosureSerializerTest extends TestCase
{
    /**
     * The type array data indclude the closure field.
     *
     * @var array
     */
    protected $closureIncludedArray;

    /**
     * The value of this variable type closure.
     *
     * @var Closure
     */
    protected $closureOnly;

    /**
     * The closure serializer instance, constructed in setUp().
     *
     * @var ClosureSerializer
     */
    protected $closureSerializer;

    /**
     * The json serializer instance with closureSerialzier injection, constructed in setUp().
     *
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->closureIncludedArray = [
            "name"  => "Nick",
            "array" => [
                "foo" => "baz",
                "xyz" => "asd"
            ],
            "closure_1" => function (string $s = "Hello Closure_1") {
                return $s;
            },
            "closure_2" => fn (string $s = "Hello Closure_2") => $s
        ];

        $this->closureOnly = fn (string $s = "Hello Closure_2") => $s;

        $this->closureSerializer = new ClosureSerializer();

        $this->jsonSerializer = new JsonSerializer($this->closureSerializer);

        SerializableClosure::setSecretKey('secret');
    }

    public function testClousreSerialzerSerialize()
    {
        $serializedBySerializer = $this->closureSerializer->serialize($this->closureOnly);
        $serializedByRepo       = serialize(new SerializableClosure($this->closureOnly));

        $this->assertEquals($serializedBySerializer, $serializedByRepo);
    }

    public function testClousreSerialzerUnserialize()
    {
        $serializedBySerializer = $this->closureSerializer->serialize($this->closureOnly);
        $serializedByRepo       = serialize(new SerializableClosure($this->closureOnly));

        $unSerializedBySerializer = $this->closureSerializer->unserialize($serializedBySerializer);
        $unSerializedByRepo       = unserialize($serializedByRepo)->getClosure();

        $this->assertEquals($unSerializedBySerializer, $unSerializedByRepo);
    }

    public function testSerializeArray()
    {
        $serializedData = $this->jsonSerializer->serialize($this->closureIncludedArray);

        $unSerializedArray = $this->jsonSerializer->unserialize($serializedData);

        $this->assertEquals($this->closureIncludedArray, $unSerializedArray);

        $this->assertEquals("Hello Closure_1", call_user_func($unSerializedArray["closure_1"]));

        $this->assertEquals("Hello Closure_2", call_user_func($unSerializedArray["closure_2"]));

        $this->assertEquals("Foo", call_user_func($unSerializedArray["closure_1"], "Foo"));

        $this->assertEquals("Foo", call_user_func($unSerializedArray["closure_2"], "Foo"));
    }

    public function testSerializeNormalClosure()
    {
        $serializedData = $this->jsonSerializer->serialize($this->closureOnly);

        $unSerializedClosure = $this->jsonSerializer->unserialize($serializedData);

        $this->assertEquals("Hello Closure_2", call_user_func($unSerializedClosure));

        $this->assertEquals("Foo", call_user_func($unSerializedClosure, "Foo"));
    }
}
