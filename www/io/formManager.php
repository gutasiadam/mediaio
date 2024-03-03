<?php
namespace Mediaio;

//require 'vendor/mk-j/php_xlsxwriter/xlsxwriter.class.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';
use Mediaio\Core;
use Mediaio\Database;

error_reporting(E_ERROR | E_PARSE);

session_start();


class formManager
{
  static function createNewForm()
  {
    if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
      $sql = "INSERT INTO `forms`(`ID`, `Name`, `Header`, `Status`, `Anonim`, `AccessRestrict`, `Data`) VALUES(NULL,'Névtelen','Leírás','0','0','1',NULL);";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $id = $connection->insert_id;
      $connection->close();
      echo $id;
      exit();
    }
  }

  static function listForms()
  {
    $sql = "SELECT * FROM forms WHERE AccessRestrict='0';";
    if (isset($_SESSION['userId'])) {
      $sql = "SELECT * FROM forms WHERE AccessRestrict IN ('0', '2');";
      if (in_array("admin", $_SESSION['groups'])) {
        $sql = "SELECT * FROM forms";
      }
    }
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $connection->close();
    $forms = array();
    while ($row = $result->fetch_assoc()) {
      $forms[] = $row;
    }
    echo json_encode($forms);
    exit();
  }

  static function saveForm($form, $formHeader, $id, $accessRestrict, $formAnonim, $formSingleAnswer, $formState)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      //convert to json
      $form = json_decode($form, true);
      $accessArray = array("restrictGroups" => array($accessRestrict));
      // echo $accessRestrict;
      // var_dump($_POST);
      //array_push($accessArray,$accessRestrict);



      $sql = "UPDATE forms SET Name='" . $form['name'] . "',Header='" . $formHeader . "',Status='" . $formState . "',Anonim='" . $formAnonim . "',Data='" . json_encode($form['elements'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "'
            ,Accessrestrict='" . $accessRestrict . "' WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
      exit();
    }
  }

  static function changeBackground($name, $id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "UPDATE forms SET Background='" . $name . "' WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
      exit();
    }
  }

  static function getForm($form, $id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "SELECT * FROM forms WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $result = $connection->query($sql);
      $connection->close();
      $row = $result->fetch_assoc();
      //If no rows are returned, return 404
      if ($row == null) {
        echo 404;
        exit();
      }
      echo json_encode($row);
      exit();
    }
  }

  static function viewForm($form, $id)
  {
    $sql = "SELECT * FROM forms WHERE ID=" . $id . ";";
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $connection->close();
    $row = $result->fetch_assoc();
    //If form is closed, return 500
    if (isset($row['Status']) && $row['Status'] == 0) {
      echo 500;
      exit();
    }
    //If no rows are returned, return 404
    if ($row == null) {
      echo 404;
      exit();
    }
    echo json_encode($row);
    exit();

  }


  static function deleteForm($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "DELETE FROM forms WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
    }
  }

  static function submitAnswer($uid, $id, $ip, $answers)
  {
    // Prevent injection
    if (preg_match('/[<>]/', $answers)) {
      echo 500;
      exit();
    }
    $sql = "INSERT INTO `formanswers` (`ID`, `FormID`, `userID`, `userIp`, `UserAnswers`) VALUES (NULL,'" . $id . "','" . $uid . "','" . $ip . "','" . $answers . "');";
    $connection = Database::runQuery_mysqli();
    $connection->query($sql);
    $connection->close();
    echo 200;
    exit();
  }

  static function getFormAnswers($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "SELECT * FROM formanswers WHERE FormID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $result = $connection->query($sql);
      $connection->close();
      $answers = array();
      while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
      }
      echo json_encode($answers);
      exit();
    }
  }

  static function generateXlsx($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "SELECT * FROM formanswers WHERE FormID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $result = $connection->query($sql);
      $connection->close();
      $answers = array();
      while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
      }
      $data = array();
      $data[] = array('ID', 'FormID', 'userID', 'userIp', 'UserAnswers');
      foreach ($answers as $answer) {
        $data[] = array($answer['ID'], $answer['FormID'], $answer['userID'], $answer['userIp'], $answer['UserAnswers']);
      }
      $writer = new \XLSXWriter();
      $writer->writeSheet($data);
      $writer->writeToFile('output.xlsx');
      echo 200;
      exit();
    }
  }
}



if (isset($_POST['mode'])) {

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  if ($_POST['mode'] == 'createNewForm') {
    echo formManager::createNewForm();
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'listForms') {
    echo formManager::listForms();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'generateXlsx') {
    echo 'ASD';
    echo formManager::generateXlsx($_POST['id']);
    echo $_POST['value'];
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'save') {
    echo formManager::saveForm(
      $_POST['form'],
      $_POST['formHeader'],
      $_POST['id'],
      $_POST['accessRestrict'],
      $_POST['formAnonim'],
      $_POST['formSingleAnswer'],
      $_POST['formState'],
    );
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if ($_POST['mode'] == 'changeBackground') {
    echo formManager::changeBackground($_POST['name'], $_POST['id']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if ($_POST['mode'] == 'getForm') {
    echo formManager::getForm($_POST['form'], $_POST['id']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if ($_POST['mode'] == 'viewForm') {
    echo formManager::viewForm($_POST['form'], $_POST['id']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if ($_POST['mode'] == 'deleteForm') {
    echo "deleteForm";
    echo formManager::deleteForm($_POST['id']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if ($_POST['mode'] == 'submitAnswer') {
    echo formManager::submitAnswer($_POST['uid'], $_POST['id'], $_POST['userIp'], $_POST['answers']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if ($_POST['mode'] == 'getFormAnswers') {
    echo formManager::getFormAnswers($_POST['id']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
}

?>