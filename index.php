<?php

require 'config/constants.php';
require "src/Controller/CustomerController.php";
require "src/ApiGateway/CustomerGateway.php";
require "src/Database/Database.php";
require "src/Auth/JWTServiceProvider.php";


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all api endpoints start with customers otherwise the results is '404 Not Found'

if ($uri[2] !== 'customers') 
{   
       
    header("content-type: application/json");
    header("HTTP/1.1 404 Not Found");        
    $message = 'Rest Api not found';
          
    $response = json_encode(['response' => ['statusCode' => NOT_FOUND, "message" => $message]]);
    
    echo $response;
    exit;
}


// take the customer id from the url . The customer id can or can not be present and it must be a number:
$customerId = isset($uri[3]) ? $uri[3] : null;


$requestMethod = $_SERVER["REQUEST_METHOD"];

$dbConnection = (new Database())->getConnection();

// instantiate a new customer object passing as parameters the request method and customerId :
$customerController = new CustomerController($dbConnection, $requestMethod, $customerId);

$customerController->processRequest();


?>