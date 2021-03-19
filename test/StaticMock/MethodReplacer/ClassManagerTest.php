<?php
namespace StaticMock\MethodReplacer;

class ClassManagerTest extends \PHPUnit\Framework\TestCase
{

    public function testMock()
    {
        $manager = ClassManager::getInstance();
        $manager->register('StaticMock\MethodReplacer\A', 'a', function() {
            return 3;
        });
        $manager->register('StaticMock\MethodReplacer\B', 'b', function() {
            return 4;
        });

        $this->assertEquals(3, A::a());
        $this->assertEquals(4, B::b());

        $manager->deregister('StaticMock\MethodReplacer\A', 'a');
        $manager->deregister('StaticMock\MethodReplacer\B', 'b');

        $this->assertEquals(1, A::a());
        $this->assertEquals(2, B::b());

    }


}
