<?php
// ONCE = en gång även om det blir cirkelreferenser
#include_once("Models/Products.php") - OK även om filen inte finns
require_once("Models/Product.php");
require_once("components/Footer.php");
require_once("Models/Database.php");
require_once("components/SingleProduct.php");
require_once("Utils/SearchEngine.php");
require_once("Utils/UrlModifier.php");

$dbContext = new Database();

$q = $_GET['q'] ?? "";
$sortCol = $_GET['sortCol'] ?? "title";
$sortOrder = $_GET['sortOrder'] ?? "asc";
$pageNo = $_GET['pageNo'] ?? "1";

$pageSize = $_GET['pageSize'] ?? "10";

$searchEngine = new SearchEngine();

$facets = [];
if(isset($_GET["Category"])){
    $facets[] = ["categoryName", explode(",",$_GET["Category"])];   
}

if(isset($_GET["Color"])){
    $facets[] = ["color", explode(",",$_GET["Color"])];   
}


$result = $searchEngine->search($q,$sortCol, $sortOrder, $pageNo, $pageSize,$facets); // $result är en array med två element: $data och $num_pages

$currentUrl = $_SERVER['REQUEST_URI'];
//$result = $dbContext->searchProducts($q,$sortCol, $sortOrder, $pageNo, $pageSize); // $result är en array med två element: $data och $num_pages
// $result["data"] = arrayen med produkter
// $result["num_pages"] = antalet sidor i databasen
?>

<!DOCTYPE html>
<html lang="en">
    <head>
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
                                    foreach($dbContext->getAllCategories() as $cat){
                                        echo "<li><a class='dropdown-item' href='/category?id=$cat->id'>$cat->name</a></li>";
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
                        <input type="text" name="q" value="<?php echo $q; ?>" placeholder="Search" class="form-control">
                     </form>   


                    <?php if($dbContext->getUsersDatabase()->getAuth()->isLoggedIn()){ ?>
                        Current user: <?php echo $dbContext->getUsersDatabase()->getAuth()->getUsername() ?>
                        Current user: <?php echo $dbContext->getUsersDatabase()->getAuth()->getUsername() ?>
                    <?php } ?>
                    <form class="d-flex">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi-cart-fill me-1"></i>
                            Cart
                            <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Super shoppen</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Handla massa onödigt hos oss!</p>
                </div>
            </div>
        </header>
        <!-- Section-->
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5 d-flex gap-3">
                <div>                
                    <div class="text-center mb-4">
                        <a class="btn btn-secondary" href="<?php echo UrlModifier::changeParameters($currentUrl,["sortCol"=>"title","sortOrder"=>"asc"]) ?>">Title asc</a>
                        <a class="btn btn-secondary" href="<?php echo UrlModifier::changeParameters($currentUrl,["sortCol"=>"title","sortOrder"=>"desc"]) ?>">Title desc</a>
                        <a class="btn btn-secondary" href="<?php echo UrlModifier::changeParameters($currentUrl,["sortCol"=>"price","sortOrder"=>"asc"]) ?>">Price asc</a>
                        <a class="btn btn-secondary" href="<?php echo UrlModifier::changeParameters($currentUrl,["sortCol"=>"price","sortOrder"=>"desc"]) ?>">Price desc</a>
                    </div>

                    <div class="text-center mb-4">
                            <h3>Categories</h3>
                            <p>
                                <?php foreach( $result["aggregations_categoryName"] as $bucket ) { ?>
                                    <div>
                                    <a href="<?php echo UrlModifier::addParameters($currentUrl,["Category"=>$bucket["key"]]) ?>">
                                        <?php echo $bucket["key"]; ?> (<?php echo $bucket["doc_count"]; ?>)
                                    </a>
                                    </div>
                                <?php }?>
                            </p>
                            

                            
                    </div>

                    <div class="text-center mb-4">
                            <h3>Color</h3>
                            <p>
                                <?php  echo var_dump($result["aggregations_color"]); ?>
                                <?php foreach( $result["aggregations_color"] as $bucket ) { ?>
                                    <div>
                                    <a href="<?php echo UrlModifier::addParameters($currentUrl,["Color"=>$bucket["key"]]) ?>">
                                        <?php echo $bucket["key"]; ?> (<?php echo $bucket["doc_count"]; ?>)
                                    </a>
                                    </div>
                                <?php }?>
                            </p>
                            

                            
                    </div>



                </div>

                <div>                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                
                <?php 
                    foreach($result["data"] as $prod){
                        SingleProduct($prod);
                     } ?>  
                          
                </div>
            </div>
            </div>
 
            <div class="container px-4 px-lg-5 mt-5">



                <nav >
                    <ul class="pagination justify-content-center">
                        <?php for($i=1; $i <= $result["num_pages"]; $i++ ) {
                            if($i == $pageNo){
                                echo " <li class='page-item active'><span class='page-link'>$i</span></li>";
                            } else {
                                echo "<li class='page-item'><a class='page-link' href='?q=$q&pageNo=$i&sortCol=$sortCol&sortOrder=$sortOrder'>$i</a></li>";
                            }
                        } ?>
                        <!-- <li class="page-item active">
                        <span class="page-link">
                            2
                        </span>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li> -->
                    </ul>
            </nav>                

            </div> 
        </section>




        <!-- Footer-->
         <?php Footer(); ?>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>




    </body>
</html>
