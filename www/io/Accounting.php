<?php
/** Logs user activity and tracks u
 * ser interactions throughout the webapp */
namespace Mediaio;

require_once 'Database.php';
use Mediaio\Database;

class Accounting
{
    static function logEvent($userID, $event, $optionalJSONData = NULL)
    {

        //Encode JSON data to make it SQL compatible
        $optionalJSONData = json_encode($optionalJSONData);

        //Write to log file
        $logFile = fopen("./log.txt", "a");
        fwrite($logFile, "User: " . $userID . " Event: " . $event . " Data: " . json_encode($optionalJSONData) . "\n");

        $connection = Database::runQuery_mysqli();
        $stmt = mysqli_stmt_init($connection);

        $sql = "INSERT INTO log (UserID, Action,Data) VALUES
        (?,?,?)";
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return array('code' => '500');
        }
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $userID, $event, $optionalJSONData);
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            $connection->close();
            return array('code' => '200');
        } else {
            $connection->close();
            return array('code' => '401');
        }

    }

}


class userManager
{
    static function getPublicUserInfo()
    {
        $sql = "SELECT `idUsers`, `usernameUsers`, `firstName`, `lastName`, `teleNum`, `emailUsers` FROM `users`;";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();

        if ($result->num_rows > 0) {
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return json_encode($users);
        } else {
            return 500;
        }

    }
}


if (isset($_POST['mode'])) {
    switch ($_POST['mode']) {
        case 'getPublicUserInfo':
            echo userManager::getPublicUserInfo();
            break;

    }
}