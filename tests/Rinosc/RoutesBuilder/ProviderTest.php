<?php
namespace RinoscTests\RoutesBuilder;

use Rinosc\RoutesBuilder\Provider;
use Silex\Application;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterClassesFinder()
    {
        $provider = new Provider();
        $app = new Application();

        $provider->register($app);

        $this->assertArrayHasKey('routes_builder.classes_finder', $app);
        $app['routes_builder.root'] = null;
        $app['routes_builder.root_namespace'] = null;
        $this->assertInstanceOf('Rinosc\RoutesBuilder\ClassFinder', $app['routes_builder.classes_finder']);
    }

    public function testRegisterControllerFinder()
    {
        $provider = new Provider();
        $app = new Application();

        $provider->register($app);

        $this->assertArrayHasKey('routes_builder.classes_finder', $app);

        $classesFinderMock = $this->getMockBuilder('Rinosc\RoutesBuilder\ClassFinder')
            ->disableOriginalConstructor()
            ->getMock();
        $app['routes_builder.classes_finder'] = $classesFinderMock;
        $app['routes_builder.root_namespace'] = null;
        $this->assertInstanceOf('Rinosc\RoutesBuilder\ControllerFinder', $app['routes_builder.controller_finder']);
    }
}
