<?php
namespace StaticMock\Recorder;


class Counter {

    private static $instance;

    private $count = array();

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance) {
            return self::$instance;
        } else {
            self::$instance = new Counter();
            return self::$instance;
        }
    }

    public function increment($id)
    {
        if (isset($this->count[$id])) {
            $this->count[$id]++;
        } else {
            $this->count[$id] = 1;
        }
    }

    public function get($id)
    {
        return $this->count[$id];
    }

    public function clear($id)
    {
        unset($this->count[$id]);
    }

}