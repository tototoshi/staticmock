<?php
namespace StaticMock;

use MethodReplacer\ClassManager;

class Stub {

    private $class_name;

    private $method_name;

    public function __construct($class_name)
    {
        $this->class_name = $class_name;
    }

    public function __destruct()
    {
        ClassManager::getInstance()->deregister($this->class_name, $this->method_name);
    }

    public function method($method_name)
    {
        $this->method_name = $method_name;
        return $this;
    }

    public function returns($value)
    {
        if ($value instanceof \Closure) {
            $implementation = $value;
        } else {
            $implementation = function () use ($value) {
                return $value;
            };
        }

        ClassManager::getInstance()->register($this->class_name, $this->method_name, $implementation);
    }

}