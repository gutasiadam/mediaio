<?php
namespace Mediaio;

require_once "../../Database.php";

use Mediaio\Database;


error_reporting(E_ERROR | E_PARSE);

session_start();

class adminTools
{
    static function getUsers()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "SELECT * FROM users ORDER BY lastName, firstName";
            $connection = Database::runQuery_mysqli();
            $result = $connection->query($sql);
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return json_encode($users);

        } else {
            return 403;
        }
    }

    static function submitRoles($data)
    {
        if (in_array("admin", $_SESSION['groups'])) {

            $data = json_decode($data, true);
            $connection = Database::runQuery_mysqli();

            $sql = "UPDATE users SET AdditionalData = ? WHERE idUsers = ?";
            $stmt = $connection->prepare($sql);

            foreach ($data as $user) {
                $user['groups'] = json_encode(['groups' => $user['groups']], JSON_UNESCAPED_UNICODE);
                $stmt->bind_param("ss", $user['groups'], $user['userId']);
                $stmt->execute();
            }

            if ($stmt->affected_rows == 1) {
                $connection->close();
                return 200;
            } else {
                $connection->close();
                return 401;
            }

        } else {
            return 403;
        }
    }
}


if (isset($_POST['mode'])) {
    switch ($_POST['mode']) {
        case 'getUsers':
            echo adminTools::getUsers();
            break;
        case 'submitRoles':
            echo adminTools::submitRoles($_POST['data']);
            break;
        default:
            echo 404;
            break;
    }
    exit();
}