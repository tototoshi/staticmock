<?php
namespace StaticMock\Util;


class StringUtil {

    public static function mkString(array $elements) {
        $length = count($elements);
        $buf = '';
        for ($i = 0; $i < $length; $i++) {
            $element = $elements[$i];
            if (is_object($element)) {
                if (method_exists($element, '__toString')) {
                    $buf .= $element;
                } else {
                    $buf .= 'object';
                }
            } else {
                $buf .= (string) $element;
            }
            if ($i !== $length - 1) {
                $buf .= ', ';
            }
        }
        return '(' . $buf . ')';
    }

} 