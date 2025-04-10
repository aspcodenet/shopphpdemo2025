<?php
// Denna fil kommer alltid att laddas in först
// vi ska mappa urler mot Pages
// om url = "/admin" så visa admin.php
// om url = "/edit" så visa edit.php
// om url = "/" så visa index.php

require_once("Utils/router.php"); // LADDAR IN ROUTER KLASSEN
require_once("vendor/autoload.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
//  :: en STATIC funktion
$dotenv = Dotenv\Dotenv::createImmutable("."); // . is  current folder for the PAGE
$dotenv->load();
// Pilar istf .
// \ istf .

// import * as dotenv from 'dotenv';



$router = new Router();
$router->addRoute('/', function () {
    require_once( __DIR__ .'/Pages/index.php');
});
$router->addRoute('/category', function () {
    require_once( __DIR__ .'/Pages/category.php');
});
$router->addRoute('/admin/products', function () {
    require_once( __DIR__ .'/Pages/admin.php' );
});
$router->addRoute('/admin/edit', function () {
    require_once( __DIR__ .'/Pages/edit.php');
});
$router->addRoute('/admin/new', function () {
    require_once( __DIR__ .'/Pages/new.php');
});
$router->addRoute('/admin/delete', function () {
    require_once( __DIR__ .'/Pages/delete.php');
});

$router->addRoute('/user/login', function () {
    require_once( __DIR__ .'/Pages/users/login.php');
});
$router->addRoute('/user/logout', function () {
    require_once( __DIR__ .'/Pages/users/logout.php');
});

$router->addRoute('/user/register', function () {
    require_once( __DIR__ .'/Pages/users/register.php');
});

$router->addRoute('/user/registerThanks', function () {
    require_once( __DIR__ .'/Pages/users/registerThanks.php');
});

$router->addRoute('/search', function () {
    require_once( __DIR__ .'/Pages/search.php');
});

$router->dispatch();
?>


