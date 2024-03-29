<?php
namespace Mediaio;

class Database{


    static function runQuery($query){
        // Specify the path to the JSON file
            $file_path = $_SERVER["DOCUMENT_ROOT"].'/server/dbCredentials.json';
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
        $connection = mysqli_connect('mysql', $username, $password, $schema);
		mysqli_set_charset($connection,"utf8mb4");
        if (!$connection){
		echo "Errored";
            die("Connection failed: ".mysqli_connect_error());
        }
        //$connection->close();
        return $connection->query($query);
    }

    //Runs query, and returns the mysqli object as a result.

    //Caller object SHOULD close the connection!
    static function runQuery_mysqli(){
		
            $file_path = $_SERVER["DOCUMENT_ROOT"].'/server/dbCredentials.json';

            // Read the contents of the file
            $json_data = file_get_contents($file_path);

            // Decode the JSON data into an associative array
            $credentials = json_decode($json_data, true);

            // Extract the username, password, and schema fields
            $username = $credentials['username'];
            $password = $credentials['password'];
            $schema = $credentials['schema'];
            
        $connection = mysqli_connect('mysql', $username, $password, $schema);
		mysqli_set_charset($connection,"utf8mb4");
        if (!$connection){
            die("Connection failed: ".mysqli_connect_error());
        }
        return $connection;
    }
}
?>
