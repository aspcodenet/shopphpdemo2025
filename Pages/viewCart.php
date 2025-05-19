<?php
// ONCE = en gång även om det blir cirkelreferenser
#include_once("Models/Products.php") - OK även om filen inte finns




require_once("Models/Product.php");
require_once("components/Footer.php");
require_once("Models/Database.php");
require_once("Models/Cart.php");
require_once("components/SingleProduct.php");

$dbContext = new Database();

$userId = null;
$session_id = null;

if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){
    $userId = $dbContext->getUsersDatabase()->getAuth()->getUserId();
}
    //$cart = $dbContext->getCartByUser($userId);
$session_id = session_id();

$cart = new Cart($dbContext, $session_id, $userId);




// POPULÄRA PRODUKTER - product 1 to many reviews text+betyg
// Vi gör enkelt : i products skapar vi PopularityFactor som är en int mellan 1-100
// ju högre ju mer populär

// På startsidan så visas de 10 mest populära produkterna
// Skapa en  getPopularProducts() i Database.php som returnerar en array av produkter
// select * from products order by popularityFactor desc limit 10	

?>

<!DOCTYPE html>
<html lang="en">
    <head>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-5NXP0GE5CV"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-5NXP0GE5CV',{ 'debug_mode':true });
</script>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Shop Homepage - Start Bootstrap Template</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="/css/styles.css" rel="stylesheet" />
    </head>
    <body>


    <?php 
    
    $googleItems = [];
    foreach($cart->getItems() as $cartitem){
        array_push($googleItems, [
            
            "quantity" => $cartitem->quantity,
            "price" =>$cartitem->price,
            "item_id"=>$cartitem->id,
            "item_name"=>$cartitem->productName,
        ]);
    }
    
    ?>

<script>
gtag("event", "view_cart", {
  currency: "SEK",
  value: <?php echo $cart->getTotalPrice(); ?>,
  items: [
    <?php echo json_encode($googleItems); ?>
  ]
});



function onCheckout(){
    gtag("event", "begin_checkout", {
    currency: "SEK",
    value: <?php echo $cart->getTotalPrice(); ?>,
    items: [
        <?php echo json_encode($googleItems); ?>
    ]
    });

}

</script>    




        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="/">SuperShoppen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kategorier</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/category">All Products</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                    <?php
                                    foreach($dbContext->getAllCategories() as $category){
                                        echo "<li><a class='dropdown-item' href='/category?id=$category->id'>$category->name</a></li>";
                                    } 
                                    ?> 
                            </ul> 
                        </li>
                        <?php
                        if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){ ?>
                            <li class="nav-item"><a class="nav-link" href="/user/logout">Logout</a></li>
                        <?php }else{ ?>
                            <li class="nav-item"><a class="nav-link" href="/user/login">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="/user/register">Create account</a></li>
                        <?php 
                        }
                        ?>
                    </ul>

                     <form action="/search" method="GET">
                        <input type="text" name="q" placeholder="Search" class="form-control">
                     </form>   


                    <?php if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){ ?>
                        Current user: <?php echo $dbContext->getUsersDatabase()->getAuth()->getUsername() ?>
                    <?php } ?>
                    <form class="d-flex">
                        <a class="btn btn-outline-dark" href="/viewCart">
                            <i class="bi-cart-fill me-1"></i>
                            Cart
                            <span id="cartCount" class="badge bg-dark text-white ms-1 rounded-pill"><?php echo $cart->getItemsCount() ?></span>
                        </a>
                    </form>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Din cart</h1>
                    <p class="lead fw-normal text-white-50 mb-0">...</p>
                </div>
            </div>
        </header>
        <!-- Section-->
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Price</th>
                                <th scope="col">Row Price</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItemsTable">
                            <?php foreach($cart->getItems() as $item){
                            ?>
                                <tr>
                                    <td>
                                        <?php echo $item->productName; ?>
                                    </td>
                                    <td>
                                        <?php echo $item->quantity; ?>
                                    </td>

                                    <td>
                                        <?php echo $item->productPrice; ?>
                                    </td>

                                    <td>
                                        <?php echo $item->rowPrice; ?>
                                    </td>
                                    <td>
                                        <a href="javascript:addToCart(<?php echo $item->productId;  ?>, true)" class="btn btn-info">PLUS JS</a>

                                    <a href="/addToCart?productId=<?php echo $item->productId ?>&fromPage=<?php echo urlencode((empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" class="btn btn-primary">+</a>                                            
                                    <a href="/removeFromCart?productId=<?php echo $item->productId ?>&fromPage=<?php echo urlencode((empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" class="btn btn-danger">-</a>                                            
                                    <a href="/removeFromCart?removeCount=<?php echo $item->quantity ?>&productId=<?php echo $item->productId ?>&fromPage=<?php echo urlencode((empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" class="btn btn-danger">DELETE ALL</a>                                            
                                    </td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"></td>
                                <td id="totalPrice"><?php echo $cart->getTotalPrice(); ?></td>
                                <td>
                                    <a href="/checkout" onclick="onCheckout()" class="btn btn-success">Checkout</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                </div> 
        </section>




        <!-- Footer-->
         <?php Footer(); ?>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="/scripts/cart.js"></script>




    </body>
</html>
