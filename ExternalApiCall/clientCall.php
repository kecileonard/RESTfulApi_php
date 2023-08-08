<?php

$token = obtainToken();

//createCustomer($token);
getAllCustomers($token);
getCustomer($token, 1); //by id=1
updateCustomer($token,21); //by id=21
//deleteCustomer($token,34); 


function obtainToken()
{  
    $curl = curl_init();
    $request = '{
					        "service":"generateToken",					
					        "email":"leonard@gmail.com",
					        "password":"123456"
				        }';

    curl_setopt($curl, CURLOPT_URL, 'http://localhost/restful_api_php/customers');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['content-type: application/json']);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    
    $err = curl_error($curl);
    if ($err) 
    {
        echo 'Curl Error: ' . $err;
    }
    else 
    {
        header('content-type: application/json');
        $response = json_decode($result, true);
  
        $token = $response['response']['result']['token'];
        
        echo "The generated token is :" . $token;
        
        echo "\n\n";

        //the token to use in all the API requests
        return $token;
    }
}    

function getAllCustomers($token) 
{
        
	      $curl = curl_init();
		
        curl_setopt_array($curl, array(
        
        CURLOPT_URL => "http://localhost/restful_api_php/customers/" ,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
              "authorization: Bearer $token",
              "content-type: application/json",
            ),
      ));

      $response = curl_exec($curl);

      $err = curl_error($curl);

      
      if ($err) 
      {
        echo "cURL Error #:" . $err;
      }
      else
      {          
                   
          echo "\n\n";
          echo "All customers :" . "\n\n" . $response;          
          echo "\n\n";
          echo "\n\n";         
                   
          
      }

}


function getCustomer($token, $id) 
{

    echo "Customer with id " .$id .":";

    echo "\n\n";
            
	  $curl = curl_init();
		
    $id = 1;
    curl_setopt_array($curl, array(
        
    CURLOPT_URL => "http://localhost/restful_api_php/customers/" . $id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
              "authorization: Bearer $token",
              "content-type: application/json",
            ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    if ($err) 
    {
       echo "cURL Error #:" . $err;
    }
    else
    {   
        echo $response;
    }

    echo " : \n";
    echo " : \n";

          
}

function createCustomer($token) 
{        
      
	      $curl = curl_init();

        $request = '{
                       "first_name":"Aberto",
                       "last_name":"Morava",
                       "email":"albertomorava@gmail.com"                      
				            }';
		
        curl_setopt_array($curl, array(
        
        CURLOPT_URL => "http://localhost/restful_api_php/customers/" ,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $request,
        CURLOPT_HTTPHEADER => array(
              "authorization: Bearer $token",
              "content-type: application/json",
            ),
      ));

      $response = curl_exec($curl);

      $err = curl_error($curl);

      
      if ($err) 
      {
        echo "cURL Error #:" . $err;
      }
      else
      {          
         
          echo "\n\n";
          echo "All customers :" . "\n\n" . $response;          
          echo "\n\n";
          echo "\n\n";
                   
         
      }

}

function updateCustomer($token, $id) 
{
        
        
	    $curl = curl_init();

        $request = '{                       
                       "last_name": "Ferrero"                                             
				    }';
		
        curl_setopt_array($curl, array(
        
        CURLOPT_URL => "http://localhost/restful_api_php/customers/". $id , 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => $request,
        CURLOPT_HTTPHEADER => array(
              "authorization: Bearer $token",
              "content-type: application/json",
            ),
      ));

      $response = curl_exec($curl);

      $err = curl_error($curl);

      
      if ($err) 
      {
        echo "cURL Error #:" . $err;
      }
      else
      {          
         
          echo "\n\n";
          echo "Updated customer :" . "\n\n" . $response;          
          echo "\n\n";
          echo "\n\n";
                
         
      }

}


function deleteCustomer($token, $id) 
{
               
	      $curl = curl_init();
		
        curl_setopt_array($curl, array(
        
        CURLOPT_URL => "http://localhost/restful_api_php/customers/". $id , 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "DELETE",        
        CURLOPT_HTTPHEADER => array(
              "authorization: Bearer $token",
              "content-type: application/json",
            ),
      ));

      $response = curl_exec($curl);

      $err = curl_error($curl);
      
      if ($err) 
      {
        echo "cURL Error #:" . $err;
      }
      else
      {          
         
          echo "\n\n";
          echo "All customers after the deleted request:" . "\n\n" . $response;          
          echo "\n\n";
          echo "\n\n";
                 
         
      }

}

?>