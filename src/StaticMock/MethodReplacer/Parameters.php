<?php


namespace StaticMock\MethodReplacer;


class Parameters
{
    /**
     * @var \StaticMock\MethodReplacer\Parameter[]
     */
    private $parameters;

    /**
     * Parameters constructor.
     *
     * @param \StaticMock\MethodReplacer\Parameter[] $parameters
     */
    public function __construct(array $parameters)
    {
        // Drop parameters that are not passed by reference at the end
        for (;;) {
            $c = count($parameters);
            if ($c === 0 || $parameters[$c - 1]->isPassedByReference()) {
                break;
            }
            array_pop($parameters);
        }

        $this->parameters = $parameters;
    }

    /**
     * Return the string to be used for the argument part of the function call.
     *
     * @return string
     */
    public function getArgString(): string
    {
        $strings = array_map(
            function ($p) { return $p->getArgString(); },
            $this->parameters
        );

        if ($this->isRequiredRestParameter()) {
            array_push($strings, $this->getRestParameter());
        }

        return implode(',', $strings);
    }

    /**
     * Returns the string to be used for the parameter part of the function definition.
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getParamString(): string
    {
        $strings = array_map(
            function ($p) { return $p->getParamString(); },
            $this->parameters
        );

        if ($this->isRequiredRestParameter()) {
            array_push($strings, $this->getRestParameter());
        }

        return implode(',', $strings);
    }

    private function isRequiredRestParameter(): bool
    {
        $count = count($this->parameters);
        return $count === 0 || !$this->parameters[$count - 1]->isVariadic();
    }

    private function getRestParameter(): string
    {
        $names = array_map(
            function ($p) { return $p->getName(); },
            $this->parameters
        );

        for ($i = 0; ; $i++) {
            $rest_name = 'rest' . $i;
            if (!in_array($rest_name, $names)) {
                return '...$' . $rest_name;
            }
        }
    }

    /**
     * @param $func
     * @return static
     * @throws \ReflectionException
     */
    public static function make($func): self
    {
        $parameters = array_map(
            function ($rp) { return new Parameter($rp); },
            (new \ReflectionFunction($func))->getParameters()
        );

        return new self($parameters);
    }
}
