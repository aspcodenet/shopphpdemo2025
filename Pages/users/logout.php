<?php

require 'vendor/autoload.php';
require_once('Models/Database.php');
require_once('Models/UserDatabase.php');


$dbContext = new Database();

$dbContext->getUsersDatabase()->getAuth()->logOut();
header('Location: /');
?>