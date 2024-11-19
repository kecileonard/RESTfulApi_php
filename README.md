### Restful Api Customer ManagementSystem
The RESTful API service maintains a list of customers with some information fields about them .
The customer fields are first_name , last_name , email, created_at (Registration date),
updated_at . These fields are stored in the customers table of database restful_api_php.
The API Endpoints are:
#### GET /customers: List all customers
#### POST /customers: Create a new customer
#### GET /customers/{id}: Retrieve a single customer
#### PUT /customers/{id}: Update a customer
#### DELETE /customers/{id}: Delete a customer
This Api is not public . The Users needs to be registered and authenticated to access the Api
resources . In order to protect the API endpoints is implemented the JWT  authentication .
This Api server does not keep any client state . No use of sessions or cookies .
So , the server replies to each user request as if it was the first request the client has made .
The customers data fields will be json encoded and represented to the frontend in the form of
json arrays , so the output response of the client requests will be a json format .
The output of the API response will be a json array with a key 'error'/'success' and value an array of elements with key
'statusCode' having for value an integer and key 'message' with value a string data type .
The generateToken function will be used to generate the JWT Api . This function will be
accessed by making  a post request to the endpoint . Also is necessary to install through
composer the library firebase/php-jwt": "^5.0.0 ".
The client will use the generated token for each of the (GET, POST, PUT, PATCH ,DELETE) requests .
The Postman tool is used to simulate an HTTP client which sends an authorized HTTP request
to the system. To enable requests with authorization for all the HTTP methods , is necessary to
set the Authorization in Postman Headers with the Bearer value the value of the token
generated previously. A variable 'jwt_token' was set globaly and this variable will be recognized by all the
HTTP request with authorization .

### Project Directory
The tools XAMPP Apache , PHP, and Mysql ,PHPUnit framework , firebase/php-jwt": "^5.0.0 " library , Postman are used and set up in Windows system .
Under the server's htdocs folder of XAMPP was created a project root directory called restful_api_php .

### Base URL
The base URL for all API requests is : http://localhost/restful_api_php/  

### User Registration
The users that are not registered so not present in database can not access the system . Once the users are
registered and inserted in database they are authenticated to access the system through Json Web Token authentication .
For more info please refer and read the RESTfulAPI_description.pdf file located inside 
postmanDocumentation folder of this repository .  

## PHP Unit Tests:
PHPUnit framework used to create unit tests for the API.

### Error Handling:
The error handling for the API is also implemented . The API returns the appropriate status
codes and error messages for different types of errors, such as record not found, invalid data,
server errors, etc.

### Documentation:
All the API endpoints, parameters, and error codes, are documented inside RESTfulAPI_description.pdf located in
postmanDocumentation folder of this repository along with description setup 
necessary to run the code.

