<?php
namespace Rinosc\RoutesBuilder;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;

class Provider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['routes_builder.classes_finder'] = $app->share(function ($app)
            {
                return new ClassFinder(
                    $app['routes_builder.root'],
                    $app['routes_builder.root_namespace'],
                    new Finder()
                );
            }
        );

        $app['routes_builder.controller_finder'] = $app->share(function ($app)
            {
                return new ControllerFinder(
                    $app['routes_builder.classes_finder']->getClasses(),
                    $app['routes_builder.root_namespace']
                );
            }
        );
    }

    public function boot(Application $app)
    {

    }
}
