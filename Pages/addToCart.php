<?php

require_once("Models/Database.php");
require_once("Models/Cart.php");

$dbContext = new Database();

$productId = $_GET['productId'] ?? "";
$fromPage = $_GET['fromPage'] ?? "";

$userId = null;
$session_id = null;

if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){
    $userId = $dbContext->getUsersDatabase()->getAuth()->getUserId();
}
    //$cart = $dbContext->getCartByUser($userId);
$session_id = session_id();

$cart = new Cart($dbContext, $session_id, $userId);
$cart->addItem($productId, 1);

header("Location: $fromPage");
?>