<?php
namespace StaticMock;


class Car {

    public static function boo()
    {
        return 'boo!';
    }

    public static function beep($times)
    {
        $buf = '';
        for ($i = 0; $i < $times; $i++) {
            $buf .= 'beep!';
        }
        return $buf;
    }
}