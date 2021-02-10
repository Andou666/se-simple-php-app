// restaurants_searcher.php

<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

// 初期設定
$KEYID =  getenv('HP_ACCESS_KEY');
$COUNT = 100;
$PREF = "Z011"; //東京
$FORMAT = "json";

$PARAMS = array("key"=> $KEYID, "count"=>$COUNT, "large_area"=>$PREF, "format"=>$FORMAT);

function write_data_to_csv($params){
    
    $restaurants = [["名称","住所","営業日"]];
    $client = new Client();
    try{
        $json_res = $client->request('GET', "http://webservice.recruit.co.jp/hotpepper/gourmet/v1/", ['query' => $params])->getBody();
    }catch(Exception $e){
        return print("エラーが発生しました。");
    }
    $response = json_decode($json_res,true);
    
    if(isset($response["error"])){
        return(print("エラーが発生しました！"));
    }
    
    foreach($response["results"]["shop"] as &$restaurant){
        $rest_info = [$restaurant["name"],$restaurant["address"],$restaurant["open"]];
        $restaurants[] = $rest_info;
    }
    $handle = fopen("restaurants_list.csv", "wb");
    
    foreach ($restaurants as $values){
        fputcsv($handle, $values);
    }

    fclose($handle);
    return print_r($restaurants);
}

write_data_to_csv($PARAMS);

?>