<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
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


$app->register(
    new Marmelab\Microrest\MicrorestServiceProvider(),
    array(
        'microrest.config_file' => __DIR__ . '/../vdm.raml',
    )
);

// avoid error in MicrorestServiceProdiver... don't seem to be useful in our case
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array('cache' => __DIR__.'/../var/cache/twig'),
));

$app->run();
