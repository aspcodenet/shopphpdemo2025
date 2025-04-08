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

        function addProductIfNotExists($title, $price, $stockLevel, $categoryName, $popularityFactor){
            $query = $this->pdo->prepare("SELECT * FROM Products WHERE title = :title AND price = :price AND stockLevel = :stockLevel AND categoryName = :categoryName");
            $query->execute(['title' => $title, 'price' => $price, 'stockLevel' => $stockLevel, 'categoryName' => $categoryName]);
            if($query->rowCount() == 0){
                $this->insertProduct($title, $stockLevel, $price, $categoryName,$popularityFactor);
            }
        }
        function initData(){
        //     $sql = "SELECT COUNT(*) FROM Products";
        //     $res = $this->pdo->query($sql);
        //     $count = $res->fetchColumn();
        //     if($count == 0){
        //         $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Banana', 10, 100, 'Fruit')");
        //         $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Apple', 5, 50, 'Fruit')");
        //         $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Pear', 7, 70, 'Fruit')");
        //         $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Cucumber', 15, 30, 'Vegetable')");
        //         $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Tomato', 20, 40, 'Vegetable')");
        //         $this->pdo->query("INSERT INTO Products (title, price, stockLevel, categoryName) VALUES ('Carrot', 10, 20, 'Vegetable')");
        //     }
            $this->addProductIfNotExists("Banana", 10, 100, "Fruit",5);
            $this->addProductIfNotExists("Apple", 5, 50, "Fruit",9);
            $this->addProductIfNotExists("Pear", 7, 70, "Fruit",2);
            $this->addProductIfNotExists("Cucumber", 15, 30, "Vegetable",7);
            $this->addProductIfNotExists("Tomato", 20, 40, "Vegetable",99);
            $this->addProductIfNotExists("Carrot", 10, 20, "Vegetable",56);
            $this->addProductIfNotExists("Potato", 5, 50, "Vegetable",2);
            $this->addProductIfNotExists("Onion", 7, 70, "Vegetable",33);
            $this->addProductIfNotExists("Lettuce", 15, 30, "Vegetable",1);
            $this->addProductIfNotExists("Broccoli", 20, 40, "Vegetable",5);
            $this->addProductIfNotExists("Spinach", 10, 20, "Vegetable",34);
            $this->addProductIfNotExists("Zucchini", 5, 50, "Vegetable",1);
            $this->addProductIfNotExists("Eggplant", 7, 70, "Vegetable",2);
         }

        function initDatabase(){
            $this->pdo->query('CREATE TABLE IF NOT EXISTS Products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(50),
                price INT,
                stockLevel INT,
                categoryName VARCHAR(50)
            )');
            if(!$this->columnExists("popularityFactor", "Products")){
                $this->pdo->query('ALTER TABLE Products ADD COLUMN  popularityFactor int default(0)');
            }
        }
         

        function columnExists($columnName, $tableName) {
            $query = $this->pdo->prepare("SHOW COLUMNS FROM $tableName LIKE :columnName");
            $query->execute(['columnName' => $columnName]);
            return $query->rowCount() > 0;
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

        function insertProduct($title, $stockLevel, $price, $categoryName,$popularityFactor) {
            $sql = "INSERT INTO Products (title, price, stockLevel, categoryName, popularityFactor) VALUES (:title, :price, :stockLevel, :categoryName,:popularityFactor)";
            $query = $this->pdo->prepare($sql);
            $query->execute(['title' => $title, 'price' => $price,
                'stockLevel' => $stockLevel, 'categoryName' => $categoryName, 'popularityFactor' => $popularityFactor]);
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
            // SELECT * FROM Products ORDER BY  id asc
            $query = $this->pdo->query("SELECT * FROM Products ORDER BY popularityFactor DESC LIMIT 0,10"); // Products är TABELL 
            return $query->fetchAll(PDO::FETCH_CLASS, 'Product'); // Product är PHP Klass
        }

        function searchProducts($q="", $sortCol="Id", $sortOrder="asc",$categoryName="", $pageNo = 1, $pageSize = 20){
            if($sortCol == null){
                $sortCol = "Id";
            }
            if($sortOrder == null){
                $sortOrder = "asc";
            }
            $sql = "SELECT * FROM Products ";
            $paramsArray = [];
            $addedWhere = false;
            if($q != null && strlen($q) > 0){  // Omman angett ett q - WHERE   tef
                    // select * from product where title like '%tef%' // Stefan  tefan atef
                if(!$addedWhere){
                    $sql = $sql . " WHERE ";            
                    $addedWhere = true;
                }else{
                    $sql = $sql . " AND ";    
                }
                $sql = $sql . " ( title like :q";        
                $sql = $sql . " OR  categoryName like :q )";        
                $paramsArray["q"] = '%' . $q . '%';                
            }
            if($categoryName != null && strlen($categoryName) > 0){
                if(!$addedWhere){
                    $sql = $sql . " WHERE ";            
                    $addedWhere = true;
                }else{
                    $sql = $sql . " AND ";    
                }
                $sql = $sql . " ( CategoryName = :categoryName )";        
                $paramsArray["categoryName"] = $categoryName;                
            }
    
            
            $sql .= " ORDER BY $sortCol $sortOrder ";    
    
            $sqlCount = str_replace("SELECT * FROM ", "SELECT CEIL (COUNT(*)/$pageSize) FROM ", $sql);
    
            // $pageNo = 1, $pageSize = 20
            $offset = ($pageNo-1)*$pageSize;
            $sql .= " limit $offset, $pageSize";    
    
            $prep = $this->pdo->prepare($sql);
            $prep->setFetchMode(PDO::FETCH_CLASS,'Product');
            $prep->execute($paramsArray);
            $data = $prep->fetchAll();      // arrayen  
    
    
    
            $prep2 = $this->pdo->prepare($sqlCount);
            $prep2->execute($paramsArray);
    
            $num_pages = $prep2->fetchColumn();       // antal sidor tex 3      
    
             $arr =  ["data"=>$data, "num_pages"=>$num_pages];
             return $arr;
    
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