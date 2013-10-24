<?php

use StaticMock\Mock;
use StaticMock\Stub;

/**
 * Class StaticMock
 */
class StaticMock {

    /**
     * Factory method to create a mock in one shot.
     *
     * @param $class_name
     * @return Mock
     */
    public static function mock($class_name)
    {
        return new Mock($class_name);
    }

}