<?php
namespace StaticMock\PHPUnit;

use StaticMock\Mock;

/**
 * Trait Assertion
 *
 * Used with PHPUnit
 * Add some useful assertions to PHPUnit's testcase.
 *
 *
 * @package StaticMock\PHPUnit
 */
trait Assertion {

    public function assertMockCalled($expected, Mock $mock)
    {
        $this->assertEquals($expected, $mock->getCalledCount());
    }

    public function assertMockArgsEqual($expected, Mock $mock)
    {
        $this->assertEquals($expected, $mock->getPassedArguments());
    }
}