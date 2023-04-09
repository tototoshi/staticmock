<?php

namespace StaticMock\MethodReplacer;

class A
{
    public static function a()
    {
        return 1;
    }

    public static function a2()
    {
        return 2;
    }
    public static function withOneArg($arg)
    {
        return $arg;
    }

    public static function withMultiArgs($arg1, $arg2)
    {
        return 0;
    }

}
