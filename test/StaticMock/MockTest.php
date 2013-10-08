<?php
namespace StaticMock;

class MockTest extends \PHPUnit_Framework_TestCase
{

    public function testMock()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('boo');
        $p = new Person();
        $p->drive();
        $this->assertEquals(1, $mock->getCalledCount());
    }

    public function testMockCreation()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('boo')->times(1);
        $p = new Person();
        $p->drive();
    }

    public function testArgs()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('beep');

        $p = new Person();
        $p->warn(5);
        $this->assertEquals(1, $mock->getCalledCount());
        $this->assertEquals(array(5), $mock->getPassedArguments());
    }

    public function testAssertions()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('beep')->times(1)->shouldReceive(5)->andReturn('ban!');
        $p = new Person();
        $expected = $p->warn(5);
        $this->assertEquals('ban!', $expected);
    }

    public function testCalledAssertionThrowsExceptionWhenFailed()
    {
        $mock = \StaticMock::mock('\StaticMock\Car');
        $mock->method('beep')->times(2)->shouldReceive(5);

        $this->setExpectedException('\StaticMock\Exception\AssertionFailedException');
        $p = new Person();
        $p->warn(5);
    }

    public function testPassedArgsAssertionThrowsExceptionWhenFailed()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('beep')->times(1)->shouldReceive(5);
        $this->setExpectedException('\StaticMock\Exception\AssertionFailedException');
        $p = new Person();
        $p->warn(4);
    }

    public function testAndReturn()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('beep')->times(1)->andReturn('BEEP!');
        $p = new Person();
        $this->assertEquals('BEEP!', $p->warn(5));
    }

    public function testAndReturnWithAnonymousFunction()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->method('beep')->times(1)->andReturn(function ($x) { return $x * 3; });

        $p = new Person();
        $actual = $p->warn(5);
        $this->assertEquals(15, $actual);
    }

}
