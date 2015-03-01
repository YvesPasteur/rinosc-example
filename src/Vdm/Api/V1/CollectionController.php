<?php
namespace Vdm\Api\V1;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class CollectionController
{
    public function getAction(Application $app, Request $request)
    {
        $availableRoutes = $app['rinosc.controllerProvider']->getAvailableRoutes();

        $keysToKeep = array('path', 'method');

        $routesInfo = array();
        foreach ($availableRoutes as $route) {
            $info = array();
            foreach ($keysToKeep as $key) {
                $info[$key] = $route[$key];
            }
            $routesInfo[] = $info;
        }

        return $app->json($routesInfo, Response::HTTP_OK);
    }
}
