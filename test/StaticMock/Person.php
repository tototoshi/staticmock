<?php

namespace StaticMock;

class Person
{
    public function drive()
    {
        return Car::boo();
    }

    public function drunk_drive()
    {
        return Car::boo(-1);
    }

    public function warn($times)
    {
        return Car::beep($times);
    }

    public static function eat($rice, $meat, $vegetable, $fish)
    {
    }

}
