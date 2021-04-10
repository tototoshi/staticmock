<?php

namespace StaticMock;

use PHPUnit\Framework\TestCase;

class PassingByReferenceTest extends TestCase
{
    public function testNormal()
    {
        $mock = \StaticMock::mock(PassingByReferenceTarget::class)
            ->shouldReceive('f')
            ->andImplement(function (&$x) {
                $x = 100;
            });

        PassingByReferenceTarget::f($x);
        $this->assertSame(100, $x);
        $mock->assert();
    }

    public function testVariadic()
    {
        $mock = \StaticMock::mock(PassingByReferenceTarget::class)
            ->shouldReceive('g')
            ->andImplement(function (&...$x) {
                $x[0] = 100;
                $x[1] = 200;
            });

        PassingByReferenceTarget::g($x, $y);
        $this->assertSame(100, $x);
        $this->assertSame(200, $y);
        $mock->assert();
    }

    public function testOptional()
    {
        $mock = \StaticMock::mock(PassingByReferenceTarget::class)
            ->shouldReceive('h')
            ->andImplement(function ($x = 'abc', &$y = 'def') {
                return $x . $y;
            });

        $this->assertSame('abcdef', PassingByReferenceTarget::h());
        $mock->assert();
    }
}
