<?php

require 'vendor/autoload.php';

require_once './bootstrap.php';

session_cache_limiter(false);
session_start();

$mainController = $container['main_controller'];
$app            = $container['app'];
$log            = $container['log'];


$mainController->setup();
$app->run();

$log->addDebug("App run");
