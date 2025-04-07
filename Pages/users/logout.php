<?php

require 'vendor/autoload.php';
require_once('Models/Database.php');
require_once('Models/UserDatabase.php');


$dbContext = new Database();
$usersDatabase = new UserDatabase($dbContext->pdo);


$usersDatabase->getAuth()->logOut();
header('Location: /');
?>