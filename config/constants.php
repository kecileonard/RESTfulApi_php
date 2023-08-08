<?php 

	/*Security key for payload to generate token*/
	define('SECRETE_KEY', 'leonard123456');

	/*Used for the switch case in validateParameter function of Customer Controller*/
	
	define('INTEGER', 	'1');
	define('STRING', 	'2');
    define('EMAIL', 	'3');
	define('DATE', 	    '4');
	

	/* Error Codes definition */	
    define('VALIDATE_PARAMETER_REQUIRED', 			422);
	define('VALIDATE_PARAMETER_DATATYPE', 			400); //or bad request 
	
	define('UNAUTHORIZED',        	 				401);
	define('SUCCESS_RESPONSE', 						200);
	define('CREATED_SUCCESSFULLY',					201);
	define('NOT_FOUND', 			     			404);
	define('NO_CONTENT', 			     			204);
	define('METHOD_NOT_ALLOWED',	     			405);
	define('CONFLICT',	     			            409);
	define('INTERNAL_SERVER_ERROR',		            500);
	define('JWT_PROCESSING_ERROR',	   			    300);
	define('INVALID_TOKEN',					        498);
		
		
    // show error reporting
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
  
	function dd($data)
	{        
    	echo  var_export($data,true);
    	die();
	}
	
?>
