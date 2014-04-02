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

$app->get("/setup/:setup/details", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\details();
    $controller->action_index($app, $setupId);
});

// get setups from user session
$app->get("/setup/:setup/details/refresh", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_detailsList($app, $setupId);
});

// get setups from user session
$app->get("/setup/:setup/refresh", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_list($app, $setupId);
});

// delete setup from user session
$app->get("/setup/:setup/delete", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\setup();
    $controller->action_delete($app, $setupId);
});

$app->get("/setup/:setup/fit/addDialog", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_addDialog($app, $setupId);
});

// refresh fit on setup detail screen
$app->get("/setup/:setup/fit/:fit/refresh", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_list($app, $setupId, $fitId);
});

// delete fit from user session
$app->get("/setup/:setup/fit/:fit/delete", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_delete($app, $setupId, $fitId);
});

// get edit dialog HTML for fit
$app->get("/setup/:setup/fit/:fit/editDialog", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_editDialog($app, $setupId, $fitId);
});

// Add fit to user session
$app->post("/setup/:setup/fit/add", function ($setupId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_add($app, $setupId);
});

// Add fit to user session
$app->post("/setup/:setup/fit/:fit/update", function ($setupId, $fitId) use ($app) {
    $controller = new eveATcheck\controller\fit();
    $controller->action_update($app, $setupId, $fitId);
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