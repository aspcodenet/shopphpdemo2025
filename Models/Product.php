<?php
class Product{
    public $id;
    public $title;
    public $price;
    public $stockLevel;
    public $categoryName;

    public $popularityFactor; // NYTT FÄLT SOM LAGTS TILL I DATABASEN

    // ANVÄND INTE CONSTRUCTOR MED PARAMETRAR FÖR PDO KAN INTE ANROPA DEN
};
?>