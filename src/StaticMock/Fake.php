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

namespace StaticMock;


use StaticMock\Recorder\Arguments;
use StaticMock\Recorder\Counter;

class Fake {

    private $_hash;

    public function __construct()
    {
        $this->_hash = md5(uniqid(rand(), true));
    }

    public function hash()
    {
        return $this->_hash;
    }

    public function getImplementation($implementation)
    {
        $that = $this;
        return function (&...$args) use ($implementation, $that) {
            Counter::getInstance()->increment($that->hash());
            Arguments::getInstance()->record($that->hash(), ...$args);
            return $implementation(...$args);
        };
    }

    public function getConstantImplementation($return_value)
    {
        $that = $this;
        return function () use ($return_value, $that) {
            Counter::getInstance()->increment($that->hash());
            Arguments::getInstance()->record($that->hash(), func_get_args());
            return $return_value;
        };
    }

    public function count()
    {
        return Counter::getInstance()->get($this->hash());
    }

    public function args()
    {
        return Arguments::getInstance()->get($this->hash());
    }

}
