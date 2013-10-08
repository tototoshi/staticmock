<?php
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

    public function getImplementation($return_value)
    {
        $that = $this;
        if ($return_value instanceof \Closure) {
            return function () use ($return_value, $that) {
                Counter::getInstance()->increment($that->hash());
                Arguments::getInstance()->record($that->hash(), func_get_args());
                return call_user_func_array($return_value, func_get_args());
            };
        } else {
            return function () use ($return_value, $that) {
                Counter::getInstance()->increment($that->hash());
                Arguments::getInstance()->record($that->hash(), func_get_args());
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