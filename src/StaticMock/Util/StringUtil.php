<?php
namespace StaticMock\Util;


class StringUtil {

    public static function methodArgsToReadableString(array $elements) {
        return substr(self::arrayToReadableString($elements), 5);
    }

    public static function arrayToReadableString(array $elements) {
        $buf = '';

        $is_assoc = ArrayUtil::isAssoc($elements);
        $length = count($elements);
        $index = 0;
        foreach ($elements as $key => $element) {

            if (is_object($element)) {
                $element_as_string = self::objectToReadableString($element);
            } else if (is_array($element)) {
                $element_as_string = self::arrayToReadableString($element);
            } else {
                $element_as_string = (string) $element;
            }

            if ($index !== $length - 1) {
                $element_as_string .= ', ';
            }

            if ($is_assoc) {
                $buf .= "$key => $element_as_string";
            } else {
                $buf .= $element_as_string;
            }

            $index++;
        }
        return 'Array(' . $buf . ')';
    }

    public static function objectToReadableString($object)
    {
        if (method_exists($object, '__toString')) {
            return (string) $object;
        } elseif (is_resource($object)) {
            return 'resource';
        } else {
            return 'object';
        }
    }

} 