<?php

namespace StaticMock\MethodReplacer;

use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGetName()
    {
        $parameter = (new \ReflectionFunction(
            function ($foo) {}
        ))->getParameters()[0];

        $target = new Parameter($parameter);

        $this->assertSame('foo', $target->getName());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetVar()
    {
        $parameter = (new \ReflectionFunction(
            function ($foo) {}
        ))->getParameters()[0];

        $target = new Parameter($parameter);

        $this->assertSame('$foo', $target->getVar());
    }

    /**
     * @param $expected
     * @param $func
     * @throws \ReflectionException
     * @dataProvider getArgStringDataProvider
     */
    public function testGetArgString($expected, $func)
    {
        $parameter = (new \ReflectionFunction($func))->getParameters()[0];

        $target = new Parameter($parameter);

        $this->assertSame($expected, $target->getArgString());
    }

    public function getArgStringDataProvider(): array
    {
        return [
            ['$foo', function ($foo) {}],
            ['$foo', function (&$foo) {}],
            ['...$foo', function (...$foo) {}],
            ['...$foo', function (&...$foo) {}],
            ['$foo', function ($foo = 1) {}],
            ['$foo', function (&$foo = 1) {}],
        ];
    }

    /**
     * @param $expected
     * @param $func
     * @throws \ReflectionException
     * @dataProvider getParamStringDataProvider
     */
    public function testGetParamString($expected, $func)
    {
        $parameter = (new \ReflectionFunction($func))->getParameters()[0];

        $target = new Parameter($parameter);

        $this->assertSame($expected, $target->getParamString());
    }

    public function getParamStringDataProvider(): array
    {
        return [
            ['$foo', function ($foo) {}],
            ['&$foo', function (&$foo) {}],
            ['...$foo', function (...$foo) {}],
            ['&...$foo', function (&...$foo) {}],
            ['$foo=1', function ($foo = 1) {}],
            ['&$foo=1', function (&$foo = 1) {}],
        ];
    }
}
