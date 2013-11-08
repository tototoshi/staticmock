<?php
namespace StaticMock\MethodReplacer;
use StaticMock\Marker\Singleton;

/**
 * Class ClassManager
 * @package StaticMock\MethodReplacer
 */
class ClassManager implements Singleton {

    private static $instance = null;

    private $runkit_managed_classes = array();

    /*
     * This class is a singleton.
     */
    private function __construct()
    {
    }

    /**
     * Get the instance of this class
     *
     * @return ClassManager
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            $instance = new ClassManager();
            self::$instance = $instance;
            return $instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * Get MethodReplaceableClass that is already managed by this class.
     *
     * @param string $class_name
     * @return MethodReplaceableClass
     */
    public function getManagedClassOrNewOne($class_name)
    {
        if (isset($this->runkit_managed_classes[$class_name])) {
            return $this->runkit_managed_classes[$class_name];
        } else {
            return new MethodReplaceableClass($class_name);
        }
    }

    /**
     * Register the pseudo implementation
     *
     * @param string $class_name
     * @param string $method_name
     * @param callable $method_implementation
     */
    public function register($class_name, $method_name, \Closure $method_implementation)
    {
        $this->runkit_managed_classes[$class_name] =
            $this
                ->getManagedClassOrNewOne($class_name)
                ->addMethod($method_name, $method_implementation);
    }

    /**
     * Register the pseudo implementation
     *
     * @param string $class_name
     * @param string $method_name
     */
    public function deregister($class_name, $method_name)
    {
        $this
            ->getManagedClassOrNewOne($class_name)
            ->removeMethod($method_name);
    }

}

