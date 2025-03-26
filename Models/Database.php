<?php 
    class Database{
        public $pdo;

        function __construct() {    
            $host = "localhost";
            $db   = "shoppen";
            $user = "root";
            $pass = "hejsan123";

            $dsn = "mysql:host=$host;port=3306;dbname=$db";
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->initDatabase();
            $this->createProducts();
        }

        function initDatabase(){
            $this->pdo->query('CREATE TABLE IF NOT EXISTS Products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(50),
                price INT,
                stockLevel INT,
                categoryName VARCHAR(50)
            )');
        }

        function getAllProducts($sortCol="", $sortOrder=""){
            if(!in_array($sortCol,["id", "title", "price", "stockLevel", "categoryName"])){
                $sortCol = "id";
            };
            if(!in_array($sortOrder,["asc","desc"])){
                $sortOrder = "asc";
            };
            $query = $this->pdo->query("SELECT * FROM Products ORDER BY $sortCol $sortOrder"); // Products är TABELL 
            return $query->fetchAll(PDO::FETCH_CLASS, 'Product'); // Product är PHP Klass
        }
        function getAllCategories(){
                // SELECT DISTINCT categoryName FROM Products
            $data = $this->pdo->query('SELECT DISTINCT categoryName FROM Products')->fetchAll(PDO::FETCH_COLUMN);
            return $data;
        }
        function createProducts(){
            $sql = "SELECT COUNT(*) FROM Products";
            $res = $this->pdo->query($sql);
            $count = $res->fetchColumn();
            if($count < 3){
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Banana', 10, 100, 'Fruit')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Apple', 5, 50, 'Fruit')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Pear', 7, 70, 'Fruit')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Cucumber', 15, 30, 'Vegetable')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Tomato', 20, 40, 'Vegetable')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Carrot', 10, 20, 'Vegetable')");
            }
        }

        function getProductById($id){
            $query = $this->pdo->prepare('SELECT * FROM Products WHERE id = :id');
            $query->execute(['id' => $id]);
            return $query->fetchObject('Product');
        }

        function updateProduct($product){
            $query = $this->pdo->prepare('UPDATE Products SET title = :title, price = :price, stockLevel = :stockLevel, categoryName = :categoryName WHERE id = :id');
            $query->execute(['title' => $product->title, 'price' => $product->price, 'stockLevel' => $product->stockLevel, 'categoryName' => $product->categoryName, 'id' => $product->id]);
        }   

    }
?>