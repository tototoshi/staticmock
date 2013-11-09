<?php
namespace StaticMock\Util;

class StringUtilTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayToReadableString()
    {
        $this->assertEquals(
            '(object, 1, a, Array(1, 2, object))',
            StringUtil::arrayToReadableString(array(new \DateTime(), 1, 'a', array(1, 2, new \DateTime())))
        );
    }


}
