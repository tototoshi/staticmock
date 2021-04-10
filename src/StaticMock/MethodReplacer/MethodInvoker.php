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

namespace StaticMock\MethodReplacer;
use StaticMock\Exception\MethodInvocationException;

/**
 * Class MethodInvoker
 * @package StaticMock\MethodReplacer
 */
class MethodInvoker {

    /**
     * Invoke pseudo method that has been registered to ClassManager
     *
     * @param string $class_name
     * @param string $method_name
     * @return mixed
     * @throws \StaticMock\Exception\MethodInvocationException
     */
    public static function invoke($class_name, $method_name, &...$method_args)
    {
         $managed_class = ClassManager::getInstance()->getManagedClassOrNewOne($class_name);

        $fake_method = $managed_class->getMethod($method_name);

        if (!$fake_method) {
            throw new MethodInvocationException("Method not found! {$class_name}::{$method_name}");
        }

        return $fake_method(...$method_args);
    }

}
