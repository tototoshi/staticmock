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
        "tototoshi/staticmock": "1.x-dev"
    }
}
```

## Example
### Stubing and Mocking

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
        $g_feed = GooglePlusClient::getFeed($this->email);
        $f_feed = FacebookClient::getFeed($this->email);
        return array_merge($g_feed, $f_feed);
    }

}



class GooglePlusClient
{

    public static function getFeed($email)
    {
        // send request to Google
    }
}


class FacebookClient
{

    public static function getFeed($email)
    {
        // send request to Facebook
    }
}


```
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
            ->with('foo@example.com')
            ->andReturn(array("From Google+"));
        $fmock
            ->shouldReceive('getFeed')
            ->once()
            ->with('foo@example.co')
            ->andReturn(array("From Facebook"));
        $this->assertEquals(array('From Google+', 'From Facebook'), $user->getFeed());
    }

}
```

### Replacing method implementation

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
        echo 'sending email...';
    }

}

```

```php
class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $user = new User('foo@example.com');
        $user->register();
        // ....
    }

}
```

```php
class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister2()
    {
        $mock = StaticMock::mock('Mailer');
        $mock->shouldReceive('send')->andReturn(function () {
            echo "doesn't send email";
        });

        $user = new User('foo@example.com');
        $user->register();
    }
}
```
