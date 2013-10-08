<?php

namespace StaticMock;


use MethodReplacer\ClassManager;
use phpDocumentor\Reflection\Exception;
use StaticMock\Exception\AssertionFailedException;
use StaticMock\Recorder\Arguments;
use StaticMock\Recorder\Counter;

class Mock {

    private $class_name;

    private $method_name;

    private $fake;

    private $shouldCalledCount;

    private $shouldPassedArgs;

    public function __construct($class_name)
    {
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
                throw new AssertionFailedException(
                    self::createAssertionFaileMessage($called_count, $this->shouldCalledCount)
                );
            }
        }

        if ($this->shouldPassedArgs) {
            if ($this->shouldPassedArgs !== $passed_arguments) {
                throw new AssertionFailedException(
                    self::createAssertionFaileMessage(
                        self::mkString($this->shouldPassedArgs),
                        self::mkString($passed_arguments)
                    )
                );
            }
        }
    }


    private static function createAssertionFaileMessage($expected, $actual)
    {
        return "Failed asserting that $actual matches expected $expected.";
    }

    private static function mkString(array $a) {
        return '(' . implode(', ', $a) . ')';
    }

    public function shouldReceive($method_name)
    {
        $this->method_name = $method_name;
        $impl = $this->fake->getImplementation(null);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
        return $this;
    }

    public function andReturn($return_value)
    {
        $impl = $this->fake->getImplementation($return_value);
        ClassManager::getInstance()->register($this->class_name, $this->method_name, $impl);
    }

    public function times($count)
    {
        $this->shouldCalledCount = $count;
        return $this;
    }

    public function once()
    {
        $this->shouldCalledCount = 1;
        return $this;
    }

    public function twice()
    {
        $this->shouldCalledCount = 2;
        return $this;
    }

    public function with()
    {
        $this->shouldPassedArgs = func_get_args();
        return $this;
    }

    public function getCalledCount()
    {
        return $this->fake->count();
    }

    public function getPassedArguments()
    {
        return $this->fake->args();
    }

}
