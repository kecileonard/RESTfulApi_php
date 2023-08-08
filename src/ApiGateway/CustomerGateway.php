<?php

class CustomerGateway
{
    
    private $conn=null;
    private $table_name = "customers";     
    private $id;
    private $userId;
    private $first_name;
    private $last_name;
    private $email;
    private $created_at;
    private $updated_at;

   
    public function getId() { return $this->id; }    
    public function setId($id) { $this->id = $id; }    
    public function getFirstName() {return $this->first_name; }    
    public function setFirstName($firstName) { $this->first_name = $firstName; }  
    public function getLastName() { return $this->last_name; }    
    public function setLastName($lastName) { $this->last_name = $lastName; }
    public function getEmail() { return $this->email; }    
    public function setEmail($email) { $this->email = $email; }
    public function getCreatedAt() { return $this->created_at; }    
    public function setCreatedAt($createdAt) { $this->created_at = $createdAt; }    
    public function getUpdatedAt() { return $this->updated_at; }    
    public function setUpdatedAt($updatedAt) { $this->updated_at = $updatedAt; }
    public function getUserId() { return $this->userId; }    
    public function setUserId($userId) { $this->userId = $userId; }

    public function __construct($db)
    {
        $this->conn = $db;       
    }

    public function findAll()
    {       
       
        $query = 'SELECT id,
                    first_name,
                    last_name,
                    email,
                    created_at,
                    updated_at 
                  FROM  ' . $this->table_name . '  
                  ORDER BY id';

        try 
        {
            $stmt = $this->conn->prepare($query);
            
            $stmt->execute();
            
            $num = $stmt->rowCount();
             
            $result = array();

            if ($num > 0)
            {                
               
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    extract($row);

                    $customer_row=array(
                        "id" => $id,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "email" => $email,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
              
                    array_push($result, $customer_row);
                }

            }

            return $result;
                       
        }
        catch (PDOException $e)
        {
            $this->InternalServerError();
            exit;
            
        }
            

    }
    
    public function checkEmail($email)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . "  WHERE email='".$email."'";

        try
        {
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if there is a result and response to  1 if email is existing
            return (is_array($result) && count($result) > 0);
        }
        catch (PDOException $e) 
        {
            $this->InternalServerError();
           
        }

    }
    
    public function create()
    {
        $sql = "INSERT INTO " . $this->table_name . " (first_name, last_name, email, user_id , created_at)
                VALUES (:first_name, :last_name, :email, :user_id, :created_at)";

        try
        {
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(":first_name", $this->first_name);
            $stmt->bindParam(":last_name", $this->last_name);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(':user_id', $this->userId);
            $stmt->bindParam(":created_at", $this->created_at);
            $stmt->execute();
            return true;
        }
        catch (PDOException $e) 
        {
            $this->InternalServerError();
           
        }    
         
    }
    
    public function find(string $id)
    {       

        $statement = "
            SELECT 
                id, first_name, last_name, email, created_at,updated_at
                FROM
                " . $this->table_name . " 
            WHERE id = :id;
            ";

        try
        {
            $statement = $this->conn->prepare($statement);
            $statement->execute(array(':id' =>$id));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
        catch (PDOException $e) 
        {
            $this->InternalServerError();
            
        }    
       

    }
    
    public function update($oldData, array $newData)
    {
                
        $sql = "
            UPDATE  " . $this->table_name . " 
            SET 
                first_name = :first_name,
                last_name  = :last_name,
                email = :email,
                created_at = :created_at,
                updated_at = :updated_at
            WHERE id = :id;
        ";
         
        $stmt = $this->conn->prepare($sql);        
        
        $first_name = isset($newData['first_name']) ? $newData['first_name'] : $oldData['first_name']; 
        $last_name = isset($newData['last_name']) ? $newData['last_name'] : $oldData['last_name']; 
        $email = isset($newData['email']) ? $newData['email'] : $oldData['email']; 
        $created_at = isset($newData['created_at']) ? $newData['created_at'] : $oldData['created_at'];
        $updated_at = $newData['updated_at'];
        $id = $oldData['id'];
              
        
        try 
        {
            
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":first_name", $first_name );
            $stmt->bindParam(":last_name", $last_name );
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":created_at", $created_at);
            $stmt->bindParam(":updated_at", $updated_at);
            
            $stmt->execute();            

            return true;

        }
        catch(PDOException $e)
        {
            $this->InternalServerError();
                       
        }        
                 
    }
    
    public function delete(string $id)
    {
        
        $sql = " 
                DELETE  FROM " . $this->table_name . " 
                WHERE id = :id";
                
        try
        {

            $stmt = $this->conn->prepare($sql);           
            $stmt->bindParam(":id", $id);            
            $stmt->execute();
            return true;
        }
        catch(PDOException $e)
        {     
            $this->InternalServerError();                      
        }      
    }
    public function InternalServerError() 
    {      
        $statusCodeHeader = 'HTTP/1.1 500 Internal server error';
        header($statusCodeHeader);           
        $message = "INTERNAL SERVER ERROR";
        $code = INTERNAL_SERVER_ERROR;
        $errorMessage = json_encode(['error' => ['statusCode'=>$code, 'message'=>$message]]);
        echo $errorMessage;
        exit;
    }

    
}





























