<?php

namespace StaticMock\MethodReplacer;

use PHPUnit\Framework\TestCase;

class ParametersTest extends TestCase
{
    /**
     * @param $expected
     * @param $func
     * @dataProvider getParamStringDataProvider
     * @throws \ReflectionException
     */
    public function testGetParamString($expected, $func)
    {
        $target = Parameters::make($func);
        $this->assertSame($expected, $target->getParamString());
    }

    public function getParamStringDataProvider(): array
    {
        return [
            ['...$rest0', function () {}],
            ['...$rest0', function ($x, $y) {}],
            ['&$x,...$rest0', function (&$x) {}],
            ['$x,&$y,...$rest0', function ($x, &$y, $z) {}],
            ['...$rest0', function ($x = 1) {}],
            ['&$x=1,...$rest0', function (&$x = 1) {}],
            ['$x=1,&$y=2,...$rest0', function ($x = 1, &$y = 2, $z = 3) {}],
            ['$x,&...$y', function ($x, &...$y) {}],
            ['$rest0,&$rest1,...$rest2', function ($rest0, &$rest1) {}],
        ];
    }

    /**
     * @param $expected
     * @param $func
     * @throws \ReflectionException
     * @dataProvider getArgStringDataProvider
     */
    public function testGetArgString($expected, $func)
    {
        $target = Parameters::make($func);
        $this->assertSame($expected, $target->getArgString());
    }

    public function getArgStringDataProvider(): array
    {
        return [
            ['...$rest0', function () {}],
            ['...$rest0', function ($x, $y) {}],
            ['$x,...$rest0', function (&$x) {}],
            ['$x,$y,...$rest0', function ($x, &$y, $z) {}],
            ['...$rest0', function ($x = 1) {}],
            ['$x,...$rest0', function (&$x = 1) {}],
            ['$x,$y,...$rest0', function ($x = 1, &$y = 2, $z = 3) {}],
            ['$x,...$y', function ($x, &...$y) {}],
            ['$rest0,$rest1,...$rest2', function ($rest0, &$rest1) {}],
        ];
    }
}
