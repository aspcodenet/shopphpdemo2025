<?php


require_once("vendor/autoload.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
require_once("Models/Database.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
//  :: en STATIC funktion
$dotenv = Dotenv\Dotenv::createImmutable("."); // . is  current folder for the PAGE
$dotenv->load();


$username = "admin";
$password = "Hejsan123#";
$indexName = "productstesthohoii";

$host = 'https://localhost:9200';
//$host = 'https://searchapi.systementor.se:9200';

try{
    $client = (new \OpenSearch\ClientBuilder())
        ->setHosts([$host])
        ->setBasicAuthentication($username, $password) // For testing only. Don't store credentials in code.
        ->setSSLVerification(false) // For testing only. Use certificate for validation
        ->build();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    die("Could not connect to OpenSearch server.");
}


$params = [
    'index' => $indexName,
    'body'  => [
        'mappings' => [
            'properties' => [
                'string_facet' => [
                    'type' => 'nested',
                    'properties' => [
                        'facet_name'  => ['type' => 'keyword'],
                        'facet_value' => ['type' => 'keyword']
                    ]
                ]
            ]
        ]
    ]
];

try {
    $client->indices()->create($params);
    echo "Index created successfully:\n";
    print_r($response);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    //die("Index error.");
}


$database = new Database();


try{
    foreach($database->getAllProducts() as $product){

        $category = $database->getCategoryById($product->categoryId);
        
        $res = $client->index([
            'index' => $indexName,
            'id' => $product->id,
            'body' => [
                'title' => $product->title,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $category->name,
                'color' => $product->color,
                'string_facet' => [
                    [
                        'facet_name' => "Color",
                        'facet_value' => $product->color
                    ],
                    [
                        'facet_name' => "Category",
                        'facet_value' => $category->name
                    ]
                ]
            ]
        ]);
    
      //$database->updateProduct($product);
    
    }
    
    
}catch(Exception $e){
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getMessage();
}





$params = [
    'index' => 'productstest',
    'body'  => [
        'query' => [
            'query_string' => [
                'query' => 'cov*'
            ]
        ],
        'aggs' => [
            'facets' => [
                'nested' => [
                    'path' => 'string_facet'
                ],
                'aggs' => [
                    'names' => [
                        'terms' => [
                            'field' => 'string_facet.facet_name'
                        ],
                        'aggs' => [
                            'values' => [
                                'terms' => [
                                    'field' => 'string_facet.facet_value'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

try {

    $response = $client->search($params);
    
    // Output results
    echo "Search results:\n";
    echo "Total hits: " . $response['hits']['total']['value'] . "\n\n";
    
    echo "Aggregations:\n";
    foreach ($response['aggregations']['facets']['names']['buckets'] as $bucket) {
        echo "Facet name: " . $bucket['key'] . " (" . $bucket['doc_count'] . ")\n";
        foreach ($bucket['values']['buckets'] as $valueBucket) {
            echo "  - Value: " . $valueBucket['key'] . " (" . $valueBucket['doc_count'] . ")\n";
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    die("Search error.");
}







// $database = new Database();


// try{
//     foreach($database->getAllProducts() as $product){

//         $category = $database->getCategoryById($product->categoryId);
        
//         $res = $client->index([
//             'index' => $indexName,
//             'id' => $product->id,
//             'body' => [
//                 'title' => $product->title,
//                 'description' => $product->description,
//                 'price' => $product->price,
//                 'category' => $category->name,
//                 'color' => $product->color,
//                 'string_facet' => [
//                     [
//                         'facet_name' => "Color",
//                         'facet_value' => $product->color
//                     ],
//                     [
//                         'facet_name' => "Category",
//                         'facet_value' => $category->name
//                     ]
//                 ]
//             ]
//         ]);
    
//       //$database->updateProduct($product);
    
//     }
    
    
// }catch(Exception $e){
//     echo "Error: " . $e->getMessage() . "\n";
//     echo $e->getMessage();
// }






?>