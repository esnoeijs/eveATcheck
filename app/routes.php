<?php


// Default route
$app->get("/", function () use ($app){
    $controller = new eveATcheck\controller\dashboard();
    $controller->action_index($app);
});

// Get dialog form
$app->get("/setup/addDialog", function () use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_addDialog($app);
});

// Add setup to user session
$app->post("/setup/add", function () use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_add($app);
});

// get setups from user session
$app->get("/setup/list", function () use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_listAll($app);
});

// Actions for specific setups

// get setups from user session
$app->get("/setup/:setup/refresh", function ($setup) use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_list($app, $setup);
});

// get delete setup from user session
$app->get("/setup/:setup/delete", function ($setup) use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_delete($app, $setup);
});

$app->get("/setup/:setup/fit/addDialog", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_addDialog($app, $setupId);
});

// Add fit to user session
$app->post("/setup/:setup/fit/add", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_add($app, $setupId);
});


// User stuff

// user registration
$app->get("/user/register", function () use ($app) {
    $controller = new eveATcheck\controller\user();
    $controller->action_register($app);
});

// user registration
$app->post("/user/register", function () use ($app) {
    $controller = new eveATcheck\controller\user();
    $controller->action_register($app);
});


// user login
$app->get("/user/login", function () use ($app) {
    $controller = new eveATcheck\controller\user();
    $controller->action_login($app);
});

// user login
$app->post("/user/login", function () use ($app) {
    $controller = new eveATcheck\controller\user();
    $controller->action_login($app);
});

// user logout
$app->get("/user/logout", function () use ($app) {
    $controller = new eveATcheck\controller\user();
    $controller->action_logout($app);
});