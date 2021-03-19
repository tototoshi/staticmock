<?php
namespace StaticMock\Util;

class ArrayUtilTest extends \PHPUnit\Framework\TestCase
{

    public function testIsAssoc()
    {
        $this->assertTrue(ArrayUtil::isAssoc(array('a' => 1, 'b' => 2)));
        $this->assertFalse(ArrayUtil::isAssoc(array('a', 'b', 'c')));
    }

}
