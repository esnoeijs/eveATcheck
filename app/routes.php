<?php

/****************************
 * Dashboard Page
 ****************************/

// dashboard index
$app->get("/", function () use ($app){
    $controller = new eveATcheck\controller\dashboard();
    $controller->action_index($app);
});

// partialHTML get list of setups
$app->get("/setup/list", function () use ($app) {
    $controller = new eveATcheck\controller\dashboard();
    $controller->action_listAll($app);
});

/****************************
 * Setup Details Page
 ****************************/

// Setup details index
$app->get("/setup/:setup/details", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\details();
    $controller->action_index($app, $setupId);
});

// refresh fit on setup detail screen
$app->get("/setup/:setup/fit/:fit/refresh", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\details();
    $controller->action_fitList($app, $setupId, $fitId);
});

/****************************
 * Setup
 ****************************/

// Get dialog form
$app->get("/setup/addDialog", function () use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_addDialog($app);
});

// Add setup
$app->post("/setup/add", function () use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_add($app);
});

// delete setup
$app->get("/setup/:setup/delete", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_delete($app, $setupId);
});

/****************************
 * Fit
 ****************************/

$app->get("/setup/:setup/fit/addDialog", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_addDialog($app, $setupId);
});

// Add fit
$app->post("/setup/:setup/fit/add", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_add($app, $setupId);
});

// get edit dialog HTML for fit
$app->get("/setup/:setup/fit/:fit/editDialog", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_editDialog($app, $setupId, $fitId);
});

// update fit
$app->post("/setup/:setup/fit/:fit/update", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_update($app, $setupId, $fitId);
});

// delete fit
$app->get("/setup/:setup/fit/:fit/delete", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_delete($app, $setupId, $fitId);
});

/****************************
 * User pages
 ****************************/

// user registration
$app->get("/user/register", function () use ($app) {
    $controller = new eveATcheck\controller\user();
    $controller->action_register($app);
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