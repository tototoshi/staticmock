<?php


namespace StaticMock\Recorder;


class Arguments {

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
        return $this->args[$hash];
    }

    public function clear($hash)
    {
        unset($this->args[$hash]);
    }

}