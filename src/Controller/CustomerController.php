<?php

class CustomerController 
{
    private $requestMethod;
    private $customerId=null;

    private $userId = null;

    private $db;
    
    private $customerGateway;
    private $jwtProvider;

    public function getUserId() { return $this->userId; }    
    public function setUserId($userId) { $this->userId = $userId; }

    public function __construct($db, $requestMethod, $customerId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->customerId = $customerId;
        $this->customerGateway = new CustomerGateway($db);
        $this->jwtProvider = new JWTServiceProvider($db);
        
    }

    public function checkAuthentication()
    {

        //Check before  process request operation if the  user is authenticated or not 
        $validateToken = $this->jwtProvider->validateToken();       

        if ($validateToken != true) 
        {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
        else
        {
            //User is authenticated . Get the userId value and set with setUserId public method of Customer object  
            //it's userId private member value .
            $userId = $this->jwtProvider->GetUserId();
            $this->setUserId($userId); 
        }
    }
    
    public function processRequest()
    {        
                              
        switch ($this->requestMethod) 
        {
            case 'GET':
                //before processing the get request check if user is authenticated
                $this->checkAuthentication();
                if($this->customerId)
                {                        
                    $response = $this->getCustomer($this->customerId);
                }
                else
                {                   
                    $response =$this->getAllCustomers();
                }
                break;

            case 'POST':
                   //The http post request is used to store customers data in database 
                   //It can also be used to generateToken   
                   $response = $this->processPostRequest();
                
                break;

            case 'PUT':
                 //before processing the  put request check if user is authenticated
                $this->checkAuthentication();
                $response = $this->updateCustomer($this->customerId);
                break;

            case 'DELETE':
                 //before processing the delete  request check if user is authenticated
                $this->checkAuthentication();
                $response = $this->deleteCustomer($this->customerId);
                break;

            default:
                header("HTTP/1.1 405 Method Not Allowed");
                $this->returnResponse(METHOD_NOT_ALLOWED, ['message' => 'Not a valid method  to process.']);
                break;
        }
        
        header($response['statusCodeHeader']);

        $this->returnResponse($response['code'], $response['data']);          
                   
    }

    private function getAllCustomers()
    {
        
        $response = array();
        
        $result = $this->customerGateway->findAll(); //the returned $result is an array of arrays 

        if(!is_array($result)) 
        {
            $this->returnResponse(NO_CONTENT, ['message' => 'Customers details not found.']);
        }

        $response['statusCodeHeader'] = 'HTTP/1.1 200 OK';
        $response['data'] = $result;
        $response['code'] = SUCCESS_RESPONSE;

        return $response;                              
        
    }

    private function getCustomer($id)
    {         
        $response = array();
        
        $customerId = $this->validateParameter('customerId', $id, INTEGER);

        $result = $this->customerGateway->find($customerId);
        
        if(!$result) 
        {
            return $this->CustomerNotFound();            
        }
        
        $response['statusCodeHeader'] = 'HTTP/1.1 200 OK';
        $response['data'] = $result;
        $response['code'] = SUCCESS_RESPONSE;

        return $response;
        
    }

    private function processPostRequest()
    {
        
        $response = array();
        
        $inputData = (array) json_decode(file_get_contents('php://input'), TRUE);
          
        //To check if the post request was send to generate  the  token 
        //, is necessary to control if generate token service field is  present and not null   

        if(isset($inputData['service']))
        {
            if ($inputData['service'] == 'generateToken') 
            {
                $response = $this->jwtProvider->generateToken();
                
            }
            else
            {   
                $statusCodeHeader = 'HTTP/1.1 300 JWT_PROCESSING_ERROR ';            
                $this->throwError($statusCodeHeader , JWT_PROCESSING_ERROR, "Invalid generate token service  .");                
            }  
        }
        else
        {   //createCustomer 
            $this->checkAuthentication();
            $response = $this->createCustomer($inputData);
        }
        return $response;
        
    }

    private function createCustomer($inputData)
    {        
        
        //Check Customer Fields 
        if (! $this->checkCustomerFields($inputData)) 
        {            
            return $this->NotProcessable();
        }
        //Validate parameters  after receiving their values
        else
        {
            $first_name = $this->validateParameter('first_name', $inputData['first_name'], STRING, true);
            $last_name = $this->validateParameter('last_name', $inputData['last_name'], STRING, true);
            $email = $this->validateParameter('email', $inputData['email'], EMAIL, true);
            $created_at =  date('Y-m-d');
            
            //Check if this email actually exists  in database
            $checkMail = $this->customerGateway->checkEmail($email);
            if ($checkMail)
            { 
              $statusCodeHeader = 'HTTP/1.1 409 Conflict';            
              $this->throwError($statusCodeHeader , CONFLICT, "This email already exists");
    
            }
        }    
       
        $this->customerGateway->setFirstName($first_name);
        $this->customerGateway->setLastName($last_name);
        $this->customerGateway->setEmail($email);
        $this->customerGateway->setCreatedAt($created_at);
        $this->customerGateway->setUserId($this->userId);
        
        if(!$this->customerGateway->create())
        {
            $message = 'Failed to create.';
            $response['statusCodeHeader'] = 'HTTP/1.1 500 Internal server error';           
            $response['code'] = INTERNAL_SERVER_ERROR;
        }
        else 
        {
            $message = "Created successfully";
            $response['statusCodeHeader'] = 'HTTP/1.1 201 Created';
            $response['code'] = CREATED_SUCCESSFULLY;            
                      
        }
    
        $response['data'] = $message;        
        
        return $response;        
    }

    private function updateCustomer($id)
    {        
        $response = array();             
        
        $customerId = $this->validateParameter('customerId', $id, INTEGER);
        
        $customerData = $this->customerGateway->find($customerId);
                        
        if(!$customerData) 
        {
            return $this->CustomerNotFound();            
        }
        
        $newCustomerData = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        $newCustomerData['updated_at'] = date('Y-m-d');
       
        if (isset($newCustomerData['first_name']))
        {
            $this->validateParameter('first_name', $newCustomerData['first_name'], STRING, true);
        }
        if (isset($newCustomerData['last_name'])) 
        {            
             $this->validateParameter('last_name', $newCustomerData['last_name'], STRING, false);
        }
        if (isset($newCustomerData['email'])) 
        {
             $this->validateParameter('email', $newCustomerData['email'], EMAIL, false);
        }
        if (isset($newCustomerData['created_at'])) 
        {
             $this->validateParameter('created_at', $newCustomerData['created_at'], DATE, false);
        }
        
        $result = $this->customerGateway->update($customerData, $newCustomerData);
        
        if(! $result) 
        {
            $message = 'Failed to update.';
            $statusCodeHeader = 'HTTP/1.1 500 Internal server error';           
            $code = INTERNAL_SERVER_ERROR;
            $this->throwError($statusCodeHeader, $code, $message);

        } 
        else
        {
            $message = "Updated successfully";
            $response['statusCodeHeader'] = 'HTTP/1.1 200 OK';
            $response['code'] = SUCCESS_RESPONSE;
        }
        
        
        $response['data'] = $message;        
        return $response;       
    }

    private function deleteCustomer($id)
    {
        
        $response = array();

        $customer = $this->customerGateway->find($id);
        
        if(!$customer) 
        {
            return $this->CustomerNotFound(); 
        }

        $result = $this->customerGateway->delete($id);
        
        if(!$result)
        {
            $message = 'Failed to update.';
            $statusCodeHeader = 'HTTP/1.1 500 Internal server error';           
            $code = INTERNAL_SERVER_ERROR;
            $this->throwError($statusCodeHeader, $code, $message);
        }
        else
        { 
            $message = "Deleted successfully";
            $response['statusCodeHeader'] = 'HTTP/1.1 200 OK';
            $response['code'] = SUCCESS_RESPONSE;
        }
               
        $response['data'] = $message;        
        return $response;

    }

    private function checkCustomerFields($input)
    {
        if (! isset($input['first_name'])) 
        {
            return false;
        }
        if (! isset($input['last_name'])) 
        {
              return false;
        }
        if (! isset($input['email'])) 
        {
              return false;
        }
        return true;
    }

        
    public function validateParameter($fieldName, $value, $dataType, $required = true) 
    {   

        $statusCodeParameterRequired = 'HTTP/1.1 422 Unprocessable Entity';
        $statusCodeHeaderDataType = 'HTTP/1.1 400 Bad Request';

        if($required == true && empty($value) == true) 
        {                
            $this->throwError($statusCodeParameterRequired , VALIDATE_PARAMETER_REQUIRED, $fieldName . " parameter is required");
            
        }

        switch ($dataType) 
        {
           
            case INTEGER:
                if(!is_numeric($value)) 
                {
                    $this->throwError($statusCodeHeaderDataType,VALIDATE_PARAMETER_DATATYPE, "Datatype not valid for " . $fieldName . '. Should be numeric');
                }
                break;

            case STRING:
                if(!is_string($value)) 
                {
                    $this->throwError($statusCodeHeaderDataType,VALIDATE_PARAMETER_DATATYPE, "Datatype not valid for " . $fieldName . '. Should be string');
                }
                break;

            case EMAIL:                
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) 
                {                                     
                    $this->throwError($statusCodeHeaderDataType,VALIDATE_PARAMETER_DATATYPE, "Datatype not valid for " . $fieldName . '. Should be a valid email');
                }
                break;

            case DATE:                
                if (! $this->validateDate($value)) 
                {                                     
                    $this->throwError($statusCodeHeaderDataType,VALIDATE_PARAMETER_DATATYPE, "Datatype not valid for " . $fieldName . '. Should be a Y-m-d format');
                }
                break;                        
            
            default:
                break;
        }

        return $value;

    } 
  
    public function throwError($statusCodeHeader,$code, $message) 
    {      

        header($statusCodeHeader);           
        $errorMessage = json_encode(['error' => ['statusCode'=>$code, 'message'=>$message]]);
        echo $errorMessage;
        exit;
    }

    
    public function returnResponse($code, $data) 
    {   
        $response = json_encode(['response' => ['statusCode' => $code, "result" => $data]]);
        echo $response;
        exit;
    }
    
    private function CustomerNotFound()
    {        
        $statusCodeHeader = 'HTTP/1.1 404 Not Found';
        $statusCode = NOT_FOUND;
        $message = "Customer not found";

        $this->throwError($statusCodeHeader, $statusCode, $message);
        
    }    

    function validateDate($date, $format = 'Y-m-d')
    {        
        $dateTime = date_create_from_format($format, $date);
	    return $dateTime && date_format($dateTime, $format) === $date;
    }

    private function NotProcessable()
    {
        $statusCodeHeader = 'HTTP/1.1 422 Unprocessable Entity';
        $message = 'Invalid input';
        $statusCode = 422;
        $this->throwError($statusCodeHeader, $statusCode, $message);
        
    }

    

}