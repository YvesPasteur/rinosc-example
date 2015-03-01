<?php
namespace Rinosc\RoutesBuilder;

class ControllerFinder
{
    private $classes;

    private $classRegexp;

    public function __construct($classes, $namespaceRoot)
    {
        $this->classes = $classes;
        $namespaceRoot = preg_quote($namespaceRoot . '\\');
        $this->classRegexp = '/^' . $namespaceRoot . '(.*\\\\)(Collection|Instance)Controller$/';
    }

    public function getControllers()
    {
        $controllers = array();
        foreach ($this->classes as $classname) {
            $classControllers = $this->getControllersFromClass($classname);
            $controllers = array_merge($controllers, $classControllers);
        }

        return $controllers;
    }

    /**
     * @param $classname
     * @return array
     */
    private function getControllersFromClass($classname)
    {
        if (! preg_match($this->classRegexp, $classname, $matches)) {
            return array();
        }

        list(, $levels, $type) = $matches;
        $path = $this->getControllerPath($levels, $type);

        $namePartsPrefix = explode('\\', $levels);
        array_pop($namePartsPrefix);
        $namePartsPrefix[] = $type;
        $classControllers = array();
        $methods = $this->getMethodsFromClassname($classname);

        foreach ($methods as $method) {
            $name = $this->getControllerName($namePartsPrefix, $method);
            $classControllers[$name] = array(
                'path'       => $path,
                'controller' => $classname,
                'method'     => $method
            );
        }

        return $classControllers;
    }

    /**
     * @param $classname
     * @return array
     */
    private function getMethodsFromClassname($classname)
    {
        $refl = new ReflectionControllerService($classname);
        $methods = $refl->getMethods();

        return $methods;
    }

    /**
     * @param $namePartsPrefix
     * @param $method
     * @return string
     */
    private function getControllerName($namePartsPrefix, $method)
    {
        $nameParts = $namePartsPrefix;
        $nameParts[] = $method;
        $name = strtolower(implode('.', $nameParts));

        return $name;
    }

    /**
     * @param $levels
     * @param $type
     * @return string
     */
    private function getControllerPath($levels, $type)
    {
        $path = strtolower(str_replace('\\', '/', $levels));
        if ($type == 'Instance') {
            $path .= '{id}';

            return $path;
        }

        return $path;
    }
}
