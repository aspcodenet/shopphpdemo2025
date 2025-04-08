<?php 

require_once('Models/UserDatabase.php');

// Hur kan man strukturera klasser
// Hir kan man struktirera filer? Folders + subfolders
// NAMESPACES       

// LÄS IN ALLA  .env VARIABLER till $_ENV i PHP



    class Database{
        public $pdo; // PDO är PHP Data Object - en klass som finns i PHP för att kommunicera med databaser
        // I $pdo finns nu funktioner (dvs metoder!) som kan användas för att kommunicera med databasen

        private $usersDatabase;
        function getUsersDatabase(){
            return $this->usersDatabase;
        }        

        
        // Note to Stefan STATIC så inte initieras varje gång
        
        // SKILJ PÅ CONFIGURATION OCH KOD

        function __construct() {    
            $host = $_ENV['HOST'];
            $db   = $_ENV['DB'];
            $user = $_ENV['USER'];
            $pass = $_ENV['PASSWORD'];
            $port = $_ENV['PORT'];

            $dsn = "mysql:host=$host:$port;dbname=$db"; // connection string
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->initDatabase();
            $this->initData();
            $this->usersDatabase = new UserDatabase($this->pdo);
            $this->usersDatabase->setupUsers();
            $this->usersDatabase->seedUsers();
        }
        function initData(){
            // if select count(*) from Products = 0
            // lägg till 50 produkter
            $sql = "SELECT COUNT(*) FROM Products";
            $res = $this->pdo->query($sql);
            $count = $res->fetchColumn();
            if($count == 0){
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Banana', 10, 100, 'Fruit')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Apple', 5, 50, 'Fruit')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Pear', 7, 70, 'Fruit')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Cucumber', 15, 30, 'Vegetable')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Tomato', 20, 40, 'Vegetable')");
                $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Carrot', 10, 20, 'Vegetable')");
            }
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

        function getProduct($id){
            $query = $this->pdo->prepare("SELECT * FROM Products WHERE id = :id");
            $query->execute(['id' => $id]);
            $query->setFetchMode(PDO::FETCH_CLASS, 'Product');
            return $query->fetch();
        }

        function updateProduct($product){
            $s = "UPDATE Products SET title = :title," .
                " price = :price, stockLevel = :stockLevel, categoryName = :categoryName WHERE id = :id";
            $query = $this->pdo->prepare($s);
            $query->execute(['title' => $product->title, 'price' => $product->price,
                'stockLevel' => $product->stockLevel, 'categoryName' => $product->categoryName, 
                'id' => $product->id]);
        }

        function deleteProduct($id){
            $query = $this->pdo->prepare("DELETE FROM Products WHERE id = :id");
            $query->execute(['id' => $id]);
        }

        function insertProduct($title, $stockLevel, $price, $categoryName) {
            $sql = "INSERT INTO Products (title, price, stockLevel, categoryName) VALUES (:title, :price, :stockLevel, :categoryName)";
            $query = $this->pdo->prepare($sql);
            $query->execute(['title' => $title, 'price' => $price,
                'stockLevel' => $stockLevel, 'categoryName' => $categoryName]);
        }


        //function getAllProducts($sortCol, $sortOrder){
        function getAllProducts($sortCol="id", $sortOrder= "asc"){
            if(!in_array($sortCol,["id", "categoryName",  "title","price","stockLevel"])){
                $sortCol = "id";
            }
            if(!in_array($sortOrder,["asc", "desc"])){
                $sortOrder = "asc";
            }

            // SELECT * FROM Products ORDER BY  id asc
            $query = $this->pdo->query("SELECT * FROM Products ORDER BY $sortCol $sortOrder"); // Products är TABELL 
            return $query->fetchAll(PDO::FETCH_CLASS, 'Product'); // Product är PHP Klass
        }

        function getCategoryProducts($catName){
            if($catName == ""){
                $query = $this->pdo->query("SELECT * FROM Products"); // Products är TABELL 
                return $query->fetchAll(PDO::FETCH_CLASS, 'Product'); // Product är PHP Klass
            }
            $query = $this->pdo->prepare("SELECT * FROM Products WHERE categoryName = :categoryName");
            $query->execute(['categoryName' => $catName]);
            return $query->fetchAll(PDO::FETCH_CLASS, 'Product');
        }
        function getAllCategories(){
                // SELECT DISTINCT categoryName FROM Products
            $data = $this->pdo->query('SELECT DISTINCT categoryName FROM Products')->fetchAll(PDO::FETCH_COLUMN);
            return $data;
        }

    }
?>