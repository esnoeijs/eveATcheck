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

// Add fit to user session
$app->get("/fit/list", function () use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_list($app);
});