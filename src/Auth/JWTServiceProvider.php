<?php

require_once('vendor/autoload.php');


use \Firebase\JWT\JWT;

class JWTServiceProvider
{

    private $conn;
    
    private $table_name = "users";
       
    public function __construct($db)
    {
        $this->conn = $db;        
    }    
        
    public function generateToken()
    {
        
        $expirationTime = (60 * 60);     
      
        $response = array();
        
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        //these are credentials for a registered  user 
        //"email" : "leonard@gmail.com",
        //"password" : "123456"
              
        $email = $input['email'];
		$password = $input['password'];
                
        try
        {
            //check if user is present and registered in database
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!is_array($user)) 
            {
              
                $statusCodeHeader = 'HTTP/1.1 401 UNAUTHORIZED';
                header($statusCodeHeader);
                $code = UNAUTHORIZED;
                $message = "Email or Password incorrect";
                $response = json_encode(['error' => ['statusCode' => $code, "message" => $message]]);
                echo $response;
                exit;
            }            

            $payload = [
                'iat' => time(),
                'iss' => 'localhost',
                'exp' => time() + $expirationTime,
                'userId' => $user['id']
            ];
           
            $token = JWT::encode($payload, SECRETE_KEY,'HS256');

            $data = ['token' => $token];

            
            //This token will be used as a value of a global variable jwt_token in Postman headers.   
            //Authorization key has as a value  this generated token 

            $result['statusCodeHeader'] = 'HTTP/1.1 200 OK';
            $result['data'] = $data;
            $result['code'] = SUCCESS_RESPONSE;

            return $result;
                                     
        }
        catch (Exception $e) 
        {
            $response = json_encode(['response' => ['statusCode' => JWT_PROCESSING_ERROR, "result" => $e->getMessage()]]);
            echo $response;
            exit;
        }
            
    }

    public function GetUserId()
    {
        $userId = null;

        if ($this->validateToken()) 
        {
            $token = $this->getBearerToken();

            $payload = JWT::decode($token, SECRETE_KEY, array('HS256'));

            $userId = $payload->userId; 
        }   
        
        return $userId;
    }

    public function validateToken() 
    {
        //To check if the user is registered or not  and also is authorized we will 
        //extract from the payload the userId and search if in user table database if this userId exist .   
          
        try 
        {
            $token = $this->getBearerToken();            

            $payload = JWT::decode($token, SECRETE_KEY, array('HS256'));
            
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :userId");
            $stmt->bindParam(":userId", $payload->userId); //receive the userId from the payload
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!is_array($user)) 
            {
                $msg = "This user is not found in database.";
                $response = json_encode(['error' => ['statusCode' => 'INVALID_USER_PASS', "message" => $msg]]);
                echo $response;
                exit;
            } 
            
            //The user exists . Is registered in database and also is authorized to access the Api service
            
            $statusCodeHeader = 'HTTP/1.1 200 OK';
            header($statusCodeHeader);
            return true;              
        } 
        catch (Exception $e) 
        {            
            $statusCodeHeader = 'HTTP/1.1 498 INVALID_TOKEN';
            header($statusCodeHeader);
            $code = INVALID_TOKEN;
            $response = json_encode(['error' => ['statusCode' => $code, "message" => $e->getMessage()]]);
            echo $response;
            exit;            
        }
       
    }


    /*
    public function getAuthorizationHeader()
    {	   
            $headers = null;
            $headers = getallheaders();
            $headers = $headers["Authorization"];
            return $headers;
	}
    */
    
    public function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) 
        {
           
            $headers = trim($_SERVER["Authorization"]);
        }
        
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) 
        { 
                       
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } 
        
        elseif (function_exists('apache_request_headers')) 
        {
            
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) 
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    

    public function getBearerToken() 
    {
        //Get the access token from  headers
        $headers = $this->getAuthorizationHeader();
        
        if (!empty($headers)) 
        {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) 
            {
                return $matches[1];
            }
        }
     
        $statusCodeHeader = 'HTTP/1.1 498 INVALID_TOKEN';
        header($statusCodeHeader);
        $code = INVALID_TOKEN;
        $response = json_encode(['error' => ['statusCode' => $code, "message" => 'Invalid token']]);
        echo $response;
        exit;
    }

    

}


