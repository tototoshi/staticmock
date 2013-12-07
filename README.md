# StaticMock

A mockery-like DSL to replace static methods in test.

```php
$mock = StaticMock::mock('FooService');
$mock
    ->shouldReceive('find')
    ->with(1)
    ->once()
    ->andReturn('Something');
```

## Motivation

Mockery (https://github.com/padraic/mockery) provides nice interfaces to create mock objects. But as for static methods, Mockery needs an alias class and we can't create a mock object in one shot with his easy DSL.


StaticMock provides Mockery-like DSL for static methods. StaticMock depends on runkit extension and rewrites static methods temporary at run-time.

## Requirements

 - PHP >=5.3
 - runkit >=1.0.3

## Install

composer.json

```js
{
    "require-dev": {
        "tototoshi/staticmock": "1.1.0"
    }
}
```

## Example
Imagine that you are writing tests for User class such like this.

### Stubbing and Mocking

```php

class User
{

    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getFeed()
    {
        $g_feed = GooglePlusClient::getFeed($this->email, 1);
        $f_feed = FacebookClient::getFeed($this->email, 1);
        return array_merge($g_feed, $f_feed);
    }

}



class GooglePlusClient
{

    public static function getFeed($email, $limit)
    {
        // send request to Google
    }
}


class FacebookClient
{

    public static function getFeed($email, $limit)
    {
        // send request to Facebook
    }
}


```

`User` class has a `getFeed` method. This method aggregates user's feeds from Google+ and Facebook. It depends on `GooglePlusClient` and `FacebookClient` to fetch feeds from their API. We sometimes want stubs for `GooglePlusClient` and `FacebookClient` to write tests for the `User` class. Our goal is only to ensure that `User` class can correctly aggregate feeds from APIs. The behavior of `GooglePlusClient` and `FacebookClient` is out of our head now.


The problem is `GooglePlusClient::getFeed` and `FacebookClient::getFeed` are static methods. If they were instace methods, we could manage their dependencies and inject stubs of them to `User` class. But since they are static methods, we can't do that.


`StaticMock` solved the problem by replacing the methods temporary at run-time. It provides the easy DSL for replacing methods. All you need to learn is only a few methods.

  - Declare the methods we want to replace with `StaticMock::mock` and `shouldReceive`.
  - The return value of the method is defined with `andReturn`.


See below. In this example, `GooglePlusClient::getFeed` and `FacebookClient::getFeed` are changed to return `array("From Google+")` and `array("From Facebook")`.

```php
class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testGetFeed()
    {
        $gmock = StaticMock::mock('GooglePlusClient');
        $fmock = StaticMock::mock('FacebookClient');
        $gmock->shouldReceive('getFeed')->andReturn(array("From Google+"));
        $fmock->shouldReceive('getFeed')->andReturn(array("From Facebook"));

        $user = new User('foo@example.com');
        $this->assertEquals(array('From Google+', 'From Facebook'), $user->getFeed());
    }

}
```

`StaticMock` also has some methods to act as mock object.

 - `never()`, `once()`, `twice()` and `times($times)` are used to check how many times they are called.
 - `with` are used to check what arguments are passed when they are called.

```php
class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testGetFeed()
    {
        $user = new User('foo@example.com');

        $gmock = StaticMock::mock('GooglePlusClient');
        $fmock = StaticMock::mock('FacebookClient');
        $gmock
            ->shouldReceive('getFeed')
            ->once()
            ->with('foo@example.com', 1)
            ->andReturn(array("From Google+"));
        $fmock
            ->shouldReceive('getFeed')
            ->once()
            ->with('foo@example.co', 1)
            ->andReturn(array("From Facebook"));
        $this->assertEquals(array('From Google+', 'From Facebook'), $user->getFeed());
    }

}
```

### Common pitfalls

Assigning a mock variable (`$mock = StaticMock::mock('MyClass')`) is required sice StaticMock is implemented with constructor and destructor magic.
The methods are replaced when the instance of `Mock` class is created by `StaticMock::mock` and reverted when the instance goes out of scope.


So, the following code doesn't work as you expect.

```php
class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testGetFeed()
    {
        StaticMock::mock('GooglePlusClient')
            ->shouldReceive('getFeed')
            ->andReturn(array("From Google+"));
        StaticMock::mock('FacebookClient')
            ->shouldReceive('getFeed')
           ->andReturn(array("From Facebook"));
        $user = new User('foo@example.com');
        $this->assertEquals(array('From Google+', 'From Facebook'), $user->getFeed());
    }

}
```



### Replacing method implementation

`andImplement` are useful to change the behavior of the method.

See below again. We are writing a test for `User::register` this time but we don't want to send email every time running the test.

```php
class User
{

    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function register()
    {
        $this->save();
        Mailer::send($this->email, 'Welcome to StaticMock');
    }

    private function save()
    {
        echo 'save!';
    }

}


class Mailer
{

    public static function send($email, $body)
    {
        // send mail
    }

}

```

Pass an anonymous function like below. Email will not be sent and only a short line will be printed on your console.


```php
class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister2()
    {
        $mock = StaticMock::mock('Mailer');
        $mock->shouldReceive('send')->andImplement(function () {
            echo "send email";
        });

        $user = new User('foo@example.com');
        $user->register();
    }

}
```

## LICENSE

BSD 3-Clause
