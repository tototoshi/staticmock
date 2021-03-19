<?php
namespace StaticMock\Util;

class HasToString
{
    public function __toString()
    {
        return "has __toString";
    }
}

class HasNoToString
{
}

class StringUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testMethodArgsToReadableString()
    {
        $this->assertEquals(
            '(object, 1, a, Array(1, 2, object))',
            StringUtil::methodArgsToReadableString(array(new \DateTime(), 1, 'a', array(1, 2, new \DateTime())))
        );

        $this->assertEquals(
            '(object, 1, a, Array(a => 1, b => 2, c => object))',
            StringUtil::methodArgsToReadableString(array(new \DateTime(), 1, 'a', array('a' => 1, 'b' => 2, 'c' => new \DateTime())))
        );
    }

    public function testArrayToReadableString()
    {
        $this->assertEquals(
            'Array(object, 1, a, Array(1, 2, object))',
            StringUtil::arrayToReadableString(array(new \DateTime(), 1, 'a', array(1, 2, new \DateTime())))
        );

        $this->assertEquals(
            'Array(object, 1, a, Array(a => 1, b => 2, c => object))',
            StringUtil::arrayToReadableString(array(new \DateTime(), 1, 'a', array('a' => 1, 'b' => 2, 'c' => new \DateTime())))
        );
    }

    public function testObjectToReadableString()
    {
        $this->assertEquals('has __toString', StringUtil::objectToReadableString(new HasToString()));
        $this->assertEquals('object', StringUtil::objectToReadableString(new HasNoToString()));
    }

}
