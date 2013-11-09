<?php

namespace StaticMock;


use phpDocumentor\Reflection\Exception;
use StaticMock\Exception\AssertionFailedException;
use StaticMock\MethodReplacer\ClassManager;
use StaticMock\Recorder\Arguments;
use StaticMock\Recorder\Counter;
use StaticMock\Util\StringUtil;

class Mock {

    private $class_name;

    private $method_name;

    private $fake;

    private $shouldCalledCount;

    private $shouldPassedArgs;

    private $file_instance_created;

    private $line_instance_created;

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

        if ($this->shouldCalledCount) {
            if ($this->shouldCalledCount !== $called_count) {
                throw $this->createAssertionFailException(
                    $called_count,
                    $this->shouldCalledCount,
                    $this->file_instance_created,
                    $this->line_instance_created
                );
            }
        }

        if ($this->shouldPassedArgs) {
            if ($this->shouldPassedArgs !== $passed_arguments) {
                throw $this->createAssertionFailException(
                    StringUtil::arrayToReadableString($this->shouldPassedArgs),
                    StringUtil::arrayToReadableString($passed_arguments),
                    $this->file_instance_created,
                    $this->line_instance_created
                );
            }
        }
    }


    /**
     * @param $expected
     * @param $actual
     * @param $file_instance_created
     * @param $line_instance_created
     * @return AssertionFailedException
     */
    private function createAssertionFailException($expected, $actual, $file_instance_created, $line_instance_created)
    {
        $message = "Failed asserting that $actual matches expected $expected.";
        $e = new AssertionFailedException($message);
        if ($file_instance_created) {
            $e->setFile($file_instance_created);
        }
        if ($line_instance_created) {
            $e->setLine($line_instance_created);
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
        $impl = $this->fake->getImplementation(null);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
        return $this;
    }

    /**
     * @param $return_value
     */
    public function andReturn($return_value)
    {
        $impl = $this->fake->getImplementation($return_value);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
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
    public function once()
    {
        $this->shouldCalledCount = 1;
        return $this;
    }

    /**
     * @return $this
     */
    public function twice()
    {
        $this->shouldCalledCount = 2;
        return $this;
    }

    /**
     * @return $this
     */
    public function with()
    {
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
