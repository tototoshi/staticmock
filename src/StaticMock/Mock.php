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
use StaticMock\Exception\BadUsageException;
use StaticMock\MethodReplacer\ClassManager;
use StaticMock\Recorder\Arguments;
use StaticMock\Recorder\Counter;
use StaticMock\Util\StringUtil;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Mock {

    private $class_name;

    private $method_name;

    private $fake;

    private $shouldCalledCount;

    private $shouldPassedArgs;

    private $file_instance_created;

    private $line_instance_created;

    private $fake_implementation;

    private $do_assertion = true;

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
        $called_count = $this->getCalledCount();
        $passed_arguments = $this->getPassedArguments();

        ClassManager::getInstance()->deregister($this->class_name, $this->method_name);
        Counter::getInstance()->clear($this->fake->hash());
        Arguments::getInstance()->clear($this->fake->hash());

        if (!$this->do_assertion) {
            return;
        }

        if ($this->shouldCalledCount === null) {
            if ($called_count == 0) {
                throw $this->createAssertionFailException(
                    "`{$this->method_name}` should be called at least once."
                );
            }
        } else {
            if ($this->shouldCalledCount !== $called_count) {
                throw $this->createAssertionFailedExceptionFromExpectedValueAndActualValue(
                    $called_count,
                    $this->shouldCalledCount
                );
            }
        }

        if ($this->shouldPassedArgs) {
            if ($this->shouldPassedArgs !== $passed_arguments) {
                throw $this->createAssertionFailedExceptionFromExpectedValueAndActualValue(
                    StringUtil::methodArgsToReadableString($this->shouldPassedArgs),
                    StringUtil::methodArgsToReadableString($passed_arguments)
                );
            }
        }
    }

    private function isAlreadyBuilt()
    {
        return $this->fake_implementation !== null;
    }

    /**
     * @param $expected
     * @param $actual
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

    private function createAssertionFailedExceptionFromExpectedValueAndActualValue($expected, $actual)
    {
        $message = "Failed asserting that $actual matches expected $expected.";
        return $this->createAssertionFailException($message);
    }

    /**
     * @param $method_name
     * @return $this
     */
    public function shouldReceive($method_name)
    {
        if ($this->isAlreadyBuilt()) {
            $this->do_assertion = false;
            throw new BadUsageException("Mock object is Already built.");
        }
        if ($this->method_name !== null) {
            $this->do_assertion = false;
            throw new BadUsageException("`shouldReceive` should not be called twice.");
        }
        $this->method_name = $method_name;
        $impl = $this->fake->getConstantImplementation(null);



        if (ClassManager::getInstance()->getManagedClassOrNewOne($this->class_name)->getMethod($this->method_name) !== null) {
            $this->do_assertion = false;
            throw new BadUsageException("`shouldReceive` should not be called twice.");
        }

        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
        return $this;
    }

    /**
     * @param $return_value
     * @return $this;
     */
    public function andReturn($return_value)
    {
        if ($this->method_name === null) {
            $this->do_assertion = false;
            throw new BadUsageException("`shouldReceive` should be called before `andReturn`.");
        }
        if ($this->isAlreadyBuilt()) {
            $this->do_assertion = false;
            throw new BadUsageException("Mock object is Already built.");
        }
        $this->fake_implementation = $this->fake->getConstantImplementation($return_value);
        ClassManager::getInstance()->register(
            $this->class_name,
            $this->method_name,
            $this->fake_implementation
        );
        return $this;
    }

    /**
     * @param $implementation
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function andImplement($implementation)
    {
        if (! $implementation instanceof \Closure) {
            throw new \InvalidArgumentException("arguments should be a Closure");
        }
        if ($this->method_name === null) {
            $this->do_assertion = false;
            throw new BadUsageException("`shouldReceive` should be called before `andImplement`.");
        }
        if ($this->isAlreadyBuilt()) {
            $this->do_assertion = false;
            throw new BadUsageException("Mock object is Already built.");
        }
        $this->fake_implementation = $this->fake->getImplementation($implementation);
        ClassManager::getInstance()->register(
            $this->class_name,
            $this->method_name,
            $this->fake_implementation
        );
        return $this;
    }

    /**
     * @param $count
     * @return $this
     */
    public function times($count)
    {
        if ($this->shouldCalledCount !== null) {
            $this->do_assertion = false;
            throw new BadUsageException("`times`, `never`, `once`, `twice` should not be called twice");
        }
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
        if ($this->shouldPassedArgs !== null) {
            $this->do_assertion = false;
            throw new BadUsageException("`with` should not be called twice");
        }
        $this->shouldPassedArgs = func_get_args();
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
