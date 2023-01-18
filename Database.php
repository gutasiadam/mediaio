<?php
namespace Mediaio;

class Database{
    private const username = 'root';
    private const password = 'umvHVAZ%';
    private const schema = 'mediaio';

    static function runQuery($query){
        
        /* Runs an SQL query on the databse, and returns it's result. 
            - Doesn't check the query, it blindly runs it !
            - Doesn't close the databse connection either.
        */
        $connection = mysqli_connect('localhost', self::username, self::password, self::schema);
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
        $connection = mysqli_connect('localhost', self::username, self::password, self::schema);
        if (!$connection){
            die("Connection failed: ".mysqli_connect_error());
        }
        return $connection;
    }
}
?>