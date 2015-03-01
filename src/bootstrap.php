<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(
    new \Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'dbname'   => 'vdm',
            'user'     => 'api',
            'password' => 'foobar',
            'host'     => 'localhost',
            'driver'   => 'pdo_mysql'
        )
    )
);

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
return $app;
