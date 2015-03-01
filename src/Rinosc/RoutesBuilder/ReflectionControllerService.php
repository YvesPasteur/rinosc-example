<?php
namespace Rinosc\RoutesBuilder;

use Silex\Application;

class ReflectionControllerService
{
    private $class;

    private static $authorizedMethods = array('get', 'post', 'put', 'delete', 'patch');

    function __construct($class)
    {
        $this->class = $class;
    }

    function getMethods()
    {
        $reflection = new \ReflectionClass($this->class);
        $actions = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $actionRegexp = '/^(' . implode('|', self::$authorizedMethods) . ')Action$/';
        $methods = array();
        foreach ($actions as $action) {
            $actionName = $action->getName();
            if (!preg_match($actionRegexp, $actionName, $matches)) {
                continue;
            }

            list($_, $httpMethod) = $matches;
            $methods[]= $httpMethod;
        }

        return $methods;
    }
}
