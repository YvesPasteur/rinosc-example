<?php
/**
 * @var Silex\Application $app
 */
$app = include __DIR__.'/../src/bootstrap.php';

$app['model.post'] = $app->protect(function ($db) {
    return new Vdm\Model\Post($db);
});
$app['model.postCollection'] = $app->protect(function ($db) {
    return new Vdm\Model\PostCollection($db);
});



$app->register(new Rinosc\RoutesBuilder\Provider(), array(
    'routes_builder.root' => __DIR__ . '/../src/Vdm/Api',
    'routes_builder.root_namespace' => 'Vdm\Api'
));
$app->register(new Rinosc\ControllerProvider());
$app->mount('/api/', $app['rinosc.controllerProvider']->buildRoutes());

$app->run();
