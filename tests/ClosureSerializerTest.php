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
        $serializedData = '{"name":"Nick","array":{"foo":"baz","xyz":"asd"},"closure_1":{"@closure":true,"value":"O:47:\"Laravel\\SerializableClosure\\SerializableClosure\":1:{s:12:\"seri
alizable\";O:46:\"Laravel\\SerializableClosure\\Serializers\\Signed\":2:{s:12:\"serializable\";s:312:\"O:46:\"Laravel\\SerializableClosure\\Serializers\\Native\":5:{s:3:\"use\";
a:0:{}s:8:\"function\";s:83:\"function (string $s = \"Hello Closure_1\") {\n                return $s;\n            }\";s:5:\"scope\";s:48:\"Pingyi\\JsonSerializer\\Test\\Closur
eSerializerTest\";s:4:\"this\";N;s:4:\"self\";s:32:\"00000000000001550000000000000000\";}\";s:4:\"hash\";s:44:\"yotONZ3w5jjUdUhAGU6+CPlOammmNGPZXH8a2BEclGU=\";}}"},"closure_2":{
"@closure":true,"value":"O:47:\"Laravel\\SerializableClosure\\SerializableClosure\":1:{s:12:\"serializable\";O:46:\"Laravel\\SerializableClosure\\Serializers\\Signed\":2:{s:12:\
"serializable\";s:278:\"O:46:\"Laravel\\SerializableClosure\\Serializers\\Native\":5:{s:3:\"use\";a:0:{}s:8:\"function\";s:49:\"fn (string $s = \"Hello Closure_2\") => $s\n
   \";s:5:\"scope\";s:48:\"Pingyi\\JsonSerializer\\Test\\ClosureSerializerTest\";s:4:\"this\";N;s:4:\"self\";s:32:\"00000000000001560000000000000000\";}\";s:4:\"hash\";s:44:\"U1
EqeSyCAT+iSoCE4PUlQsPjWh7VR2Kb9paGkF6jIbs=\";}}"}}';

        $serializedBySerializer = $this->jsonSerializer->serialize($this->closureIncludedArray);

        $this->assertEquals($serializedBySerializer, $serializedData);
    }

    public function testSerializeNormalClosure()
    {
        $serializedData = 'O:47:"Laravel\SerializableClosure\SerializableClosure":1:{s:12:"serializable";O:46:"Laravel\SerializableClosure\Serializers\Signed":2:{s:12:"serializable";s:252:"O:
46:"Laravel\SerializableClosure\Serializers\Native":5:{s:3:"use";a:0:{}s:8:"function";s:40:"fn (string $s = "Hello Closure_2") => $s";s:5:"scope";s:31:"Pingyi\JsonSerializer\Ser
ialize";s:4:"this";N;s:4:"self";s:32:"000000000000000d0000000000000000";}";s:4:"hash";s:44:"ITNhyNE0OxHZCAkT0EJ3tPeFE3IsH/tO/Gnjxcgf0qg=";}}';

        $serializedBySerializer = $this->jsonSerializer->serialize($this->closureOnly);

        $this->assertEquals($serializedBySerializer, $serializedData);
    }
}
