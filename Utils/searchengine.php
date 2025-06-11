<?php


require_once("vendor/autoload.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
require_once("Models/Product.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once("Models/Database.php"); // LADDA ALLA DEPENDENCIES FROM VENDOR
//  :: en STATIC funktion
$dotenv = Dotenv\Dotenv::createImmutable("."); // . is  current folder for the PAGE
$dotenv->load();




class SearchEngine{
    // Nr 12
    private $accessKey = 'qLidn4cVx6dS8dhJ6zRCHw';
    private $secretKey='7JByYAMA4aMbRLtCH8WdBauMfu_ENQ';
    private $url = "https://betasearch.systementor.se";
    private $index_name = "products-13";


    // Nr 5
    // private $accessKey = 'MHPD-epV-6ZygsphezEPxw';
    // private $secretKey='sTcru3VjnlVs1fgDTY91hmT0otD8Cw';
    // private $url = "http://localhost:8080";

    // private $index_name = "products-5";

    private  $client;

    function __construct(){
        $this->client = new Client([
            'base_uri' => $this->url,
            'verify' => false,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->accessKey . ':' . $this->secretKey),
                'Content-Type' => 'application/json'
            ]
        ]);

    }

    function getDocumentIdOrUndefined(string $webId): ?string {
        $query = [
            'query' => [
                'term' => [
                    'webid' => $webId
                ]
            ]
        ];


        try {
            $response = $this->client->post("/api/index/v1/{$this->index_name}/_search", [
                'json' => $query
            ]);

            $data = json_decode($response->getBody(), true);

            if (empty($data['hits']['total']['value'])) {
                return null;
            }

            return $data['hits']['hits'][0]['_id'];
        } catch (RequestException $e) {
            // Hantera eventuella fel här
            echo $e->getMessage();
            return null;
        }
    }

    // Integration med tredjepartssystem: REST/JSON, Filer (XML mot Prisjakt) - språk/regelverk att förhålla sig till

    function search(string $query,string $sortCol, string $sortOrder, int $pageNo, int $pageSize,$facets){
        // "språk" mot sökmotorn
        // offset, limit, 
        // 50, 10
        // from  , size
        $aa = "";
        // foreach($facets as $facet){
        //     if($aa != ""){
        //         $aa .= " AND ";
        //     } 
        //     $aa = $aa .  $facet[0] . ":" .  implode(" or " , $facet[1]) ;
        // }
        // if($aa != ""){
        //     $aa = $aa . " AND ";
        // }

        $aa = $aa . " combinedsearchtext:" . $query . '*';     

        $query = [
            'query' => [
                            'query_string' => [
                                'query' =>  $aa
                            ],
                
            ],
            'post_filter' => [
                
                'bool' => [
                    'must' => $this->getAllTerms($facets,"") 
                            ]],
            'from' => ($pageNo - 1) * $pageSize,
            'size' => $pageSize,
            'sort' => [
                $sortCol => [
                    'order' => $sortOrder
                ]
                ],
            'aggs'=>$this->GetAggs($facets)

        ];


        //echo "QUERY: " . json_encode($query)  . "</br>";   

        try {
            $response = $this->client->post("/api/index/v1/{$this->index_name}/_search", [
                'json' => $query
            ]);

            $data = json_decode($response->getBody(), true);

                             // data.hits.total.value
            if (empty($data['hits']['total']['value'])) {
                return null;
            }
            //print_r($data["aggregations"]["facets"]['names']['buckets'] );

            $data["hits"]["hits"] = $this->convertSearchEngineArrayToProduct($data["hits"]["hits"]);
            $pages = ceil($data["hits"]["total"]["value"] / $pageSize);

            //var_dump($data["aggregations"]);
            //var_dump($data["aggregations"]["aggs_Color"]["facets"]["aggs_special"]["names"]["buckets"]["0"]["values"]["buckets"]);
            return  ["data"=>$data["hits"]["hits"],
                     "num_pages"=>$pages,
                     "aggregations_color"=>$data["aggregations"]["aggs_Color"]["facets"]["aggs_special"]["names"]["buckets"]["0"]["values"]["buckets"],
                     "aggregations_categoryName"=>$data["aggregations"]["aggs_Category"]["facets"]["aggs_special"]["names"]["buckets"]["0"]["values"]["buckets"],
                    ];
        } catch (RequestException $e) {
            // Hantera eventuella fel här
            echo $e->getMessage();
            return null;
        }  
    }


    function getAllTerms($facets, $current){
        $terms = [];
        foreach($facets as $facet){
            if($current == $facet[0]){
                    continue;
            }
            $term = [];
            $term["term"] = [];
            $term["term"][$facet[0]] = implode(",",$facet[1]); // TODO FLERA 
            array_push($terms, $term);
        }
        return $terms;
    }

    function GetAggs($facets){
        $aggs = [];


         


        foreach(["Category","Color"] as $facet){

            $aggsFacet = [
                    'facets' => [
                        'nested' => [
                            'path' => 'string_facet'
                        ],
                        'aggs' => [
                            'aggs_special' => [
                                'filter' => [
                                    'match' => [
                                        'string_facet.facet_name' => "$facet"
                                    ]
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

            $aggs["aggs_$facet"]  = [
                    "aggs" => $aggsFacet
            ];
            $f = $this->setFilter($facet, $facets[$facet]);
            if($f != null){
                $aggs["aggs_$facet"]['filter'] = $f;
            }else{
                $aggs["aggs_$facet"]['filter'] = [
                    'match_all' => new \stdClass()
                ];
            }


        }
//        var_dump($aggs);

        return $aggs;
    }

    function setFilter($facet, $values){
        if($values == null || count($values) == 0){
            return null;
        }
$filter = [
    [
        'nested' => [
            'path' => 'string_facet',
            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'term' => [
                                'string_facet.facet_name' => "$facet"
                            ]
                        ],
                        [
                            'term' => [
                                'string_facet.facet_value' => "$values"
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
        return $filter;
    }


    /*
    array(4) { ["_index"]=> string(11) "products-12" ["_id"]=> string(20) "JevW55YBjv4AvNg2A_3B" ["_score"]=> float(1) ["_source"]=> array(9) { ["webid"]=> int(24) ["title"]=> string(18) "Sleek Cotton Clock" ["description"]=> string(127) "Fabric-covered clock with silent quartz movement. Minimalist design blends into any decor. Hidden stitching ensures durability." ["price"]=> int(10) ["categoryName"]=> int(5) ["stockLevel"]=> int(98) ["color"]=> string(5) "white" ["categoryid"]=> int(5) ["string_facet"]=> array(2) { [0]=> array(2) { ["facet_name"]=> string(5) "Color" ["facet_value"]=> string(5) "white" } [1]=> array(1) { ["facet_name"]=> string(8) "Category" } } } }
    
    */

    function convertSearchEngineArrayToProduct($searchengineResults){
        $newarray = [];
        foreach($searchengineResults as $hit){
            // echo "MUUU";
            // var_dump($hit);
            $prod = new Product();
            $prod->searchengineid = $hit["_id"];
            $prod->id = $hit["_source"]["webid"];
            $prod->title = $hit["_source"]["title"];
            $prod->description = $hit["_source"]["description"];
            $prod->price = $hit["_source"]["price"];
            $prod->categoryName = $hit["_source"]["categoryName"];
            $prod->categoryId = $hit["_source"]["categoryId"];
            $prod->color = $hit["_source"]["color"];
            $prod->stockLevel = $hit["_source"]["color"];

            array_push($newarray, $prod);
        }
        return $newarray;

    }



// $res = search("cov*",$accessKey,$secretKey,$url,$index_name);
// //var_dump(count($res["hits"]["hits"]));
// for($i =0 ; $i < count($res["hits"]["hits"]); $i++){
//     $hit = $res["hits"]["hits"][$i];
// //    var_dump($hit);
//     echo $hit["_id"] . ","; 
//     echo $hit["_source"]["webid"] . ","; 
//     echo $hit["_source"]["title"] . ","; 
//     echo $hit["_source"]["price"] . "</br>"; 
// }



}





// $res = getDocumentIdOrUndefined(1,$accessKey,$secretKey,$url,$index_name);
// if ($res == null){
//     die("INGET");
// }else{
// }



