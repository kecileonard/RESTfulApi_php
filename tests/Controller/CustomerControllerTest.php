<?php

require('vendor/autoload.php');
//use GuzzleHttp\Client;

use PHPUnit\Framework\TestCase;

class CustomerControllerTest extends TestCase
{
    protected $client;

    protected $token;

    protected function setUp():void 
    {
        $this->client = new GuzzleHttp\Client([
            
            'base_uri' => 'http://localhost/restful_api_php/'
        ]);

        $this->token = $this->getToken();
        
    }

    public function tearDown():void
    {
        $this->client = null;
    }
    
    public function getToken()
    {
        $email = 'leonard@gmail.com';
        $password = '123456';        
       
        try 
        {           
            $result = $this->client->request('POST', 'customers', [
                  'headers' => [
                      'Accept'     => 'application/json',
                      'Content-Type' => 'application/json'
                  ],
                  'json' => [
                      'service'  =>'generateToken',	
                      'email'    => $email,
                      'password' => $password,
                  ]
              ]);             

              $response = json_decode($result->getBody() , true); 
              
                   
        }
        catch (exception $e) 
        { 
              if ($e->getMessage()) 
              {
                print_r($e->getMessage()); 
                die();
              }
        }
        return $response['response']['result']['token'];
         
    }

    
    public function testNonExistentEndpoint()
    {     
            $response = $this->client->get('nonexistent-endpoint', [
                               
                'http_errors' => false

            ]);                  

            $responseData = json_decode($response->getBody(), true);
        
            $this->assertArrayHasKey("statusCode", $responseData['response']);
            $this->assertArrayHasKey("message", $responseData['response']);        
            $this->assertEquals("Rest Api not found", $responseData['response']['message']);            
    
    }

    public function testGetAllCustomersSuccessfully() 
    {
                                          

            $headers = [

                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',

            ];

            $response = $this->client->request('GET', 'customers', [

                'http_errors' => false,
                'headers' => $headers

            ]);

            $this->assertEquals(200, $response->getStatusCode());

            $data = json_decode($response->getBody(), true);

            $this->assertEquals(200, $data['response']['statusCode']);

            $this->assertArrayHasKey('result', $data['response']);
  
        
    }

    
    public function testGetCustomerSuccessfully() //get customer by Id
    {
       
        $id = 1;
        
        $headers = [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->token,            
            'Accept'     => 'application/json',
            
        ];

        $response = $this->client->request('GET','customers/'. $id, [ 
            'http_errors' => false,       
            'headers' => $headers           
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);
         
        $data = array();
        $data = $response['response'];
        
        $this->assertArrayHasKey('first_name', $data['result']);
        $this->assertArrayHasKey('last_name', $data['result']);
        $this->assertArrayHasKey('email', $data['result']);
        $this->assertArrayHasKey('created_at', $data['result']);
        $this->assertArrayHasKey('updated_at', $data['result']);
        $this->assertEquals('Alessandro', $data['result']['first_name']);
        $this->assertEquals('Tara', $data['result']['last_name']);
        
    }

    public function testUpdateCustomerWithOnlyOneParameterSuccessfully() 
    {
        
        $id = 3;
        
        $headers = [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->token,            
            'Accept'     => 'application/json',
            
        ];

        $json = [
            	
            'last_name' => "Fornara"
            
        ];

        $response = $this->client->request('PUT','customers/'. $id, [  
            'http_errors' => false,      
            'headers' => $headers ,
            'json' => $json           
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);
         
        $data = array();
        $data = $response['response'];
        
        $this->assertEquals(200, $data['statusCode']);
        $this->assertEquals('Updated successfully', $data['result']);
        
        
    }

    public function testUpdateCustomerThrowErrorIfCreatedDateParameterIsNotADateFormat() 
    {
        
        $id = 3;
        
        $headers = [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->token,            
            'Accept'     => 'application/json',
            
        ];

        $json = [
            	
            'created_at' => "11111111-07-29"
            
        ];

        $response = $this->client->request('PUT','customers/'. $id, [   
            'http_errors' => false,     
            'headers' => $headers ,
            'json' => $json           
        ]);

        $this->assertEquals(400, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);
         
        $data = array();
        $data = $response['error'];
        
        $this->assertEquals(400, $data['statusCode']);
        $this->assertEquals('Datatype not valid for created_at. Should be a Y-m-d format', $data['message']);
        
        
    }

    public function testGetCustomerThrowErrorParameterCustomerIdNotValid() 
    {
        
        $id = 'stringId';
        
        $headers = [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->token,            
            'Accept'  => 'application/json',
            
        ];

        $response = $this->client->request('GET','customers/'. $id, [        
            'headers' => $headers,
            'http_errors' => false           
        ]);

        $this->assertEquals(400, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);
         
        $data = array();
        $data = $response['error'];
        
        $this->assertEquals(400, $data['statusCode']);
        $this->assertEquals('Datatype not valid for customerId. Should be numeric', $data['message']);
                
    }

    public function testGetCustomerThrowErrorIfCustomerIdNotFound() 
    {
        
        $id = 174;
        
        $headers = [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->token,            
            'Accept'  => 'application/json',
            
        ];

        $response = $this->client->request('GET','customers/'. $id, [        
            'headers' => $headers,
            'http_errors' => false           
        ]);

        $this->assertEquals(404, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);
         
        $data = array();
        $data = $response['error'];
        
        $this->assertEquals(404, $data['statusCode']);
        $this->assertEquals('Customer not found', $data['message']);
                
    }

    public function testCreateCustomerShouldThrowErrorIfEmailAlreadyExists() 
    {
    
        try 
        {  
            
            $response = $this->client->request('POST', 'customers', [

                'http_errors' => false ,
                
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
    
                ],

                'json' => [
                    'first_name'    => "Marlon",
                    'last_name'     => "Brando",
                    'email'    => "marlonbrando@gmail.com"
                ]

            ]);

            $this->assertEquals(409, $response->getStatusCode());

            $response = json_decode($response->getBody(), true);
         
            $data = array();
            $data = $response['error'];
            
            $this->assertEquals(409, $data['statusCode']);

            $this->assertEquals("This email already exists", $data['message']);
        }
        catch (exception $e) 
        { 
            if ($e->getMessage()) 
            {
                print_r($e->getMessage()); 
                die();
            }
        }             
    
    }

    public function testCreateCustomerShouldThrowErrorIfFirstNameParameterMissing() 
    {
        
       
                       
            $response = $this->client->request('POST', 'customers', [
                
                'http_errors' => false ,

                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
    
                ],

                'json' => [ 
                    'first_name'     => "",                   
                    'last_name'     => "Brando",
                    'email'    => "marlonbrando@gmail.com"
                ]

            ]);

            $this->assertEquals(422, $response->getStatusCode());

            $response = json_decode($response->getBody(), true);
         
            $data = array();
            $data = $response['error'];
            
            $this->assertEquals(422, $data['statusCode']);

            $this->assertEquals("first_name parameter is required", $data['message']);
          
    
    }
    
    public function testCreateCustomerShouldThrowErrorIfParameterFirstNameNotString() 
    {
        
                               
            $response = $this->client->request('POST', 'customers', [
                
                'http_errors' => false ,
                
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
    
                ],

                'json' => [ 
                    'first_name'     => 4027,86,                   
                    'last_name'     => "Brando",
                    'email'    => "marlonbrando@gmail.com"
                ]

            ]);

            $this->assertEquals(400, $response->getStatusCode());

            $response = json_decode($response->getBody(), true);
         
            $data = array();
            $data = $response['error'];
            
            $this->assertEquals(400, $data['statusCode']);

            $this->assertEquals("Datatype not valid for first_name. Should be string", $data['message']);
      
              
    
    }
    public function testCreateCustomerShouldThrowErrorIfEmailHasNotValidFormat() 
    {   
                     
            $response = $this->client->request('POST', 'customers', [

                'http_errors' => false ,
                
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
    
                ],

                'json' => [
                    'first_name'   => "Marlon",
                    'last_name'  => "Brando",
                    'email'    => "marlonbrando##gmail.com"
                ]

            ]);

            $this->assertEquals(400, $response->getStatusCode());

            $response = json_decode($response->getBody(), true);
         
            $data = array();
            $data = $response['error'];
            
            $this->assertEquals(400, $data['statusCode']);

            $this->assertEquals("Datatype not valid for email. Should be a valid email", $data['message']);
             
    
    }

    public function testGenerateTokenThrowErrorIfUserNotRegistered()
    {
        //credentials for a non registered user in database 
        $email = 'richard@gmail.com';
        $password = '147123456';        
       
        $response = $this->client->request('POST', 'customers', [
                  
                  'http_errors' => false ,
                  'headers' => [
                      'Accept'     => 'application/json',
                      'Content-Type' => 'application/json'
                  ],
                  'json' => [
                      'service'  =>'generateToken',	
                      'email'    => $email,
                      'password' => $password,
                  ]
              ]);             
                         
              $this->assertEquals(401, $response->getStatusCode());

              $response = json_decode($response->getBody(), true);
           
              $data = array();
              $data = $response['error'];
              
              $this->assertEquals(401, $data['statusCode']);
  
              $this->assertEquals("Email or Password incorrect", $data['message']);
            
    }
      
    
} 
     
