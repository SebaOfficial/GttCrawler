<?php
/**
 * Gtt Client Endpoint.
 * This file handles the HTTP reuqests.
 * 
 * @author Sebastiano Racca
*/
declare(strict_types=1);


define("ROOT_DIR", dirname(dirname(__DIR__)));

require_once ROOT_DIR . "/vendor/autoload.php";

use \Gtt\Api\Components\{Response, GttClient};


$response = new Response();

$response->setHeaders(["Content-Type: application/json"])
    ->allowMethods(["OPTIONS", "GET"])
        ->allowOrigin(["*"]);


if(isset($_GET['stop'])){

    if(!is_numeric($_GET['stop'])){
        $response->setHttpCode(400)
            ->setBody(["error" => "Stop id must be a numeric value."])
                ->send();
    }

    $gttClient = new GttClient((int)$_GET['stop']);

    try{
        $res = $gttClient->askStop();

        if($res === NULL)
            throw new Exception();
            
        $response->setHttpCode(200)
            ->setBody((array)$res)
                ->send();

    } catch(Exception $e){
        $response->setHttpCode(404)
            ->setBody(["error" => "Stop id not found."])
                ->send();
    }

} else{
    $response->setHttpCode(400)
        ->setBody(["error" => "Must specify the stop id."])
            ->send();
}