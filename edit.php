<?php
require_once('Models/Product.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    // plocka det som står i URL edit.php?id=80
    $id = $_GET['id'];
    // Hämta den produkt med detta ID
    $product = getProduct($id);
    if($product == null){
        echo "<h1>Produkten finns inte</h1>";
    }else{
        echo "<h1>$product->title</h1>";
        echo "<p>$product->price</p>";
        echo "<p>$product->stockLevel</p>";
        echo "<p>$product->categoryName</p>";
    }

    ?>
</body>
</html>