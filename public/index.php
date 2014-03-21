<?php


/**
 * @todo Add user with login/logout persistant fit storage
 * @todo Add fleet setups which can contain fits
 * @todo Add check for fits and fleet setups and the fits within.
 */

require '../config/config.php';
require '../vendor/autoload.php';
require '../vendor/slim/slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();
require '../vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$twig = new \Slim\Views\Twig();
$twig->parserOptions = $twigOptions;
$twig->parserExtensions = array(new Twig_Extension_Debug());

$app = new \Slim\Slim(
    array_merge(
    array(
        'view' => $twig,
        'templates.path' => '../app/view/'
    ),
    $slimOptions)
);


session_start();


$app->db = new \eveATcheck\lib\database\database($dbhost,$dbport,$dbname,$dbuser,$dbpass);
$app->model = new \eveATcheck\lib\evemodel\evemodel($app->db);
$app->user  = new \eveATcheck\lib\user\user($app->model, new \eveATcheck\lib\user\auth\database($app->model));
$app->evefit = new \eveATcheck\lib\evefit\evefit($app->model, $app->user);


require '../app/routes.php';


$app->run();
