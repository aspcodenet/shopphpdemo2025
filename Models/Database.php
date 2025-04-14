<?php 

require_once('Models/UserDatabase.php');
require_once("vendor/autoload.php");

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
            $this->modifyDatabase();
            $this->initData();
            $this->usersDatabase = new UserDatabase($this->pdo);
            $this->usersDatabase->setupUsers();
            $this->usersDatabase->seedUsers();
        }

        function addProductIfNotExists($title, $price, $stockLevel, $categoryName,$popularityFactor){
            $query = $this->pdo->prepare("SELECT * FROM Products WHERE title = :title");
            $query->execute(['title' => $title]);
            if($query->rowCount() == 0){
                $this->insertProduct($title, $stockLevel, $price, $categoryName,$popularityFactor);
            }
        }


        function initData(){
            $sql = "SELECT COUNT(*) FROM Products";
            $res = $this->pdo->query($sql);
            $count = $res->fetchColumn();
            if($count > 0){
                return;
            }
            $faker = \Faker\Factory::create();
            $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));            

            for($i = 0; $i < 100; $i++){
                $title = $faker->productName();
                $price = $faker->numberBetween(1, 100);
                $stockLevel = $faker->numberBetween(1, 100);
                $categoryName = $faker->category();
                $popularityFactor = $faker->numberBetween(1, 100);
                $this->addProductIfNotExists($title, $price, $stockLevel, $categoryName,$popularityFactor);
            }
        }

        function columnExists($pdo, $table, $column) {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM $table WHERE  field = :column");
            $stmt->execute(['column' => $column]);
            return $stmt->rowCount() > 0;
        }

        function modifyDatabase(){
            if($this->columnExists($this->pdo, 'Products', 'color')){
                return;
            }
            $this->pdo->query('ALTER TABLE Products ADD COLUMN color varchar(20) DEFAULT NULL');
        }

        function initDatabase(){
            $this->pdo->query('CREATE TABLE IF NOT EXISTS Products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(50),
                price INT,
                stockLevel INT,
                categoryName VARCHAR(50),
                popularityFactor INT DEFAULT(0)            
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
                " price = :price, stockLevel = :stockLevel, categoryName = :categoryName, popularityFactor=:popularityFactor WHERE id = :id";
            $query = $this->pdo->prepare($s);
            $query->execute(['title' => $product->title, 'price' => $product->price,
                'stockLevel' => $product->stockLevel, 'categoryName' => $product->categoryName, 
                'id' => $product->id,
                'popularityFactor' => $product->popularityFactor]);
        }

        function deleteProduct($id){
            $query = $this->pdo->prepare("DELETE FROM Products WHERE id = :id");
            $query->execute(['id' => $id]);
        }

        function insertProduct($title, $stockLevel, $price, $categoryName,$popularityFactor) {
            $sql = "INSERT INTO Products (title, price, stockLevel, categoryName, popularityFactor) VALUES (:title, :price, :stockLevel, :categoryName, :popularityFactor)";
            $query = $this->pdo->prepare($sql);
            $query->execute(['title' => $title, 'price' => $price,
                'stockLevel' => $stockLevel, 'categoryName' => $categoryName,
                'popularityFactor' => $popularityFactor]);
        }


        function searchProducts($q,$sortCol, $sortOrder){ // $q = oo
            if(!in_array($sortCol,[ "title","price"])){ // title123312132312321
                $sortCol = "title";
            }
            if(!in_array($sortOrder,["asc", "desc"])){
                $sortOrder = "asc";
            }

            $query = $this->pdo->prepare("SELECT * FROM Products WHERE title LIKE :q OR categoryName LIKE :q ORDER BY $sortCol $sortOrder"); // Products är TABELL
            $query->execute(['q' => "%$q%"]);
            return $query->fetchAll(PDO::FETCH_CLASS, 'Product');
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
        function getPopularProducts(){
            $query = $this->pdo->query("SELECT * FROM Products ORDER BY popularityFactor DESC LIMIT 10"); // Products är TABELL 
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