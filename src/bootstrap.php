<?php

use Pimple\Container;

use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;

$container = new Container();

$container['config'] = array(

    "lime" => array(
        "address" => "http://serwer-wiosny.home.pl/ankiety02/index.php/survey/index/", //adres do ankiet LimeSurvey
        "sid"     => "635363", //id ankiety
        "operator_question_id" => "635363X1409X20325", //id pytania o konsultanta
        "attempt_question_id"  => "635363X1409X20320" //id pytania o próbę dotarcia
    ),

    "db" => array(
        'server'   => 'sql.wiosna.org.pl',
        'username' => 'serwer_wiosny_06',
        'password' => 'Xn4F9YlG5dt9',
        'database' => 'serwer_wiosny_06',
    ),

    "view" => array(
        "path" => dirname(__FILE__) . '/views'
    ),

    "app" => array(
        'debug' => true
    ),

    'cache' => array(
        'dir' => '/tmp',
        'ttl' => 3600
    )
);


$container['log'] = function ($c) {
    // create a log channel
    $log = new Logger('main');
    $log->pushHandler(new BrowserConsoleHandler(Logger::DEBUG));

    return $log;
};

$container['view'] = function ($c) {
    $loader = new Twig_Loader_Filesystem($c["config"]["view"]["path"]);
    return new Twig_Environment($loader);
};

$container['cache'] = function ($c) {
    $adapter = new File($c['config']['cache']['dir']);
    $adapter->setOption('ttl', $c['config']['cache']['ttl']);
    return new Cache($adapter);
};

$container['db'] = function ($c) {
    $config = $c['config']['db'];
    $connection = new mysqli(
        $config['server'], $config['username'],
        $config['password'], $config['database']
    ) or die('mysqli_connect_error()');
    $connection->set_charset("utf8");

    return $connection;
};

$container['app'] = function($c) {
    $app = new \Slim\Slim();
    $app->config($c['config']['app']);
    return $app;
};

$container['contact_model'] = function($c) {
    return new \Dreamcc\Model\Contact($c['db'], $c['log']);
};

$container['user_model'] = function($c) {
    return new \Dreamcc\Model\User($c['db'], $c['log'], $c['cache']);
};

$container['lime_model'] = function($c) {
    return new \Dreamcc\Model\Lime($c['config'], $c['log']);
};

$container['main_controller'] = function($c) {
    return new \Dreamcc\Controller\Main($c['app'], $c['view'], $c['log'], $c['contact_model'], $c['user_model']);
};
