#!/usr/bin/php
<?php
if (!$loader = include __DIR__.'/../vendor/autoload.php') {
    die('You must set up the project dependencies.');
}
$app = new \Cilex\Application('Cilex');

$app['scrap.client'] = new \Goutte\Client();
$app->command(new \VdmScraping\Command());
$app->run();
