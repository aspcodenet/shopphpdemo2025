<?php

require_once("Models/Database.php");
require_once("Models/Cart.php");

$dbContext = new Database();

$productId = $_GET['productId'] ?? "";
$fromPage = $_GET['fromPage'] ?? "";
$removeCount = $_GET['removeCount'] ?? 1;

$userId = null;
$session_id = null;

if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){
    $userId = $dbContext->getUsersDatabase()->getAuth()->getUserId();
}
$session_id = session_id();

$cart = new Cart($dbContext, $session_id, $userId);
$cart->removeItem($productId, $removeCount);

header("Location: $fromPage");
?>