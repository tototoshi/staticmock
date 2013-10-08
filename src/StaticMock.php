<?php

use StaticMock\Mock;
use StaticMock\Stub;

/**
 * Class StaticMock
 */
class StaticMock {


    public static function mock($class_name)
    {
        return new Mock($class_name);
    }

    public static function stub($class_name)
    {
        return new Stub($class_name);
    }

}