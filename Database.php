<?php
namespace Mediaio;

class Database{


    static function runQuery($query){
        // Specify the path to the JSON file
            $file_path = __DIR__.'/server/dbCredentials.json';

            // Read the contents of the file
            $json_data = file_get_contents($file_path);

            // Decode the JSON data into an associative array
            $credentials = json_decode($json_data, true);

            // Extract the username, password, and schema fields
            $username = $credentials['username'];
            $password = $credentials['password'];
            $schema = $credentials['schema'];

        /* Runs an SQL query on the databse, and returns it's result. 
            - Doesn't check the query, it blindly runs it !
            - Doesn't close the databse connection either.
        */
        $connection = mysqli_connect('localhost', $username, $password, $schema);
        if (!$connection){
            die("Connection failed: ".mysqli_connect_error());
        }
        //$connection->close();
        return $connection->query($query);
        /*$statement = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($statement, $query)){
            return $query;
            exit();
        }else{
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        
        return $result;
        }*/
    }

    //Runs query, and returns the mysqli object as a result.

    //Caller object SHOULD close the connection!
    static function runQuery_mysqli(){

                    $file_path = __DIR__.'/server/dbCredentials.json';

            // Read the contents of the file
            $json_data = file_get_contents($file_path);

            // Decode the JSON data into an associative array
            $credentials = json_decode($json_data, true);

            // Extract the username, password, and schema fields
            $username = $credentials['username'];
            $password = $credentials['password'];
            $schema = $credentials['schema'];
            
        $connection = mysqli_connect('localhost', $username, $password, $schema);
        if (!$connection){
            die("Connection failed: ".mysqli_connect_error());
        }
        return $connection;
    }
}
?>