<?php
namespace StaticMock;


use StaticMock\Recorder\Arguments;
use StaticMock\Recorder\Counter;

class Fake {

    private $_hash;

    public function __construct()
    {
        $this->_hash = spl_object_hash($this);
    }

    public function hash()
    {
        return $this->_hash;
    }

    public function getImplementation($return_value)
    {
        if ($return_value instanceof \Closure) {
            return function () use ($return_value) {
                Counter::getInstance()->increment($this->hash());
                Arguments::getInstance()->record($this->hash(), func_get_args());
                return call_user_func_array($return_value, func_get_args());
            };
        } else {
            return function () use ($return_value) {
                Counter::getInstance()->increment($this->hash());
                Arguments::getInstance()->record($this->hash(), func_get_args());
                return $return_value;
            };
        }
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