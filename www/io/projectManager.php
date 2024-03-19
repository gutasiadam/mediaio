<?php
namespace Mediaio;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';
use Mediaio\Core;
use Mediaio\Database;

error_reporting(E_ERROR | E_PARSE);

session_start();


class projectManager
{
    static function createNewProject()
    {
        if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
            $sql = "INSERT INTO `projects`(`ID`, `Name`, `Description`, `Members`, `Deadline`) VALUES (NULL,'Névtelen','Leírás...',NULL,NULL);";
            $connection = Database::runQuery_mysqli();
            $connection->query($sql);
            $id = $connection->insert_id;
            $connection->close();

            // Create a new table for the project
            $sql = "CREATE TABLE `project_" . $id . "` (ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, Name VARCHAR(30) NOT NULL, Description VARCHAR(100), Members VARCHAR(100), Deadline DATE);";
            $connection = Database::runProjectQuery_mysqli();
            $connection->query($sql);
            $connection->close();

            echo $id;
            echo 200;
        }
    }

    static function listProjects()
    {
        $sql = "SELECT * FROM projects;";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $resultItems = array();
        while ($row = $result->fetch_assoc()) {
            $resultItems[] = $row;
        }
        echo (json_encode($resultItems));
        exit();
    }

    static function getProject()
    {
        $sql = "SELECT * FROM projects WHERE ID=" . $_POST['id'];
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $row = $result->fetch_assoc();
        echo (json_encode($row));
        exit();
    }

    static function updateProject()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $projectName = $_POST['projectName'];
            $projectDescription = $_POST['projectDescription'];
            $projectMembers = $_POST['projectMembers'];
            $projectDeadline = $_POST['projectDeadline'];
            $sql = "UPDATE projects SET Name='" . $projectName . "', Description='" . $projectDescription . "', Members='" . $projectMembers . "', Deadline='" . $projectDeadline . "' WHERE ID=" . $_POST['id'];
            $connection = Database::runQuery_mysqli();
            $connection->query($sql);
            $connection->close();
            echo 1;
            exit();
        }
    }
}

if (isset ($_POST['mode'])) {
    //Set timezone to the computer's timezone.
    date_default_timezone_set('Europe/Budapest');

    switch ($_POST['mode']) {
        case 'createNewProject':
            echo projectManager::createNewProject();
            break;
        case 'listProjects':
            echo projectManager::listProjects();
            break;
        case 'getProject':
            echo projectManager::getProject();
            break;
        case 'updateProject':
            echo projectManager::updateProject();
            break;
    }
    exit();
}