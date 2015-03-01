<?php
namespace Rinosc;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ControllerProvider implements ServiceProviderInterface
{
    private static $validMethods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
    private $routes = array();
    private $availableRoutes = array();

    private $controllers;

    public function register(Application $app)
    {
        $this->routes = $app['routes_builder.controller_finder']->getControllers();
        $this->controllers = $app['controllers_factory'];

        $app['rinosc.controllerProvider'] = $this;
    }

    public function boot(Application $app)
    {

    }

    public function buildRoutes()
    {
        $availableRoutes = $this->getAvailableRoutes();

        foreach ($availableRoutes as $name => $route) {
            $method = strtolower($route['method']);
            $path = strtolower($route['path']);
            $normalizedName = strtolower($name);
            $action = $route['controller'] . '::' . $method . 'Action';

            $this->controllers->match($path, $action)
                ->method($method)
                ->bind($normalizedName);
        }
        return $this->controllers;
    }

    public function getAvailableRoutes()
    {
        if (! empty($this->availableRoutes)) {
            return $this->availableRoutes;
        }

        $availableRoutes = array();

        foreach ($this->routes as $name => $route) {
            if (! in_array(strtoupper($route['method']), self::$validMethods)) {
                continue;
            }

            $availableRoutes[$name] = $route;
        }
        $this->availableRoutes = $availableRoutes;
        return $availableRoutes;
    }
}
