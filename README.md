# Json Serializer with Closure for PHP

## Introduction

This repository provides the variables, object and closure serializing and unserializing by integrate two repositories([Zumba\JsonSerializer](https://github.com/zumba/json-serializer) and [laravel/serializable-closure](https://github.com/laravel/serializable-closure)) for PHP.

It still leave the original repo [Zumba\JsonSerializer](https://github.com/zumba/json-serializer) operation and solve it's problem that cannot serialize the *closure* because the dependency repo [SuperClosure\Serializer](https://github.com/jeremeamia/super_closure) is no longer maintained.

Simply put, This repository complements the shortcomings of two repositories and provides a complete soluation for PHP Object/Closure serialization.

## Installation

### Prerequisites
1. PHP 7.4 or PHP 8^
2. Composer

### Composer Install

```bash
composer require pingyi/json-serializer
```

## How to Use

### Serializing Data
You still use the Zumba\JsonSerializer to serialize\unserialize your variables or object, it will be like:

```php
class MyCustomClass {
	public $isItAwesome = true;
	protected $nice = 'very!';
}

$instance = new MyCustomClass();

$serializer = new Zumba\JsonSerializer\JsonSerializer();
$json = $serializer->serialize($instance);
// $json will contain the content {"@type":"MyCustomClass","isItAwesome":true,"nice":"very!"}

$restoredInstance = $serializer->unserialize($json);
// $restoredInstance will be an instance of MyCustomClass
```
(From [Zumba\JsonSerializer](https://github.com/zumba/json-serializer))

And the other more operation, refer to [Zumba\JsonSerializer](https://github.com/zumba/json-serializer).

### Serializing Closure

You may serialize your data including the clousre like this:

```php
use Pingyi\JsonSerializer\ClosureSerializer\ClosureSerializer;
use Zumba\JsonSerializer\JsonSerializer;

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
```

or your class including closure, like this:

```php
use Pingyi\JsonSerializer\ClosureSerializer\ClosureSerializer;
use Zumba\JsonSerializer\JsonSerializer;
use stdClass;

$toBeSerializedClass = new stdClass();
$toBeSerializedClass->name      = "Mary";
$toBeSerializedClass->arrayData = [1, 2, 3];
$toBeSerializedClass->closure   = function (string $s = "Hello") {
    print_r("{$s}." . PHP_EOL);
};

$jsonSerializer = new JsonSerializer(new ClosureSerializer());

$serializedData   = $jsonSerializer->serialize($toBeSerializedClass);
$unserializedData = $jsonSerializer->unserialize($serializedData);

call_user_func($unserializedData->closure);
```

Also, you can serialize the simple closure by using the `ClosureSerializer`, like:

```php
$closure   = function (string $s = "Hello") {
    print_r("{$s}." . PHP_EOL);
};

$closureSerializer = new ClosureSerializer();

$serializedData   = $closureSerializer->serialize($closure);
$unserializedData = $closureSerializer->unserialize($serializedData);

call_user_func($unserializedData);
```

In addition, you can set your secret key when you construct the `ClosureSerializer()`, like:

```php
new ClosureSerializer("your secret key");
```
