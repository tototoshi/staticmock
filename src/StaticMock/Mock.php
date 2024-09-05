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

use phpDocumentor\Reflection\Exception;
use StaticMock\Exception\AssertionFailedException;
use StaticMock\MethodReplacer\ClassManager;
use StaticMock\Recorder\Arguments;
use StaticMock\Recorder\Counter;
use StaticMock\Util\StringUtil;

class Mock
{
    private $class_name;

    private $method_name;

    private $fake;

    private $shouldCalledCount;

    private $shouldPassedArgs;

    private $file_instance_created;

    private $line_instance_created;

    private $assertion_done = false;

    public function __construct($class_name)
    {
        /*
         * Get the information about the place where this instance was created
         * for better error message.
         */
        $bt = debug_backtrace();
        if (isset($bt[1])) {
            if (isset($bt[1]['file'])) {
                $this->file_instance_created = $bt[1]['file'];
            }
            if (isset($bt[1]['line'])) {
                $this->line_instance_created = $bt[1]['line'];
            }
        }
        $this->fake = new Fake();
        $this->class_name = $class_name;

    }

    public function __destruct()
    {
        ClassManager::getInstance()->deregister($this->class_name, $this->method_name);
        Counter::getInstance()->clear($this->fake->hash());
        Arguments::getInstance()->clear($this->fake->hash());

        if ((!$this->assertion_done) && ($this->shouldCalledCount !== null || $this->shouldPassedArgs !== null)) {
            error_log(
                "Some expectations are set but Mock::assert() has not been called. " .
                "The instance is created at file={$this->file_instance_created}, " .
                "line={$this->line_instance_created}."
            );
        }
    }

    public function assert()
    {
        $this->assertion_done = true;

        $called_count = $this->getCalledCount();
        $passed_arguments = $this->getPassedArguments();

        if ($this->shouldCalledCount !== null) {
            if ($this->shouldCalledCount !== $called_count) {
                throw $this->createAssertionFailException(
                    "Mocked method should be called {$this->shouldCalledCount} times but called {$called_count} times"
                );
            }
        }

        if ($this->shouldPassedArgs !== null) {

            if (count($passed_arguments) === 0 || count($this->shouldPassedArgs) === max(array_keys($passed_arguments)) + 1) {

                if ($this->shouldPassedArgs !== $passed_arguments) {
                    $expected = StringUtil::methodArgsToReadableString($this->shouldPassedArgs);
                    $actual = StringUtil::methodArgsToReadableString($passed_arguments);
                    throw $this->createAssertionFailException(
                        "Mocked method should be called with $expected but called with {$actual}"
                    );
                }

            } elseif (count($this->shouldPassedArgs) === 0 && max(array_keys($passed_arguments)) + 1 !== 0) {
                $actual = StringUtil::methodArgsToReadableString($passed_arguments);
                throw $this->createAssertionFailException("Mocked method should be called with no argments but called with {$actual}");
            } else {
                foreach ($passed_arguments as $k => $v) {
                    if (array_key_exists($k, $this->shouldPassedArgs)) {
                        $expected = $this->shouldPassedArgs[$k];
                        $index = $k + 1;
                        $actual = $passed_arguments[$k];
                        if ($expected !== $actual) {
                            throw $this->createAssertionFailException(
                                "Mocked method should be called with $expected as the {$index}th argument but called with {$actual}"
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $message
     * @param $file_instance_created
     * @param $line_instance_created
     * @return AssertionFailedException
     */
    private function createAssertionFailException($message)
    {
        $e = new AssertionFailedException($message);
        if ($this->file_instance_created) {
            $e->setFile($this->file_instance_created);
        }
        if ($this->line_instance_created) {
            $e->setLine($this->line_instance_created);
        }
        return $e;
    }

    /**
     * @param $method_name
     * @return $this
     */
    public function shouldReceive($method_name)
    {
        $this->method_name = $method_name;
        $impl = $this->fake->getConstantImplementation(null);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
        return $this;
    }

    /**
     * @param $return_value
     * @return $this
     */
    public function andReturn($return_value)
    {
        $impl = $this->fake->getConstantImplementation($return_value);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
        return $this;
    }

    /**
     * @param $implementation
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function andImplement($implementation)
    {
        if (!$implementation instanceof \Closure) {
            throw new \InvalidArgumentException("arguments should be a Closure");
        }
        $impl = $this->fake->getImplementation($implementation);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl, $implementation);
        return $this;
    }

    /**
     * @param $count
     * @return $this
     */
    public function times($count)
    {
        $this->shouldCalledCount = $count;
        return $this;
    }

    /**
     * @return $this
     */
    public function never()
    {
        return $this->times(0);
    }

    /**
     * @return $this
     */
    public function once()
    {
        return $this->times(1);
    }

    /**
     * @return $this
     */
    public function twice()
    {
        return $this->times(2);
    }

    /**
     * @return $this
     */
    public function with()
    {
        $this->shouldPassedArgs = func_get_args();
        return $this;
    }

    public function withNthArg($n, $arg)
    {
        if (!is_int($n) || $n < 1) {
            throw new \InvalidArgumentException('first argument of ::withNthArgs must be a positive int.');
        }
        if (is_null($this->shouldPassedArgs)) {
            $this->shouldPassedArgs = array();
        }
        $this->shouldPassedArgs[$n - 1] = $arg;
        return $this;
    }

    /**
     * @return int
     */
    public function getCalledCount()
    {
        return $this->fake->count();
    }

    /**
     * @return array
     */
    public function getPassedArguments()
    {
        return $this->fake->args();
    }

}
