<?php

use Pimple\Container;

use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;

$container = new Container();

$container['config'] = array(

    //production address

     "lime" => array(
         "address" => "http://serwer-wiosny.home.pl/ankiety02/index.php/survey/index/" //adres do ankiet LimeSurvey
     ),

    //dev address
    /*"lime" => array(
        "address" => "http://ankiety-dev.wiosna.org.pl/ankiety-dev/index.php/survey/index/" //adres do ankiet LimeSurvey
    ),*/

    //local address
//    "lime" => array(
//        "address" => "http://localhost/limesurvey/index.php/survey/index/" //adres do ankiet LimeSurvey
//    ),

    
    // production db
    "db" => array(
        'server'   => 'sql.wiosna.org.pl',
        'username' => 'serwer_wiosny_06',
        'password' => 'Xn4F9YlG5dt9',
        'database' => 'serwer_wiosny_06',
    ),

    // dev db
    // "db" => array(
    //     'server'   => 'sql.wiosna.org.pl',
    //     'username' => '04827858_limedev',
    //     'password' => 'a&F6@EE,b%cB',
    //     'database' => '04827858_limedev',
    // ),

    //local db
//    "db" => array(
//        'server'   => 'localhost',
//        'username' => 'root',
//        'password' => '',
//        'database' => 'limesurvey',
//    ),

    
    //konfiguracja projektu 
    "project_config" => array(
               "suffix" => "liderzy4", //suffix który ma być dodany do widoków w bazie dotyczących tego projektu
                 "defined_user" => true //czy konsultanci są przypisani do konkretnych projektów?
    ),
    

    
    "view" => array(
        "path" => dirname(__FILE__) . '/views',
        "options" => array(
            "debug" => true,
        )
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
    $twig   = new Twig_Environment($loader, $c["config"]["view"]["options"]);

    $twig->addExtension(new \Slim\Views\TwigExtension());
    $twig->addExtension(new Twig_Extension_Debug());

    $twig->addExtension(new \Dreamcc\Lib\TwigContactExtension($c['config']));

    return $twig;
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
    return new \Dreamcc\Model\Contact($c['db'], $c['log'], $c['cache'], $c['config']);
};

$container['user_model'] = function($c) {
    return new \Dreamcc\Model\User($c['db'], $c['log'], $c['cache'], $c['config']);
};

$container['dbconf_model'] = function($c) {
    return new \Dreamcc\Model\DbConf($c['db'], $c['log'], $c['cache'], $c['config']);
};

$container['bi_model'] = function($c) {
    return new \Dreamcc\Model\BI($c['db'], $c['log'], $c['cache'], $c['config']);
};

$container['lime_model'] = function($c) {
    return new \Dreamcc\Model\Lime($c['config'], $c['log']);
};

$container['main_controller'] = function($c) {
    return new \Dreamcc\Controller\Main(
        $c['app'],
        $c['view'],
        $c['log'],
        $c['contact_model'],
        $c['user_model'],
        $c['dbconf_model'],
        $c['bi_model']
    );
};
