<?php

namespace StaticMock\MethodReplacer;

class ClassMethodReplacementGuardTest extends \PHPUnit\Framework\TestCase
{
    public function expectDefaultBehavior()
    {
        $this->assertEquals(1, A::a());
        $this->assertEquals(2, B::b());
    }

    public function expectreplacedBehavior()
    {
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'a', function () {
            return 3;
        });
        $mock->replace('StaticMock\MethodReplacer\B', 'b', function () {
            return 4;
        });
        $this->assertEquals(3, A::a());
        $this->assertEquals(4, B::b());
        $this->assertEquals(3, C::bar());
    }

    public function replaceTwice()
    {
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'a', function () {
            return 3;
        });
        $this->assertEquals(3, A::a());
        $mock->replace('StaticMock\MethodReplacer\A', 'a', function () {
            return 4;
        });
        $this->assertEquals(4, A::a());
    }

    public function test_replaceOnlyInMethodScope()
    {
        $this->expectDefaultBehavior();
        $this->expectreplacedBehavior();
        $this->expectDefaultBehavior();
    }

    public function test_canreplace()
    {
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'a', function () {
            return 3;
        });
        $this->assertEquals(3, A::a());
    }

    public function test_canreplaceMethodWithOneArg()
    {
        $this->assertEquals(1, A::withOneArg(1));
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'withOneArg', function ($arg) {
            return $arg * 2;
        });
        $this->assertEquals(2, A::withOneArg(1));
    }

    public function test_canreplaceMethodWithMultiArgs()
    {
        $this->assertEquals(0, A::withMultiArgs(1, 2));
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'withMultiArgs', function ($arg1, $arg2) {
            return $arg1 + $arg2;
        });
        $this->assertEquals(3, A::withMultiArgs(1, 2));
    }

    public function test_canreplaceMultiMethods()
    {
        $this->assertEquals(1, A::a());
        $this->assertEquals(2, A::a2());
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'a', function () {
            return 'a';
        });
        $mock->replace('StaticMock\MethodReplacer\A', 'a2', function () {
            return 'a2';
        });
        $this->assertEquals('a', A::a());
        $this->assertEquals('a2', A::a2());
    }

    public function test_canreplaceTwice()
    {
        $this->replaceTwice();
        $this->expectDefaultBehavior();
    }

    public function test_canCallreplacedMethodMultipleTimes()
    {
        $mock = new ClassMethodReplacementGuard();
        $mock->replace('StaticMock\MethodReplacer\A', 'a', function () {
            return 3;
        });
        $this->assertEquals(3, A::a());
        $this->assertEquals(3, A::a());
    }

}
