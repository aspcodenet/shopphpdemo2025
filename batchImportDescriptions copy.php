<?php


require_once("vendor/autoload.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
require_once("Models/Database.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
//  :: en STATIC funktion
$dotenv = Dotenv\Dotenv::createImmutable("."); // . is  current folder for the PAGE
$dotenv->load();



//echo "DB UPDATE\n";
// $file = fopen("imports/deepseek_csv_20250501_dd402f.txt","r");
// $database = new Database();

// try{
//     while(!feof($file)){
//         $arr = fgetcsv($file);
//         $productName = $arr[0];
//         $productDescription = $arr[1];
//         $productColor = $arr[2];
//         $product = $database->getProductByName($productName);
//         if($product == null){
//             echo "Product not found: $productName\n";
//             continue;
//         }
//         echo "Product found: $productName\n";
//         $product->description = $productDescription;
//         $product->color = $productColor;
        
//         $database->updateProduct($product);
    
//     }
    
//     fclose($file);
    
// }catch(Exception $e){
//     echo "Error: " . $e->getMessage() . "\n";
//     echo $e->getMessage();
// }

// die("Color update");


/*

productsen
{
  "analysis": {
    "analyzer": {
      "default": {
        "tokenizer": "en",
        "type": "standard"
      }
    }
  }
}

*/


$user = "admin";
$pass = "Hejsan123#";
//$pass = "Complexpass#123";
//$indexName = "productsen";
$indexName = "productsaaaaaaaa";
// $indexName = "products_pelle";
// $user = "pelle";
// $pass = "pelle1234";
//$url = "https://search.systementor.se/";
$url = "http://localhost:8080/";


$params = [
        'settings' => [
            "number_of_shards"=> 3,
            "number_of_replicas"=> 2
        ]
];






try{
    $client = new GuzzleHttp\Client();

    $res = $client->request('PUT', $url . "api/index/v1/$indexName", [
        'auth' => [$user, $pass],
        'verify' => false,
        'json' => $params
        
    ]);
    echo $res->getStatusCode();


    $res = $client->request('GET', $url . 'api/index', [
        'auth' => [$user, $pass],
        'verify' => false,
        ]);
    echo $res->getStatusCode();
} catch (Exception $e) {
    echo  $e->getMessage();    
}

die("eree");


echo "Starting import\n";
$database = new Database();

try{
    foreach($database->getAllProducts() as $product){

        $category = $database->getCategoryById($product->categoryId);
        
        $res = $client->request('POST', $url . "api/" . $indexName . '/_doc', [
            //'auth' => ['admin', 'Complexpass#123'],
            'auth' => [$user, $pass],
            'verify' => false,
            'json' => [
                'id' => $product->id,
                'description' => $product->description,
                'title' => $product->title,
                'price' => $product->price,
                'category' => $product->categoryId,
                'stockLevel' => $product->stockLevel,
                'color' => $product->color,
                'categoryName' => $category->name2,
                ]
            ]);
    
            echo $res->getStatusCode();
    
    //    $database->updateProduct($product);
    
    }
    
    
}catch(Exception $e){
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getMessage();
}


// SEARCH MED  FACETS
/*

{
    "search_type": "match",
    "query": {
        "term": "decor"
    },
    "from": 0,
    "max_results": 20,
     "aggs":{"Color":{ "agg_type": "term", "field":"color","size":10 },
                  "categoryName":{ "agg_type": "term", "field":"categoryName","size":10 }
}
}

*/



?>