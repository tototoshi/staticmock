<?php
namespace StaticMock\MethodReplacer;

/**
 * Class ClassMethodReplacementGuard
 * @package StaticMock\MethodReplacer
 */
class ClassMethodReplacementGuard
{
    private $class_and_methods;

    public function __construct()
    {
    }

    public function __destruct()
    {
        foreach ($this->class_and_methods as $class_and_methods) {
            list ($class_name, $method_name) = $class_and_methods;
            ClassManager::getInstance()->deregister($class_name, $method_name);
        }
    }

    /**
     * Use replace instead.
     *
     * @deprecated use replace instead
     * @param string $class_name
     * @param string $method_name
     * @param callable $callable
     */
    public function override($class_name, $method_name, \Closure $callable)
    {
        $this->replace($class_name, $method_name, $callable);
    }

    /**
     * Replace the method implementation
     *
     * @param string $class_name
     * @param string $method_name
     * @param callable $callable
     */
    public function replace($class_name, $method_name, \Closure $callable)
    {
        ClassManager::getInstance()->register($class_name, $method_name, $callable);
        $this->class_and_methods[] = array($class_name, $method_name);
    }

}


