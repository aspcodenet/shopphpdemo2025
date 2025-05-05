<?php

class Product{
    public $id;
    public $title;
    public $price;
    public $stockLevel;
    public $categoryName;

    public $popularityFactor;

    public $color;

    public $categoryId; 

    public $description;
    // ANVÄND INTE CONSTRUCTOR MED PARAMETRAR FÖR PDO KAN INTE ANROPA DEN
};
?>