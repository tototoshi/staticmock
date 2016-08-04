<?php
namespace StaticMock;

use StaticMock\Exception\AssertionFailedException;

class MockTest extends \PHPUnit_Framework_TestCase
{

    public function testMock()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('boo');
        $p = new Person();
        $p->drive();
        $this->assertEquals(1, $mock->getCalledCount());
        $mock->assert();
    }

    public function testMockShorthand()
    {
        $mock = \StaticMock::mock('StaticMock\Car::boo');
        $p = new Person();
        $p->drive();
        $this->assertEquals(1, $mock->getCalledCount());
        $mock->assert();
    }

    public function testMockConstruction()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('boo')->times(1)->andReturn(true);
        $p = new Person();
        $p->drive();
        $mock->assert();
    }

    public function testMockConstruction2()
    {
        $mock = \StaticMock::mock('StaticMock\Car')->shouldReceive('boo')->times(1)->andReturn(true);
        $p = new Person();
        $p->drive();
        $mock->assert();
    }

    public function testArgs()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep');

        $p = new Person();
        $p->warn(5);
        $this->assertEquals(1, $mock->getCalledCount());
        $this->assertEquals(array(5), $mock->getPassedArguments());
        $mock->assert();
    }

    public function testPartialArgs_Success()
    {
        $mock = \StaticMock::mock('StaticMock\Person');
        $mock->shouldReceive('eat')->withNthArg(3, null)->withNthArg(2, '2')->withNthArg(1, 1);
        Person::eat(1, '2', null, new \DateTime());
        $mock->assert();
    }

    public function testPartialArgs_Fail()
    {
        $raised = false;
        try {
            $mock = \StaticMock::mock('StaticMock\Person');
            $mock->shouldReceive('eat')->withNthArg(3, 3)->withNthArg(1, 1)->withNthArg(2, 100);
            Person::eat(1, 2, 3, 4);
            $mock->assert();
        } catch (AssertionFailedException $e) {
            $raised = true;
            $this->assertEquals('Mocked method should be called with 100 as the 2th argument but called with 2', $e->getMessage());
        }
        $this->assertTrue($raised);
    }

    public function testAssertions()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->times(1)->with(5)->andReturn('ban!');
        $p = new Person();
        $expected = $p->warn(5);
        $this->assertEquals('ban!', $expected);
        $mock->assert();
    }

    public function testNever()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->never()->andReturn('ban!');
        $mock->assert();
    }

    public function testOnce()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->with(5)->once()->andReturn('ban!');
        $p = new Person();
        $expected = $p->warn(5);
        $this->assertEquals('ban!', $expected);
        $mock->assert();
    }

    public function testTwice()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->with(5)->twice()->andReturn('ban!');
        $p = new Person();
        $expected = $p->warn(5);
        $expected = $p->warn(5);
        $this->assertEquals('ban!', $expected);
        $mock->assert();
    }

    public function testAssertionThrowsExceptionWhenFailed()
    {
        $mock = \StaticMock::mock('\StaticMock\Car');
        $mock->shouldReceive('beep')->times(2)->with(5);

        $this->setExpectedException('\StaticMock\Exception\AssertionFailedException');
        $p = new Person();
        $p->warn(5);
        $mock->assert();
    }

    public function testPassedArgsAssertionThrowsExceptionWhenFailed()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->times(1)->with(5);
        $this->setExpectedException('\StaticMock\Exception\AssertionFailedException');
        $p = new Person();
        $p->warn(4);
        $mock->assert();
    }

    public function testAndReturn()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->times(1)->andReturn('BEEP!');
        $p = new Person();
        $this->assertEquals('BEEP!', $p->warn(5));
        $mock->assert();
    }

    public function testAndReturnWithAnonymousFunction()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->times(1)->andImplement(function ($x) { return $x * 3; });

        $p = new Person();
        $actual = $p->warn(5);
        $this->assertEquals(15, $actual);
        $mock->assert();
    }

}
