<?php
require_once('Models/Product.php');
require_once("components/Footer.php");
require_once('Models/Database.php');
require_once("Utils/Validator.php");

$id = $_GET['id'];
$dbContext = new Database();
// Hämta den produkt med detta ID
$product = $dbContext->getProduct($id); // TODO felhantering om inget produkt
$v = new Validator($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Här kommer vi när man har tryckt  på SUBMIT
    // IMORGON TISDAG SÅ UPDATE PRODUCT SET title = $_POST['title'] WHERE id = $id
    $product->title = $_POST['title'];
    $product->stockLevel = $_POST['stockLevel'];

    $product->price = $_POST['price'];
    $product->categoryName = $_POST['categoryName'];
    $product->popularityFactor = $_POST['popularityFactor'];

    // validera
    $v->field('title')->required()->alpha_num([' '])->min_len(3)->max_len(50);
    $v->field('stockLevel')->required()->numeric()->min_val(0);
    $v->field('price')->required()->numeric()->min_val(0);
    $v->field('categoryName')->required()->alpha_num([' '])->min_len(3)->max_len(50);
    $v->field('popularityFactor')->required()->numeric()->min_val(0);

    // om ok så spara i databas
    if($v->is_valid()){
        // OK - spara i databas
         $dbContext->updateProduct($product);
        header("Location: /admin/products");
        exit;
    }
}else{
    // Det är INTE ett formulär som har postats - utan man har klickat in på länk tex edit.php?id=12
}

//Kunna lagra i databas


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
                                    <?php
                                    foreach($dbContext->getAllCategories() as $cat){
                                        echo "<li><a class='dropdown-item' href='#!'>$cat</a></li>";
                                    } 
                                    ?> 
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
    <section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">



    <form method="POST" > 
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control <?php echo $v->get_error_message('title') != "" ? "is-invalid" : ""  ?>" name="title" value="<?php echo $product->title ?>">
            <span class="invalid-feedback"><?php echo $v->get_error_message('title');  ?></span>                        
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" class="form-control <?php echo $v->get_error_message('price') != "" ? "is-invalid" : ""  ?>"" name="price" value="<?php echo $product->price ?>">
            <span class="invalid-feedback"><?php echo $v->get_error_message('price');  ?></span>                        
        </div>
        <div class="form-group">
            <label for="stockLevel">Stock</label>
            <input type="text" class="form-control <?php echo $v->get_error_message('stockLevel') != "" ? "is-invalid" : ""  ?>"" name="stockLevel" value="<?php echo $product->stockLevel ?>">
            <span class="invalid-feedback"><?php echo $v->get_error_message('stockLevel');  ?></span>                        
            </div>
        <div class="form-group">
            <label for="categoryName">Category name:</label>
            <input type="text" class="form-control <?php echo $v->get_error_message('categoryName') != "" ? "is-invalid" : ""  ?>"" name="categoryName" value="<?php echo $product->categoryName ?>">
            <span class="invalid-feedback><?php echo $v->get_error_message('categoryName');  ?></span>                        
            </div>
        <div class="form-group">
            <label for="popularityFactor">Popularity factor</label>
            <input type="number" class="form-control <?php echo $v->get_error_message('popularityFactor') != "" ? "is-invalid" : ""  ?>"" name="popularityFactor" value="<?php echo $product->popularityFactor ?>">
            <span class="invalid-feedback"><?php echo $v->get_error_message('popularityFactor');  ?></span>                        
    
        </div>


        <input type="submit" class="btn btn-primary" value="Uppdatera">
    </form>
</div>
</section>



<?php Footer(); ?>
<!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>

</body>
</html>

<!-- 
<input type="text" name="title" value="<?php echo $product->title ?>">
        <input type="text" name="price" value="<?php echo $product->price ?>">
        <input type="text" name="stockLevel" value="<?php echo $product->stockLevel ?>">
        <input type="text" name="categoryName" value="<?php echo $product->categoryName ?>">
        <input type="submit" value="Uppdatera"> -->
