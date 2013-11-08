<?php
namespace StaticMock\MethodReplacer;
use StaticMock\Exception\ClassNotFoundException;
use StaticMock\Exception\MethodNotFoundException;

/**
 * Class MethodReplaceableClass
 * @package StaticMock\MethodReplacer
 */
class MethodReplaceableClass {

    private $class_name;

    private $methods = array();

    /**
     * @param string $class_name The name of target class
     * @throws ClassNotFoundException
     */
    public function __construct($class_name)
    {
        if (!class_exists($class_name)) {
            throw new ClassNotFoundException("No such a class found ({$class_name})");
        }

        $this->class_name = $class_name;
    }

    private function getFakeCode($class_name, $method_name)
    {
        return "
            return call_user_func_array(
                array('StaticMock\\MethodReplacer\\MethodInvoker', 'invoke'),
                array_merge(array('{$class_name}', '{$method_name}'), func_get_args())
            );"
        ;
    }

    private function getStashedMethodName($method_name) {
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
     * @param string $method_name
     * @param callable $func anonymous function
     * @return $this
     * @throws MethodNotFoundException
     */
    public function addMethod($method_name, \Closure $func) {
        if (!method_exists($this->class_name, $method_name)) {
            throw new MethodNotFoundException("{$this->class_name} doesn't have such a method ({$method_name})");
        }

        $this->methods[$method_name] = $func;

        /**
         * Stash the original implementation temporarily as a method of different name.
         * Need to check the existence of stashed method not to write psuedo implementation
         * twice and forget the original implementation
         */
        if (!$this->stashedMethodExists($method_name)) {
            runkit_method_rename($this->class_name, $method_name, $this->getStashedMethodName($method_name));
        } else {
            runkit_method_remove($this->class_name, $method_name);
        }

        $code = $this->getFakeCode($this->class_name, $method_name);
        runkit_method_add($this->class_name, $method_name, '', $code, RUNKIT_ACC_STATIC);
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
        } else {
            return null;
        }

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
        if ($this->stashedMethodExists($method_name)) {
            runkit_method_remove($this->class_name, $method_name);
            runkit_method_rename($this->class_name, $this->getStashedMethodName($method_name), $method_name);
        }
        unset($this->methods[$method_name]);

        return $this;
    }

}
