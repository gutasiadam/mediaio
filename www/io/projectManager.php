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

            echo $id;
        }
    }

    static function deleteProject()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "DELETE FROM projects WHERE ID=" . $_POST['id'] . ";";
            $connection = Database::runQuery_mysqli();
            $connection->query($sql);
            $connection->close();
            echo 1;
            exit();
        }
    }

    static function listProjects()
    {
        $sql = "SELECT * FROM projects;";       //TODO: Add a filter to show only the projects that are supposed to be shown to a user.
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

    static function getProjectSettings()
    {
        $sql = "SELECT * FROM projects WHERE ID=" . $_POST['id'] . ";";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $row = $result->fetch_assoc();
        if ($row == null) {
            echo 404;
            exit();
        }
        echo (json_encode($row));
        exit();
    }

    static function getProjectTasks()
    {
        $sql = "SELECT * FROM project_components WHERE projectId=" . $_POST['id'] . ";";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        if ($rows == null) {
            echo 404;
            exit();
        }
        echo (json_encode($rows));
        exit();
    }

    static function createNewTask()
    {
        $settings = json_decode($_POST['task'], true);

        if ($settings['Deadline'] != "NULL") {
            $sql = "INSERT INTO `project_components` (`ProjectId`, `Task_type`, `Task_title`, `Task_data`, `Task_members`, `Deadline`) VALUES ('" . $settings['ProjectId'] . "','" . $settings['Task_type'] . "','" . $settings['Task_title'] . "','" . $settings['Task_data'] . "',NULL,'" . $settings['Deadline'] . "');";
        } else {
            $sql = "INSERT INTO `project_components` (`ProjectId`, `Task_type`, `Task_title`, `Task_data`, `Task_members`, `Deadline`) VALUES ('" . $settings['ProjectId'] . "','" . $settings['Task_type'] . "','" . $settings['Task_title'] . "','" . $settings['Task_data'] . "',NULL,NULL);";
        }
        $connection = Database::runQuery_mysqli();
        $connection->query($sql);
        $connection->close();
        echo 200;
        exit();
    }

    static function saveProjectSettings()
    {
        if (in_array("admin", $_SESSION['groups'])) {

            $settings = json_decode($_POST['settings'], true);

            // Convert Members to a string of comma-separated numbers
            $members = implode(",", $settings['Members']);

            $connection = Database::runQuery_mysqli();

            if ($settings['Deadline'] == "NULL") {
                $sql = "UPDATE projects SET Name=?, Members=?, Deadline=NULL, Visibility_group=? WHERE ID=?";
            } else {
                $sql = "UPDATE projects SET Name=?, Members=?, Deadline=?, Visibility_group=? WHERE ID=?";
            }

            $stmt = $connection->prepare($sql);
            if ($settings['Deadline'] == "NULL") {
                $stmt->bind_param("sssi", $settings['Name'], $members, $settings['Visibility_group'], $_POST['id']);
            } else {
                $stmt->bind_param("ssssi", $settings['Name'], $members, $settings['Deadline'], $settings['Visibility_group'], $_POST['id']);
            }

            $stmt->execute();
            $stmt->close();
            $connection->close();
            echo 1;
            exit();
        }
    }

    static function saveDescription()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "UPDATE projects SET Description='" . $_POST['description'] . "' WHERE ID=" . $_POST['id'] . ";";
            $connection = Database::runQuery_mysqli();
            $connection->query($sql);
            $connection->close();
            echo 1;
            exit();
        }
    }

    // Functions for users

    static function getUsers()
    {
        $sql = "SELECT `idUsers`, `firstName`, `lastName` FROM `users`;";
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
}

if (isset ($_POST['mode'])) {
    //Set timezone to the computer's timezone.
    date_default_timezone_set('Europe/Budapest');

    switch ($_POST['mode']) {
        case 'createNewProject':
            echo projectManager::createNewProject();
            break;
        case 'deleteProject':
            echo projectManager::deleteProject();
            break;
        case 'listProjects':
            echo projectManager::listProjects();
            break;

        case 'getProjectTasks':
            echo projectManager::getProjectTasks();
            break;
        case 'createNewTask':
            echo projectManager::createNewTask();
            break;

        case 'saveProjectSettings':
            echo projectManager::saveProjectSettings();
            break;
        case 'saveDescription':
            echo projectManager::saveDescription();
            break;
        case 'getProjectSettings':
            echo projectManager::getProjectSettings();
            break;

        case 'getUsers':
            echo projectManager::getUsers();
            break;
    }
    exit();
}