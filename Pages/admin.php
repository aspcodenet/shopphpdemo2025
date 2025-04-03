<?php
require_once("Models/Product.php");
require_once("components/Footer.php");
require_once("Models/Database.php");

$dbContext = new Database();

// admin.php?sortCol=price&sortOrder=asc
// admin.php?sortCol=price&sortOrder=desc
// admin.php?sortCol=stockLevel&sortOrder=asc

$sortCol = $_GET['sortCol'] ?? "";

// $sortCol = $_GET['sortCol'];
// if($sortCol == null){
//     $sortCol = "";
// }
$sortOrder = $_GET['sortOrder'] ?? "";

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
                                <li><a class="dropdown-item" href="#!">All Products</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                    <li><a class="dropdown-item" href="#!">En cat</a></li>
                            </ul> 
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#!">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="#!">Create account</a></li>
                    </ul>
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
        <!-- Section-->
        <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <a href="/admin/new" class="btn btn-primary">Create new</a>
            <table class="table">
                <thead>
                        <th>Name
                            <a href="/admin/products?sortCol=title&sortOrder=asc"><i class="bi bi-arrow-down-circle-fill"></i></a>
                            <a href="/admin/products?sortCol=title&sortOrder=desc"><i class="bi bi-arrow-up-circle-fill"></i></a>
                        </th>
                        <th>Category
                        <a href="/admin/products?sortCol=categoryName&sortOrder=asc"><i class="bi bi-arrow-down-circle-fill"></i></a>
                            <a href="/admin/products?sortCol=categoryName&sortOrder=desc"><i class="bi bi-arrow-up-circle-fill"></i></a>  
                        </th>
                        <th>Price
                        <a href="/admin/products?sortCol=price&sortOrder=asc"><i class="bi bi-arrow-down-circle-fill"></i></a>
                            <a href="/admin/products?sortCol=price&sortOrder=desc">
                                <i class="bi bi-arrow-up-circle-fill"></i>
                            </a>
                        </th>
                        <th>Stock level
                        <a href="/admin/products?sortCol=stockLevel&sortOrder=asc"><i class="bi bi-arrow-down-circle-fill"></i></a>
                            <a href="/admin/products?sortCol=stockLevel&sortOrder=desc">
                                <i class="bi bi-arrow-up-circle-fill"></i>
                            </a>
                        </th>
                        <th>action</th>
                </thead>

                <tbody>
                <?php foreach($dbContext->getAllProducts($sortCol, $sortOrder) as $prod){ ?>
                    <tr>
                        <td><?php echo $prod->title; ?></td>
                        <td><?php echo $prod->categoryName; ?></td>
                        <td><?php echo $prod->price; ?></td>
                        <td><?php echo $prod->stockLevel; ?></td>
                        <td>
                            <a href="edit?id=<?php echo $prod->id; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete?id=<?php echo $prod->id; ?>" class="btn btn-danger">Delete</a>
                        
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
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

