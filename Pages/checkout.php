<?php
require_once("vendor/autoload.php");

require_once("Models/Product.php");
require_once("Models/Database.php");
require_once("Models/Cart.php");

$dbContext = new Database();



$userId = null;
$session_id = null;

if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){
    $userId = $dbContext->getUsersDatabase()->getAuth()->getUserId();
}
    //$cart = $dbContext->getCartByUser($userId);
$session_id = session_id();

$cart = new Cart($dbContext, $session_id, $userId);


\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET']);

// Skapa en array med s.k LineItems som innehåller produkterna i kundvagnen
$lineitems = [];
foreach($cart->getItems() as $cartitem ){
    array_push($lineitems, [
        "quantity" => $cartitem->quantity,
        "price_data" => [
            "currency" => "sek",
            "unit_amount" => $cartitem->productPrice*100,
            "product_data" => [
                "name" => $cartitem->productName
            ]
        ]

    ]);
}

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost:8000/checkoutsuccess",
    "cancel_url" => "http://localhost:8000",
    "locale" => "auto",
    "line_items" => $lineitems
]);

http_response_code(303);
header("Location: " . $checkout_session->url);

