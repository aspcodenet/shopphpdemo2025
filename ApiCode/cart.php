<?php

require_once("Models/Database.php");
require_once("Models/Cart.php");

$dbContext = new Database();

$productId = $_GET['productId'] ?? "";

$userId = null;
$session_id = null;

if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){
    $userId = $dbContext->getUsersDatabase()->getAuth()->getUserId();
}
$session_id = session_id();

$cart = new Cart($dbContext, $session_id, $userId);

$cart->addItem($productId, 1);



$jsonData =  json_encode([
    "status" => "success",
    "message" => "Product added to cart",
    "cart" => $cart->getItems(),
    "cartCount" => $cart->getItemsCount(),
    "bestTeam" => "Modo Hockey",
    "cartTotal" => $cart->getTotalPrice(),
]);


echo $jsonData




?>