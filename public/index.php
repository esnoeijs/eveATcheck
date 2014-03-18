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

$app->db = new \eveATcheck\lib\database\database($dbhost,$dbport,$dbname,$dbuser,$dbpass);


// Let's just use a normal session for now.
session_start();
//$app->add(new \Slim\Middleware\SessionCookie(array(
//    'expires' => '20 minutes',
//    'path' => '/',
//    'domain' => null,
//    'secure' => false,
//    'httponly' => false,
//    'name' => 'slim_session',
//    'secret' => 'asdjfhasd@#$32423jh4k2j3h4jb kbksdafsjkdhfu 2u3h42;l;lk',
//    'cipher' => MCRYPT_RIJNDAEL_256,
//    'cipher_mode' => MCRYPT_MODE_CBC
//)));



require '../app/routes.php';


$app->run();
