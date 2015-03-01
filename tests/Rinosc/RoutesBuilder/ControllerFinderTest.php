<?php
namespace RinoscTests\RoutesBuilder;

use Rinosc\RoutesBuilder\ControllerFinder;
use RinoscTests\RoutesBuilder\Fixtures\ControllerFinder\InstanceController;

class ControllerFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetControllersWithoutClasses()
    {
        $finder = new ControllerFinder(array(), 'Foo');
        $this->assertEquals(array(), $finder->getControllers());
    }

    public function testGetControllersWithNoValidClasses()
    {
        $classes = array(
            'Foo\Bar',
            'CollectionController',
            'Rinosc\\Api\\FooController'
        );
        $finder = new ControllerFinder($classes, 'Foo');
        $this->assertEquals(array(), $finder->getControllers());
    }

    public function testGetControllersWithInstanceController()
    {
        $classname = 'RinoscTests\\RoutesBuilder\\Fixtures\\ControllerFinder\\InstanceController';

        $finder = new ControllerFinder(array($classname), 'RinoscTests\\RoutesBuilder');
        $expectedControllers = array(
            'fixtures.controllerfinder.instance.get' => array(
                'path'       => 'fixtures/controllerfinder/{id}',
                'controller' => $classname,
                'method'     => 'get'
            )
        );
        $this->assertEquals($expectedControllers, $finder->getControllers());
    }

    public function testGetControllersWithCollectionController()
    {
        $classname = 'RinoscTests\\RoutesBuilder\\Fixtures\\ControllerFinder\\CollectionController';

        $finder = new ControllerFinder(array($classname), 'RinoscTests\\RoutesBuilder');

        $expectedControllers = array();
        foreach (array('get', 'post', 'delete', 'put', 'patch') as $method) {
            $expectedControllers['fixtures.controllerfinder.collection.' . $method] = array(
                    'path'       => 'fixtures/controllerfinder/',
                    'controller' => $classname,
                    'method'     => $method
            );
        }

        $this->assertEquals($expectedControllers, $finder->getControllers());
    }
}
