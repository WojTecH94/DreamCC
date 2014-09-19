<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));
$app->config(array(
    'debug' => true,
    'templates.path' => dirname(__FILE__) . '/views'
));

$view = $app->view();

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__) . '/cache'
);

$controller = new \Dreamcc\Controller\Main();

$controller->setup($app);

$app->run();
