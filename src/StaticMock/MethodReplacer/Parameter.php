<?php

namespace StaticMock\MethodReplacer;

class Parameter
{
    /**
     * @var \ReflectionParameter
     */
    private $parameter;

    public function __construct(\ReflectionParameter $parameter)
    {
        $this->parameter = $parameter;
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function getVar(): string
    {
        return '$' . $this->getName();
    }

    /**
     * @return string
     */
    public function getArgString(): string
    {
        $s = $this->getVar();

        if ($this->isVariadic()) {
            $s = '...' . $s;
        }
        return $s;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getParamString(): string
    {
        $s = $this->getArgString();

        if ($this->parameter->isPassedByReference()) {
            $s = '&' . $s;
        }

        if ($this->parameter->isDefaultValueAvailable()) {
            $s .= '=' . var_export($this->parameter->getDefaultValue(), true);
        }

        return $s;
    }

    public function isVariadic(): bool
    {
        return $this->parameter->isVariadic();
    }

    public function isPassedByReference(): bool
    {
        return $this->parameter->isPassedByReference();
    }
}
