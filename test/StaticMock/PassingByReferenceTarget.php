<?php

namespace StaticMock;

class PassingByReferenceTarget
{
    public static function f(&$x)
    {
    }

    public static function g(&...$x)
    {
    }

    public static function h($x = 'a', &$y = 'b'): string
    {
        return '';
    }
}
