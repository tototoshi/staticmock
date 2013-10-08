<?php
namespace StaticMock\PHPUnit;

use StaticMock\Mock;
use StaticMock\Person;
use StaticMock\PHPUnit\Assertion;

class AssertionTest extends \PHPUnit_Framework_TestCase
{

    use Assertion;

    public function testAssertMockCalled()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep');
        $p = new Person();
        $p->warn(5);
        $this->assertMockCalled(1, $mock);
    }

    public function testAssertMockArgsEqual()
    {
        $mock = \StaticMock::mock('StaticMock\Car');
        $mock->shouldReceive('beep');
        $p = new Person();
        $p->warn(5);
        $this->assertMockArgsEqual(array(5), $mock);
    }

}
