<?php
/*
 * Copyright (c) 2013, Toshiyuki Takahashi
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the Toshiyuki Takahashi nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICE;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace StaticMock\Util;

class StringUtil
{
    public static function methodArgsToReadableString(array $elements)
    {
        return substr(self::arrayToReadableString($elements), 5);
    }

    public static function arrayToReadableString(array $elements)
    {
        $buf = '';

        $is_assoc = ArrayUtil::isAssoc($elements);
        $length = count($elements);
        $index = 0;
        foreach ($elements as $key => $element) {

            if (is_object($element)) {
                $element_as_string = self::objectToReadableString($element);
            } elseif (is_array($element)) {
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
