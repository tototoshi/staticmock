<?php
namespace StaticMock;

class StubTest extends \PHPUnit_Framework_TestCase
{

    public function testStub()
    {
        $p = new Person();
        $this->assertEquals('boo!', $p->drive());

        $stub = \StaticMock::stub('StaticMock\Car');
        $stub->method('boo')->returns('boo!boo!');

        $this->assertEquals('boo!boo!', $p->drive());
    }

    public function testStubWithArg()
    {
        $p = new Person();
        $this->assertEquals('beep!beep!beep!beep!beep!', $p->warn(5));

        $stub = \StaticMock::stub('StaticMock\Car');
        $stub->method('beep')->returns(function ($times) {
            return 'beep!' . 'x' . $times;
        });

        $this->assertEquals('beep!x5', $p->warn(5));
    }

}
