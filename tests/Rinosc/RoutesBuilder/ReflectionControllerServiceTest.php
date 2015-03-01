<?php
namespace RinoscTests\RoutesBuilder;

use Rinosc\RoutesBuilder\ReflectionControllerService;

class ReflectionControllerServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMethodsWithEmptyClass()
    {
        $service = new ReflectionControllerService('RinoscTests\RoutesBuilder\Fixtures\ReflectionControllerService\EmptyClass');
        $result = $service->getMethods();

        $this->assertEquals(array(), $result);
    }

    public function testGetMethodsWithCompleteClass()
    {
        $service = new ReflectionControllerService('RinoscTests\RoutesBuilder\Fixtures\ReflectionControllerService\CompleteClass');
        $result = $service->getMethods();

        $expected = array('get', 'post', 'delete', 'patch', 'put');
        $this->assertEquals($expected, $result);
    }

    public function testGetMethodsWithScopedMethods()
    {
        $service = new ReflectionControllerService('RinoscTests\RoutesBuilder\Fixtures\ReflectionControllerService\ScopedMethodClass');
        $result = $service->getMethods();

        $expected = array('delete');
        $this->assertEquals($expected, $result);
    }
}
