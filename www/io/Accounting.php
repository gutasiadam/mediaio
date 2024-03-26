<?php
/** Logs user activity and tracks u
 * ser interactions throughout the webapp */
namespace Mediaio;
require_once 'Database.php';
use Mediaio\Database;
class Accounting {
    static function logEvent($userID, $event, $optionalJSONData=NULL){
        
        //Encode JSON data to make it SQL compatible
        $optionalJSONData = json_encode($optionalJSONData);

        //Write to log file
        $logFile = fopen("./log.txt", "a");
        fwrite($logFile, "User: ".$userID." Event: ".$event." Data: ".json_encode($optionalJSONData)."\n");

        $connection = Database::runQuery_mysqli();
        $stmt = mysqli_stmt_init($connection);

        $sql="INSERT INTO log (UserID, Action,Data) VALUES
        (?,?,?)";
        if (!mysqli_stmt_prepare($stmt, $sql)){
            return array('code' => '500');
        }
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $userID,$event, $optionalJSONData);
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            return array('code' => '200');
        }else{
            return array('code' => '401');
        }

        

        $stmt->close();
    }

}

?>