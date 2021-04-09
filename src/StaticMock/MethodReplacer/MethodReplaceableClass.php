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
use StaticMock\Exception\ClassNotFoundException;
use StaticMock\Exception\ExtensionNotFoundException;
use StaticMock\Exception\MethodNotFoundException;

/**
 * Class MethodReplaceableClass
 * @package StaticMock\MethodReplacer
 */
class MethodReplaceableClass {

    private $class_name;

    /**
     * If true, use runkit or runkit7.
     * If false, use uopz.
     */
    private $use_runkit = true;

    private $methods = array();

    /**
     * @param string $class_name The name of target class
     * @throws ClassNotFoundException
     */
    public function __construct($class_name)
    {
        if (function_exists('runkit7_method_rename') || function_exists('runkit_method_rename')) {
            $this->use_runkit = true;
        } else if (function_exists('uopz_set_return')) {
            $this->use_runkit = false;
        } else {
            throw new ExtensionNotFoundException("PHP extension not found, please install runkit7(runkit) or uopz");
        }

        if (!class_exists($class_name)) {
            throw new ClassNotFoundException("No such a class found ({$class_name})");
        }

        $this->class_name = $class_name;
    }

    /**
     * @param $class_name
     * @param $method_name
     * @param $func
     * @return array
     * @throws \ReflectionException
     */
    private function getFakeCode($class_name, $method_name, $func)
    {
        $parameters = Parameters::make($func);

        $params_string = $parameters->getParamString();
        $args_string = $parameters->getArgString();
        return [
            $params_string,
            "
            return StaticMock\\MethodReplacer\\MethodInvoker::invoke(
                '{$class_name}', '{$method_name}', {$args_string}
            );
            "
        ];
    }

    private function getStashedMethodName($method_name)
    {
        /* Like python :) */
        return '_' . $this->class_name . '__' . $method_name;
    }

    private function stashedMethodExists($method_name)
    {
        return method_exists($this->class_name, $this->getStashedMethodName($method_name));
    }

    /**
     * Added the information of the pseudo implementation
     *
     * @param string   $method_name
     * @param callable $func anonymous function
     * @param callable $original original anonymous function
     * @return $this
     * @throws MethodNotFoundException
     * @throws \ReflectionException
     */
    public function addMethod($method_name, \Closure $func, \Closure $original)
    {
        if (!method_exists($this->class_name, $method_name)) {
            throw new MethodNotFoundException("{$this->class_name} doesn't have such a method ({$method_name})");
        }

        $this->methods[$method_name] = $func;

        [$params, $code] = $this->getFakeCode($this->class_name, $method_name, $original);
        if ($this->use_runkit) {
            if (!$this->stashedMethodExists($method_name)) {
                /**
                 * Stash the original implementation temporarily as a method of different name.
                 * Need to check the existence of stashed method not to write psuedo implementation
                 * twice and forget the original implementation
                 */

                if (function_exists('runkit7_method_rename')) {
                    runkit7_method_rename($this->class_name, $method_name, $this->getStashedMethodName($method_name));
                } else {
                    runkit_method_rename($this->class_name, $method_name, $this->getStashedMethodName($method_name));
                }
            } else {
                if (function_exists('runkit7_method_remove')) {
                    runkit7_method_remove($this->class_name, $method_name);
                } else {
                    runkit_method_remove($this->class_name, $method_name);
                }
            }

            if (function_exists('runkit7_method_add')) {
                runkit7_method_add($this->class_name, $method_name, $params, $code, RUNKIT_ACC_STATIC);
            } else {
                runkit_method_add($this->class_name, $method_name, $params, $code, RUNKIT_ACC_STATIC);
            }
        } else {
            $callback = eval("return function({$params}) { {$code} };");
            uopz_set_return($this->class_name, $method_name, $callback, 1);
        }

        return $this;
    }

    /**
     * Get the pseudo implementation of the method
     *
     * @param string $method_name
     * @return callable
     */
    public function getMethod($method_name)
    {
        if (isset($this->methods[$method_name])) {
            return $this->methods[$method_name];
        }

        return null;
    }

    /**
     *
     * Remove the information about the pseudo implementation of the method
     *
     * @param string $method_name
     * @return $this
     */
    public function removeMethod($method_name)
    {
        if ($this->use_runkit) {
            if ($this->stashedMethodExists($method_name)) {
                if (function_exists('runkit7_method_remove')) {
                    runkit7_method_remove($this->class_name, $method_name);
                } else {
                    runkit_method_remove($this->class_name, $method_name);
                }
                if (function_exists('runkit7_method_rename')) {
                    runkit7_method_rename($this->class_name, $this->getStashedMethodName($method_name), $method_name);
                } else {
                    runkit_method_rename($this->class_name, $this->getStashedMethodName($method_name), $method_name);
                }
            }
        } else {
            uopz_unset_return($this->class_name, $method_name);
        }
        unset($this->methods[$method_name]);

        return $this;
    }
}
