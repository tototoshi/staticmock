<?php

namespace StaticMock;


use StaticMock\Marker\Singleton;

class MockObserver implements Singleton {

    private static $instance;

    /*
     * This class is a singleton.
     */
    private function __construct()
    {
    }

    /**
     * Get the instance of this class
     *
     * @return MockObserver
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $instance = new MockObserver();
            self::$instance = $instance;
            return $instance;
        } else {
            return self::$instance;
        }
    }

    public function

} 