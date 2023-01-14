<?php
namespace Mediaio;

class Database{
    private const username = 'root';
    private const password = 'umvHVAZ%';
    private const schema = 'mediaio';

    static function runQuery($query){
        
        /* Runs an SQL query on the databse, and returns it's result. 
            - Doesn't check the query, it blindly runs it !
        */
        $connection = mysqli_connect('localhost', self::username, self::password, self::schema);
        if (!$connection){
            die("Connection failed: ".mysqli_connect_error());
        }
        return $connection->query($query);
        /*$statement = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($statement, $query)){
            return $query;
            exit();
        }else{
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $connection->close();
        return $result;
        }*/
    }
}
?>