<?php
namespace StaticMock;

class BadUsageTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldReceiveCalledTwice()
    {
        $this->setExpectedException('\RuntimeException');

        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('boo');
        $mock->shouldReceive('beep');
    }

    public function testTimesNotCalled()
    {
        $this->setExpectedException('\StaticMock\Exception\AssertionFailedException');

        $mock = \StaticMock::mock('\StaticMock\Car');
        $mock->shouldReceive('beep');
        // the beep method was never called
    }

    public function testTimesCalledTwice()
    {
        $this->setExpectedException('\RuntimeException');

        $mock = \StaticMock::mock('\StaticMock\Car');
        $mock->shouldReceive('beep')->times(2)->times(1)->with(5);

        $p = new Person();
        $p->warn(5);
    }

    public function testAndReturnCalledTwice()
    {
        $this->setExpectedException('\RuntimeException');

        $mock = \StaticMock::mock('\StaticMock\Car');
        $mock->shouldReceive('beep')->times(1)->with(1)->andReturn(1)->andReturn(2);

        $p = new Person();
        $this->assertEquals('beep!beep!', $p->warn(1));
    }

    public function testMethodReplacedTwice()
    {
        $this->setExpectedException('\RuntimeException');

        $mock1 = \StaticMock::mock('StaticMock\Car');
        $mock1->shouldReceive('beep')->times(1)->with(1)->andReturn(2);

        $mock2 = \StaticMock::mock('StaticMock\Car');
        $mock2->shouldReceive('beep')->times(1)->with(1)->andReturn(3);

        $p = new Person();
        $this->assertEquals('beep!beep!', $p->warn(1));
    }

}
