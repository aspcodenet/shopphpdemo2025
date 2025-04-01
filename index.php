<?php
// Denna fil kommer alltid att laddas in först
// vi ska mappa urler mot Pages
// om url = "/admin" så visa admin.php
// om url = "/edit" så visa edit.php
// om url = "/" så visa index.php


$router = new Router();
$router->addRoute('/', function () {
    require __DIR__ .'/Pages/index.php';
});
$router->addRoute('/category', function () {
    require __DIR__ .'/Pages/category.php';
});
$router->addRoute('/admin/admin', function () {
    require __DIR__ .'/Pages/admin.php';
});
$router->addRoute('/admin/edit', function () {
    require __DIR__ .'/Pages/edit.php';
});

$router->dispatch();
?>


?>