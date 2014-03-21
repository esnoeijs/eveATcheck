<?php


// Default route
$app->get("/", function () use ($app){
    $controller = new eveATcheck\controller\dashboard();
    $controller->action_index($app);
});

// Add fit to user session
$app->post("/fit/add", function () use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_add($app);
});

// get fits from user session
$app->get("/fit/list", function () use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_list($app);
});



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