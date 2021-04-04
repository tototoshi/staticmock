<?php
/*
 * Copyright (c) 2013, Toshiyuki Takahashi
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the Toshiyuki Takahashi nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICE;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace StaticMock\MethodReplacer;
use StaticMock\Marker\Singleton;

/**
 * Class ClassManager
 * @package StaticMock\MethodReplacer
 */
class ClassManager implements Singleton {

    private static $instance = null;

    private $managed_classes = array();

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
        if (isset($this->managed_classes[$class_name])) {
            return $this->managed_classes[$class_name];
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
        $this->managed_classes[$class_name] =
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

