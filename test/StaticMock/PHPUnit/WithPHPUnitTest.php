<?php

namespace StaticMock\PHPUnit;

use StaticMock\Mock;
use StaticMock\Person;

class WithPHPUnitTest extends \PHPUnit\Framework\TestCase
{

    public function assertStaticMock(Mock $mock)
    {
        $this->assertThat($mock, new StaticMockConstraint);
    }

    public function testWithPHPUnitTest()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep')->once()->andReturn('BEEP!');
        $p = new Person();
        $this->assertEquals('BEEP!', $p->warn(5));
        $this->assertStaticMock($mock);
    }

    public function testWithPHPUnitTest_Fail1()
    {
        try {
            $mock = \StaticMock::mock('StaticMock\Car');
            $mock->shouldReceive('beep')->never()->andReturn('BEEP!');
            $p = new Person();
            $this->assertEquals('BEEP!', $p->warn(5));
            $this->assertStaticMock($mock);
            $this->fail();
        } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
            $this->assertEquals('Failed asserting that mocked method should be called 0 times but called 1 times.', $e->getMessage());
        }
    }

    public function testWithPHPUnitTest_Fail2()
    {
        try {
            $mock = \StaticMock::mock('StaticMock\Car');
            $mock->shouldReceive('beep')->with(1)->andReturn('BEEP!');
            $p = new Person();
            $this->assertEquals('BEEP!', $p->warn(5));
            $this->assertStaticMock($mock);
            $this->fail();
        } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
            $this->assertEquals('Failed asserting that mocked method should be called with (1) but called with (5).', $e->getMessage());
        }
    }

}
