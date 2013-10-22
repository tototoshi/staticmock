<?php


namespace StaticMock\Exception;


class AssertionFailedException extends \RuntimeException {

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

}