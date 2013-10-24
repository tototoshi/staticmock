<?php


namespace StaticMock\Recorder;


use StaticMock\Marker\Singleton;

class Arguments implements Singleton {

    private static $instance;

    private $args = array();

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance) {
            return self::$instance;
        } else {
            self::$instance = new Arguments();
            return self::$instance;
        }
    }

    public function record($hash, $args)
    {
        $this->args[$hash] = $args;
    }

    public function get($hash)
    {
        if (isset($this->args[$hash])) {
            return $this->args[$hash];
        } else {
            return array();
        }
    }

    public function clear($hash)
    {
        unset($this->args[$hash]);
    }

}