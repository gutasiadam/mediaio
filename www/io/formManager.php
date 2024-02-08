<?php
namespace Mediaio;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';
use Mediaio\Core;
use Mediaio\Database;

session_start();

class formManager
{
  static function createNewForm()
  {
    if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
      $sql = "INSERT INTO forms VALUES(NULL,NULL,'2',NULL,NULL);";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $id = $connection->insert_id;
      $connection->close();
      echo $id;
      exit();
    }
  }

  static function getEditingPhaseForms()
  {
    if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
      $sql = "SELECT * FROM forms WHERE Status='2'";
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
  }

  static function getPublicForms()
  {
    $sql = "SELECT ID,Name,AccessRestrict,Status FROM forms WHERE Status!='e' AND JSON_CONTAINS(AccessRestrict, '[\"public\"]')"; //AND Status='1'
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $connection->close();
    $forms = array();
    //echo $sql;
    while ($row = $result->fetch_assoc()) {
      $forms[] = $row;
    }
    echo json_encode($forms);
    exit();
  }

  static function getRestrictedForms()
  {
    if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
      $sql = "SELECT ID,Name,Status,AccessRestrict FROM forms WHERE Status!='e' AND NOT JSON_CONTAINS(AccessRestrict, '[\"public\"]')";
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
  }

  static function saveForm($form, $id, $accessRestrict, $formState)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      //convert to json
      $form = json_decode($form, true);
      $accessArray = array("restrictGroups" => array($accessRestrict));
      // echo $accessRestrict;
      // var_dump($_POST);
      //array_push($accessArray,$accessRestrict);



      $sql = "UPDATE forms SET Name='" . $form['name'] . "',Status='" . $_POST['formState'] . "',Data='" . json_encode($form['elements'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "'
            ,Accessrestrict='" . json_encode($accessArray['restrictGroups'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "' WHERE ID=" . $id . ";";
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

  static function submitAnswer($uid, $id, $answers)
  {
    $sql = "INSERT INTO `formanswers` (`ID`, `FormID`, `userID`, `UserAnswers`) VALUES (NULL,'" . $id . "','" . $uid . "','" . $answers . "');";
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
}



if (isset($_POST['mode'])) {

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  if ($_POST['mode'] == 'createNewForm') {
    echo formManager::createNewForm();
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'getRestrictedForms') {
    echo formManager::getRestrictedForms();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'getPublicForms') {
    echo formManager::getPublicForms();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'getEditingPhaseForms') {
    echo formManager::getEditingPhaseForms();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if ($_POST['mode'] == 'save') {
    echo formManager::saveForm(
      $_POST['form'],
      $_POST['id'],
      $_POST['accessRestrict'],
      $_POST['formState']
    );
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
    echo formManager::submitAnswer($_POST['uid'], $_POST['id'], $_POST['answers']);
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