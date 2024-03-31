<?php
namespace Mediaio;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';
require_once __DIR__ . '/projectMailer.php';
use Mediaio\Core;
use Mediaio\Database;
use Mediaio\ProjectMailer;

error_reporting(E_ERROR | E_PARSE);

session_start();


class projectManager
{
    static function createNewProject()
    {
        if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
            $sql = "INSERT INTO `projects`(`ID`, `Name`, `Description`, `Deadline`) VALUES (NULL,'Névtelen','Leírás...',NULL);";
            $connection = Database::runQuery_mysqli();
            $connection->query($sql);
            $id = $connection->insert_id;
            $connection->close();

            echo $id;
        } else {
            echo 403;
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
        } else {
            echo 403;
        }
    }

    static function listProjects()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "SELECT * FROM projects;";
        } else if (in_array("média", $_SESSION['groups'])) {
            $sql = "SELECT * FROM projects WHERE Visibility_group IN (0,1);";
        } else if (in_array("studio", $_SESSION['groups'])) {
            $sql = "SELECT * FROM projects WHERE Visibility_group IN (0,2);";
        } else {
            $sql = "SELECT * FROM projects WHERE Visibility_group=0;";
        }
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

    // TASKS

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
    }

    static function getTask()
    {
        $sql = "SELECT * FROM project_components WHERE ID=" . $_POST['ID'] . ";";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $row = $result->fetch_assoc();
        if ($row == null) {
            echo 404;
            exit();
        }
        echo (json_encode($row));
    }

    static function getTaskCreator()
    {
        $sql = "SELECT `AddedByUID` FROM `project_components` WHERE `ID`=" . $_POST['ID'] . ";";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $row = $result->fetch_assoc();
        if ($row == null) {
            echo 404;
            exit();
        }
        echo (json_encode($row));
    }

    static function saveTask()
    {
        $settings = json_decode($_POST['task'], true);

        if ($settings['Task_type'] == "checklist" || $settings['Task_type'] == "radio") {
            $settings['Task_data'] = json_encode($settings['Task_data']);
        }

        $connection = Database::runQuery_mysqli();

        try {
            // Get current task members
            $sql = "SELECT * FROM `project_task_members` WHERE TaskId=" . $_POST['ID'] . ";";
            $result = $connection->query($sql);
            $currentMembers = array();
            while ($row = $result->fetch_assoc()) {
                $currentMembers[] = $row['UserId'];
            }

            $_POST['taskMembers'] = json_decode($_POST['taskMembers'], true);

            foreach ($_POST['taskMembers'] as $member) {
                // Check if the record already exists
                if (in_array($member, $currentMembers)) {
                    continue;
                }

                // If the record doesn't exist, insert it
                $sql = "INSERT INTO `project_task_members` (`ProjectId`,`TaskId`, `UserId`) VALUES (" . $settings['ProjectId'] . "," . $_POST['ID'] . "," . $member . ")";
                $connection->query($sql);

                // Send an email to the new member
                // You would need to implement the ProjectMailer::sendNewTaskMail method
                //try {
                //    ProjectMailer::sendNewTaskMail($_POST['ID'], $member);
                //} catch (\Exception $e) {
                //    // Do nothing
                //}
            }

            // Delete members that were removed
            $deletedMembers = array_diff($currentMembers, $_POST['taskMembers']);
            foreach ($deletedMembers as $member) {
                $sql = "DELETE FROM `project_task_members` WHERE `TaskId`=" . $_POST['ID'] . " AND `UserId`=" . $member . ";";
                $connection->query($sql);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        if ($settings['Deadline'] != "NULL") {
            $sql = "INSERT INTO `project_components` (`ID`, `ProjectId`, `Task_type`, `Task_title`, `Task_data`, `isInteractable`, `AddedByUID`, `Deadline`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE `Task_title`=?, `Task_data`=?, `Deadline`=?, `isInteractable`=?;";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iisssiissssi", $_POST['ID'], $settings['ProjectId'], $settings['Task_type'], $settings['Task_title'], $settings['Task_data'], $settings['isInteractable'], $_SESSION['userId'], $settings['Deadline'], $settings['Task_title'], $settings['Task_data'], $settings['Deadline'], $settings['isInteractable']);
        } else {
            $sql = "INSERT INTO `project_components` (`ID`, `ProjectId`, `Task_type`, `Task_title`, `Task_data`, `isInteractable`, `AddedByUID`, `Deadline`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NULL) 
                    ON DUPLICATE KEY UPDATE `Task_title`=?, `Task_data`=?, `Deadline`=NULL, `isInteractable`=?;";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iisssiissi", $_POST['ID'], $settings['ProjectId'], $settings['Task_type'], $settings['Task_title'], $settings['Task_data'], $settings['isInteractable'], $_SESSION['userId'], $settings['Task_title'], $settings['Task_data'], $settings['isInteractable']);
        }

        $stmt->execute();

        $connection->close();
        echo 200;
    }

    static function submitTask()
    {
        $connection = Database::runQuery_mysqli();

        // Getting project id for task
        $sql = "SELECT `ProjectId` FROM `project_components` WHERE `ID`=" . $_POST['ID'] . ";";
        $result = $connection->query($sql);
        $projectID = $result->fetch_assoc()['ProjectId'];

        $sql = "INSERT INTO `project_task_userdata` (`ProjectId`, `TaskId`, `UserId`, `Data`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `UserId`=?, `Data`=?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iiisis", $projectID, $_POST['ID'], $_SESSION['userId'], $_POST['task'], $_SESSION['userId'], $_POST['task']);

        $stmt->execute();

        $connection->close();
        echo 200;
    }

    static function getUIs()
    {
        $sql = "SELECT * FROM `project_task_userdata` WHERE `TaskId`=" . $_POST['id'] . ";";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        if ($rows == null) {
            echo 404;
            exit();
        }
        echo (json_encode($rows));
    }

    static function getUI()
    {
        $sql = "SELECT * FROM `project_task_userdata` WHERE `TaskId`=" . $_POST['ID'] . " AND `UserId`=" . $_SESSION['userId'] . ";";
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $row = $result->fetch_assoc();
        if ($row == null) {
            echo 404;
            exit();
        }
        echo (json_encode($row));

    }

    static function deleteTask()
    {
        $connection = Database::runQuery_mysqli();
        $sql = "DELETE FROM project_components WHERE ID=" . $_POST['ID'] . ";";
        $connection->query($sql);
        $connection->close();
        echo 200;
    }

    // PROJECT SETTINGS
    static function saveProjectSettings()
    {
        if (in_array("admin", $_SESSION['groups'])) {

            $settings = json_decode($_POST['settings'], true);

            $connection = Database::runQuery_mysqli();

            if ($settings['Deadline'] == "NULL") {
                $sql = "UPDATE projects SET Name=?, Deadline=NULL, Visibility_group=? WHERE ID=?";
            } else {
                $sql = "UPDATE projects SET Name=?, Deadline=?, Visibility_group=? WHERE ID=?";
            }

            $stmt = $connection->prepare($sql);
            if ($settings['Deadline'] == "NULL") {
                $stmt->bind_param("ssi", $settings['Name'], $settings['Visibility_group'], $_POST['id']);
            } else {
                $stmt->bind_param("sssi", $settings['Name'], $settings['Deadline'], $settings['Visibility_group'], $_POST['id']);
            }

            $stmt->execute();
            $stmt->close();
            $connection->close();
            echo 200;
            exit();
        } else {
            echo 403;
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
        if (isset($_POST['ID'])) {
            $sql = "SELECT `idUsers`, `firstName`, `lastName` FROM `users` WHERE `idUsers`=" . $_POST['ID'] . ";";
        } else {
            $sql = "SELECT `idUsers`, `firstName`, `lastName` FROM `users`;";
        }
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

    static function getTaskMembers()
    {
        $sql = "SELECT * FROM `project_task_members` WHERE TaskId=" . $_POST['id'] . ";";
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

    static function getProjectMembers()
    {
        $sql = "SELECT * FROM `project_members` WHERE ProjectID=" . $_POST['id'] . ";";
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

    static function saveProjectMembers()
    {
        // For every member in array add to database
        $members = json_decode($_POST['Members'], true);

        $connection = Database::runQuery_mysqli();
        // Get current members
        $sql = "SELECT * FROM `project_members` WHERE ProjectID=" . $_POST['id'] . ";";
        $result = $connection->query($sql);
        $currentMembers = array();
        while ($row = $result->fetch_assoc()) {
            $currentMembers[] = $row['UserID'];
        }

        //$deletedMembers = array_diff($currentMembers, $members);

        foreach ($members as $member) {
            // Check if the record already exists
            if (in_array($member, $currentMembers)) {
                continue;
            }

            // If the record doesn't exist, insert it
            $sql = "INSERT INTO `project_members` (`ProjectID`, `UserID`) VALUES (" . $_POST['id'] . "," . $member . ")";
            $connection->query($sql);

            // Send an email to the new member
            try {
                ProjectMailer::sendNewProjectMail($_POST['id'], $member);
            } catch (\Exception) {
                // Do nothing
            }
        }

        $connection->close();

        echo 200;
    }

    static function removeMemberFromProject()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "DELETE FROM project_members WHERE ProjectID=" . $_POST['projectId'] . " AND UserID=" . $_POST['userId'] . ";";
            $connection = Database::runQuery_mysqli();
            $connection->query($sql);
            $connection->close();
            echo 200;
            exit();
        } else {
            echo 403;
        }
    }
}

if (isset($_POST['mode'])) {
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

        case 'getTask':
            echo projectManager::getTask();
            break;

        case 'getTaskCreator':
            echo projectManager::getTaskCreator();
            break;

        case 'saveTask':
            echo projectManager::saveTask();
            break;
        case 'submitTask':
            echo projectManager::submitTask();
            break;

        case 'getUI':
            echo projectManager::getUI();
            break;
        case 'getUIs':
            echo projectManager::getUIs();
            break;
        case 'deleteTask':
            echo projectManager::deleteTask();
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
        case 'getTaskMembers':
            echo projectManager::getTaskMembers();
            break;
        case 'getProjectMembers':
            echo projectManager::getProjectMembers();
            break;
        case 'saveProjectMembers':
            echo projectManager::saveProjectMembers();
            break;
        case 'removeMemberFromProject':
            echo projectManager::removeMemberFromProject();
            break;
    }
    exit();
}