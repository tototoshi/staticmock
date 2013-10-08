# StaticMock

A mockery-like DSL to replace static method in test.

## Requirements

 - PHP >=5.5
 - runkit >=1.0.3

## Install

composer.json

```js
{
    "require": {
        "tototoshi/staticmock": "dev-master"
    }
}
```

## Example

```
<?php
require 'vendor/autoload.php';


class UserRepository
{

    public static function find($id)
    {
        $users = array(
            new User('John'),
            new User('Paul'),
            new User('George'),
            new User('Ringo')
        );
        return $users[$id];
    }


}

class User
{
    private $name;

    function __construct($name)
    {
        $this->name = $name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

}

class UserService
{

    public static function getSomeone($id)
    {
        return UserRepository::find($id);
    }

}


function test0()
{
    $arg_id = 1;
    $actual = UserService::getSomeone($arg_id);
    assert($actual->getName() == 'Paul');
}

function test1()
{
    $mock = StaticMock::mock('UserRepository');
    $arg_id = 1;
    $mock->method('find')->times(1)->shouldReceive($arg_id)->andReturn(new User('Eric'));
    $actual = UserService::getSomeone($arg_id);
    assert($actual->getName() == 'Eric');
}

function test2()
{
    $mock = StaticMock::mock('UserRepository');
    $arg_id = 1;
    $mock->method('find')->times(1)->andReturn('Eric');
    UserService::getSomeone($arg_id);
    UserService::getSomeone($arg_id);
}

test0();
test1();

try {
    test2();
} catch (\StaticMock\Exception\AssertionFailedException $e) {
    print $e->getMessage(); // Failed asserting that 1 matches expected 2.
}
```