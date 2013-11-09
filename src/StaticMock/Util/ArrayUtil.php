<?php

namespace StaticMock\Util;


class ArrayUtil {

    public static function isAssoc(array $xs)
    {
        $index = 0;
        foreach ($xs as $k => $v) {
            if ($k !== $index) {
                return true;
            }
            $index++;
        }
        return false;
    }
} 