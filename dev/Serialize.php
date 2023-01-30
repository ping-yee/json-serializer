<?php

namespace Pingyi\JsonSerializer;

require_once '../vendor/autoload.php';
require_once '../src/ClosureSerializer/ClosureSerializer.php';

use Pingyi\JsonSerializer\ClosureSerializer\ClosureSerializer;
use stdClass;
use Zumba\JsonSerializer\JsonSerializer;

class Serialize
{
    public static function testSerialize()
    {
        $outsideData = "Jack";
        $toBeSerialized = [
            "name"      => 'N$ck',
            "arrayData" => [1, 2, 3],
            "closure_1"   => fn (string $s = "Hello") => print_r($s . PHP_EOL),
            "closure_2"   => function (string $s = "Hello") use ($outsideData) {
                print_r("{$s} {$outsideData}." . PHP_EOL);
            }
        ];

        $jsonSerializer = new JsonSerializer(new ClosureSerializer());

        $serializedData   = $jsonSerializer->serialize($toBeSerialized);
        $unserializedData = $jsonSerializer->unserialize($serializedData);

        call_user_func($unserializedData["closure_1"]);
        call_user_func($unserializedData["closure_2"]);
    }

    public static function testSerializeObject()
    {
        $toBeSerializedClass = new stdClass();
        $toBeSerializedClass->name      = "Mary";
        $toBeSerializedClass->arrayData = [1, 2, 3];
        $closure   = function (string $s = "Hello") {
            print_r("{$s}." . PHP_EOL);
        };

        $closureSerializer = new ClosureSerializer();

        $serializedData   = $closureSerializer->serialize($closure);
        $unserializedData = $closureSerializer->unserialize($serializedData);

        call_user_func($unserializedData);
    }
}


Serialize::testSerialize();
Serialize::testSerializeObject();
