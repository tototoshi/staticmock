<?php
namespace StaticMock\Util;

class StringUtilTest extends \PHPUnit_Framework_TestCase
{

    public function testMkString()
    {
        $this->assertEquals(
            '(object, 1, a)',
            StringUtil::mkString(array(new \DateTime(), 1, 'a'))
        );
    }


}
