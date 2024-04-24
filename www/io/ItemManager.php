<?php

/**
 * ItemManager.php
 * Manages the takeout and retrieve processes, stats and user Lists.
 */

namespace Mediaio;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';

use Mediaio\Core;
use Mediaio\Database;


class takeOutManager
{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageTakeout($takeoutItems, $user)
  {
    //Accesses post and Session Data.
    //var_dump($takeoutData);
    $currDate = date("Y/m/d H:i:s");
    $connection = Database::runQuery_mysqli();

    //Logging into takelog

    //Auto accept available
    $takeOutAsUser = $user == '' ? $_SESSION['userId'] : $user;
    $acknowledged = in_array("admin", $_SESSION['groups']) ? 1 : 0; // Stageing happens here
    $ackBy = $acknowledged ? $_SESSION['UserUserName'] : NULL;

    $sql = "INSERT INTO takelog (`ID`, `Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
            VALUES (NULL, '$currDate', '$takeOutAsUser', '$takeoutItems', 'OUT', $acknowledged, '$ackBy')";
    $result = mysqli_query($connection, $sql);


    // Default response
    $response = 400;

    if ($result == TRUE) {
      // Change every item as taken in the database

      // Prepare the SQL statement once
      $stmt = $connection->prepare("UPDATE leltar SET Status = ?, RentBy = ? WHERE `UID` = ?");

      $takeoutItems = json_decode($takeoutItems, true);
      foreach ($takeoutItems as $i) {
        $uid = $i["uid"];
        $status = in_array("admin", $_SESSION['groups']) ? 0 : 2;

        // Bind parameters and execute
        $stmt->bind_param("iss", $status, $takeOutAsUser, $uid);
        $result = $stmt->execute();

        if ($result != TRUE) {
          echo "Error: " . $stmt->error;
          break;
        }
      }

      // If no errors occurred during the loop
      if ($result == TRUE) {
        $response = 200;
      }
    }

    echo $response;
    $connection->close();
    return;
  }

  //Usercheck approved takeout process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  //static function approveTakeout($value)
  //{
  //  if ($value == 'true') {/*  If approved (value=true)  */
  //    $userName = $_SESSION['UserUserName'];
  //    if (empty($userName)) {
  //      return 400; // Session data is empty (e.g User is not loggged in.)
  //    } else {
  //      $data = json_decode(stripslashes($_POST['data']), true);
  //      $dataArray = array();
  //      foreach ($data as $d) {
  //        array_push($dataArray, $d["uid"]);
  //      }
  //      //For Use in the SQL query.
  //      $dataString = "'" . implode("','", $dataArray) . "'";
  //      //Restore Items allowing others to take it out.
  //      $sql = "START TRANSACTION; UPDATE leltar SET leltar.Status=0 AND RentBy='" . $_POST['user'] . "' WHERE leltar.UID IN (" . $dataString . ");";
  //      //Acknowledge events in log.
  //      $sql .= "UPDATE takelog SET Acknowledged=1, ACKBY='" . $userName . "' WHERE User='" . $_POST['user'] . "' AND Date='" . $_POST['date'] . "' AND EVENT='OUT' AND JSON_CONTAINS(Items, '" . $_POST['data'] . "'); COMMIT;";
//
  //      $connection = Database::runQuery_mysqli();
  //      if (!$connection->multi_query($sql)) {
  //        printf("Error message: %s\n", $connection->error);
  //      } else {
  //        //All good, return OK message
  //        //echo $sql;
  //        echo 200;
  //        return;
  //      }
  //    }
  //  } else {
  //    /*  If not approved (value=false) - Decline takeout  */
  //    $userName = $_SESSION['UserUserName'];
  //    if (empty($userName)) {
  //      return 400; // Session data is empty (e.g User is not loggged in.)
  //    } else {
  //      $data = json_decode(stripslashes($_POST['data']), true);
  //      $dataArray = array();
  //      foreach ($data as $d) {
  //        array_push($dataArray, $d["uid"]);
  //      }
  //      // var_dump($dataArray);
  //      //Preparing items for dataset in sql command.
  //      $dataString = "'" . implode("','", $dataArray) . "'";
//
  //      //Restore Items allowing others to take it out.
  //      $sql = "START TRANSACTION; UPDATE leltar SET Status=1, RentBy='NULL' WHERE UID IN (" . $dataString . ");";
  //      //Remove takeOut event form log.
  //      $sql .= "DELETE FROM takelog WHERE Acknowledged=0 AND User='" . $_POST['user'] . "'AND Event='OUT' AND JSON_CONTAINS(Items, '" . $_POST['data'] . "') AND Date='" . $_POST['date'] . "'; COMMIT;";
//
  //      $connection = Database::runQuery_mysqli();
  //      if (!$connection->multi_query($sql)) {
  //        printf("Error message: %s\n", $connection->error);
  //      } else {
  //        //All good, return OK message
  //        echo 200;
  //        return;
  //      }
  //    }
  //  }
  //}

  /*Take out items from the database. Sets the item status to 0 (taken out)

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.
  Currenty limited behaviour (Only empty takerestrict items work!)*/

  //TODO: update this behaviour.
  static function REST_takeout($items, $userData)
  {
    $successfulTakeouts = 0;
    $successfulItems = array();
    foreach ($items as $item) {
      # Check if it is taken out or marked as restri
      $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
      //Get a new database connection
      $connection = Database::runQuery_mysqli();
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("s", $item);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      if ($row['Status'] == 0 && $row['RentBy'] != NULL && $row['TakeRestrict'] != "") { //TODO: Update this line!
        //Item is taken out, or currenty limited by api (Only empty takerestrics items work!)
        continue;
      } else {
        $sql = "UPDATE leltar SET Status = 0, RentBy = ? WHERE UID = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ss", $userData['username'], $item);
        $stmt->execute();

        // Check affected rows on the prepared statement
        if ($stmt->affected_rows == 1) {
          // All good, return OK message
          $successfulItems[] = $item;
          $successfulTakeouts++;
        }

        $stmt->close();
      }
    }
    return array('successfulTakeouts' => $successfulTakeouts, 'successfulItems' => $successfulItems);
  }

  /*Retrieve items to the database. Sets the item status to 1.

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.*/

  //TODO: update this behaviour.
  static function REST_retrieve($items, $userData)
  {
    $successfulRetrieves = 0;
    $successfulItems = array();
    foreach ($items as $item) {
      # Check if it is taken out or marked as restri
      $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
      //Get a new database connection
      $connection = Database::runQuery_mysqli();
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("s", $item);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      if ($row['Status'] == 0 && $row['RentBy'] == $userData['username']) {
        //Item is taken out by this user.
        $sql = "UPDATE leltar SET Status = 1, RentBy = NULL WHERE UID = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $item);
        $stmt->execute();

        // Check affected rows on the prepared statement
        if ($stmt->affected_rows == 1) {
          // All good, return OK message
          $successfulItems[] = $item;
          $successfulRetrieves++;
        }
        $stmt->close();
      } else {
        continue;
      }
    }
    return array('successfulRetrieves' => $successfulRetrieves, 'successfulItems' => $successfulItems);
  }
}

class retrieveManager
{
  // Function to list the items that are taken out by the user
  static function listUserItems()
  {
    //Get the items that are currently by the user
    $connection = Database::runQuery_mysqli();
    $sql = ("SELECT * FROM `leltar` WHERE `RentBy`='" . $_SESSION['userId'] . "' AND Status=0");
    $result = $connection->query($sql);
    $connection->close();
    $items = array();
    while ($row = $result->fetch_assoc()) {
      $items[] = $row;
    }

    return json_encode($items);
  }

  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageRetrieve()
  {
    //Accesses post and Session Data.
    //CHECK if sesison data is empty!
    $userName = $_SESSION['UserUserName'];
    if (empty($userName)) {
      return 400; // Session data is empty (e.g User is not loggged in.)
    }
    date_default_timezone_set('Europe/Budapest');
    $currDate = date("Y/m/d H:i:s");
    $data = json_decode(stripslashes($_POST['data']), true);
    $dataArray = array();

    foreach ($data as $d) {
      array_push($dataArray, $d["uid"]);
    }
    //For Use in SQL query.

    $itemNamesString = '';
    //Append each uid to a string, separated by commas.
    foreach ($dataArray as $uid) {
      $itemNamesString .= "'" . $uid . "',";
    }

    //strip last comma
    $itemNamesString = substr($itemNamesString, 0, -1);
    // //Convert DataArraz to JSON
    $dataString = json_encode($data);
    // Database init  - create a mysqli object

    $connection = Database::runQuery_mysqli();
    if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
      $sql = " START TRANSACTION; UPDATE leltar SET leltar.Status=1, leltar.RentBy=NULL WHERE leltar.UID IN (" . $itemNamesString . ");";
      $sql .= "INSERT INTO takelog VALUES";
      $sql .= "(NULL, '$currDate', '" . $_SESSION['userId'] . "', '" . $dataString . "', 'IN',1,'$userName')";
      $sql .= "; COMMIT;";
      ;
      if (!$connection->multi_query($sql)) {
        printf("Error message: %s\n", $connection->error);
      } else {
        //All good, return OK message
        echo 200;
        exit();
        //return;
      }
    } else { // Manual accept in usercheck required
      $sql = " 
      START TRANSACTION; UPDATE leltar SET leltar.Status=2, leltar.RentBy=NULL WHERE leltar.UID IN (" . $itemNamesString . ");";
      $sql .= "INSERT INTO takelog VALUES";
      $sql .= "(NULL, '$currDate', '" . $_SESSION['userId'] . "', '" . $dataString . "', 'IN',0,NULL)";
      $sql .= "; COMMIT;";
      if (!$connection->multi_query($sql)) {
        printf("Error message: %s\n", $connection->error);
      } else {
        //All good, return OK message
        echo 200;
        return;
      }
    }
  }

  //Usercheck approved retrieve process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  //static function approveRetrieve($value)
  //{
  //  /*  If not approved (value=false) - RETRIEVE CANNOT BE declined!  */
  //  /*  If approved (value=true) - RETRIEVE CANNOT BE declined!  */
  //  $userName = $_SESSION['UserUserName'];
  //  if (empty($userName)) {
  //    return 400; // Session data is empty (e.g User is not loggged in.)
  //  } else {
  //    $data = json_decode(stripslashes($_POST['data']), true);
  //    $dataArray = array();
//
  //    foreach ($data as $d) {
  //      array_push($dataArray, $d["uid"]);
  //    }
  //    //For Use in the SQL query.
  //    $dataString = "'" . implode("','", $dataArray) . "'";
//
  //    //Restore Items allowing others to take it out.
  //    $sql = "START TRANSACTION; UPDATE leltar SET leltar.Status=1 WHERE leltar.UID IN (" . $dataString . ");";
  //    //Acknowledge events in log.
  //    $sql .= "UPDATE takelog SET Acknowledged=1, ACKBY='" . $userName . "', Date='" . date("Y/m/d H:i:s") . "' WHERE User='" . $_POST['user'] . "' AND Date='" . $_POST['date'] . "' AND EVENT='IN' AND JSON_CONTAINS(Items, '" . $_POST['data'] . "'); COMMIT;";
//
  //    $connection = Database::runQuery_mysqli();
  //    if (!$connection->multi_query($sql)) {
  //      echo "Error message: %s\n" . $connection->error;
  //    } else {
  //      //All good, return OK message
  //      echo 200;
  //      return;
  //    }
  //  }
  //  /*  If approved (value=true)  */
  //}
}

class itemDataManager
{

  static function confirmItems($eventID, $items, $direction)
  {
    if (!isset($_SESSION["userId"]))
      return 400; // Session data is empty (e.g User is not loggged in.)

    $items = json_decode($items, true);

    $connection = Database::runQuery_mysqli();

    // Get the user who initiated the transaction and the items
    $sql = "SELECT UserID, Items FROM takelog WHERE ID=" . $eventID;
    $info = $connection->query($sql);
    $info = $info->fetch_assoc();
    $transUser = $info['UserID'];
    $originalItems = json_decode($info['Items'], true);

    $declinedItems = array();

    // For every item check if it was accepted or declined
    foreach ($items as $item) {
      if ($item['declined'] == 'true') {
        if ($direction == 'OUT') {
          $sql = "UPDATE leltar SET Status = 1, RentBy = NULL WHERE UID = '" . $item['uid'] . "'";
        } else {
          $sql = "UPDATE leltar SET Status = 0, RentBy = '" . $transUser . "' WHERE UID = '" . $item['uid'] . "'";
        }
        $connection->query($sql);
        // Add the declined item to the list
        $declinedItems[] = $item['uid'];
        continue;
      }

      if ($direction == 'OUT') {
        $sql = "UPDATE leltar SET Status = 0, RentBy = '" . $transUser . "' WHERE UID = '" . $item['uid'] . "'";
      } else {
        $sql = "UPDATE leltar SET Status = 1, RentBy = NULL WHERE UID = '" . $item['uid'] . "'";
      }
      $connection->query($sql);
    }

    $sql = "UPDATE takelog SET Acknowledged=1, ACKBY='" . $_SESSION['UserUserName'] . "' WHERE ID=" . $eventID;
    $result = $connection->query($sql);


    if ($result == TRUE) {
      // Check if there are any declined items
      if (count($declinedItems) > 0) {

        // Get the id and name from the original items for the declined items
        $declinedItems = array_map(function ($item) use ($originalItems) {
          foreach ($originalItems as $originalItem) {
            if ($originalItem['uid'] == $item) {
              return array ('uid' => $item, 'name' => $originalItem['name']);
            }
          }
        }, $declinedItems);


        // Create a new takelog entry for the declined items
        $sql = "INSERT INTO takelog (`ID`, `Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
                VALUES (NULL, '" . date("Y/m/d H:i:s") . "', '" . $transUser . "', '" . json_encode($declinedItems) . "', 'DECLINE', 1, '" . $_SESSION['UserUserName'] . "')";
        $connection->query($sql);

        //Function to compare multidimensional arrays
        function array_diff_multi($array1, $array2)
        {
          foreach ($array1 as $key => $value) {
            if (array_search($value, $array2) !== false) {
              unset($array1[$key]);
            }
          }
          return array_values($array1); // Use array_values to reindex the array
        }

        // Update the takelog entry for the original items
        $sql = "UPDATE takelog SET Items='" . json_encode(array_diff_multi($originalItems, $declinedItems)) . "' WHERE ID=" . $eventID;
        $connection->query($sql);
      }
      $connection->close();
      return 200;
    }
    return 500;
  }

  static function getItemData($itemTypes)
  {
    $displayed = "";
    if ($itemTypes['rentable'] != 1 & $itemTypes['studio'] != 2 & $itemTypes['nonRentable'] != 3 & $itemTypes['Out'] != 4 & $itemTypes['Event'] != 5) {
      return NULL;
    }
    $sql = '';
    //Kölcsönözhető
    if ($itemTypes['rentable'] == 1) {
      $sql .= 'SELECT * FROM leltar WHERE TakeRestrict=""';
      $displayed = $displayed . " Médiás";
    }
    //Stúdiós
    if ($itemTypes['studio'] == 2) {
      if (isset($_GET['rentable'])) {
        $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="s"';
        $displayed = $displayed . ", Stúdiós";
      } else {
        $sql = 'SELECT * FROM leltar WHERE TakeRestrict="s"';
        $displayed = $displayed . " Stúdiós";
      }
    }

    //Eventes
    if ($itemTypes['Event'] == 5) {
      if (isset($_GET['rentable']) || isset($_GET['studio'])) {
        $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="e"';
        $displayed = $displayed . ", Eventes";
      } else {
        $sql = ' SELECT * FROM leltar WHERE TakeRestrict="e"';
        $displayed = $displayed . "eventes";
      }
    }
    //Nem kölcsönözhető
    if ($itemTypes['nonRentable'] == 3) {
      //Speciális eset, ha csak a nem kölcsönözhető, stúdiós elemeket akarjuk kilistázni
      if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['Event'])) {
        $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="*"';
        $displayed = $displayed . ", Nem kölcsönözhető";
      } else {
        $sql = ' SELECT * FROM leltar WHERE TakeRestrict="*"';
        $displayed = $displayed . "Nem kölcsönözhető";
      }
    }
    //Kinnlevő
    if ($itemTypes['Out'] == 4) {
      if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['nonRentable']) || isset($_GET['Event'])) {
        $sql .= 'UNION SELECT * FROM leltar WHERE RentBy IS NOT NULL';
        $displayed = $displayed . ", Kinnlevő";
      } else {
        $sql = 'SELECT * FROM leltar WHERE RentBy IS NOT NULL';
        $displayed = $displayed . "Kinnlevő";
      }
    }


    $sql = $sql . " ORDER BY " . $_GET['orderByField'] . " " . $_GET['order'];
    //echo $sql;
    return Database::runQuery($sql);
  }

  static function getPresets()
  {
    $mysqli = Database::runQuery_mysqli();
    $rows = array();
    $mysqli->set_charset("utf8");
    $query = "SELECT Name, Items FROM takeoutpresets";
    if ($result = $mysqli->query($query)) {
      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      $a = json_encode($rows);
      //var_dump($a);
      echo $a;
    }
    return;
  }

  static function getItems()
  {
    $sql = "SELECT * FROM leltar";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }

  static function listByCriteria($itemState, $orderCriteria, $orderDirection = 'asc', $takeRestrict = 'none')
  {
    $takeRestrictArray = array(
      'medias' => 'TakeRestrict=""',
      'studios' => 'TakeRestrict="s"',
      'eventes' => 'TakeRestrict="e"',
      'nonRentable' => 'TakeRestrict="*"',
      'mediaAndStudio' => '(TakeRestrict="" OR TakeRestrict="s")',
      'mediaAndEvent' => '(TakeRestrict="" OR TakeRestrict="e")',
      'studioAndEvent' => '(TakeRestrict="s" OR TakeRestrict="e")',
      'mediaAndStudioAndEvent' => '(TakeRestrict="" OR TakeRestrict="s" OR TakeRestrict="e")',
      'none' => '1=1',
    );

    $stateArray = array(
      'in' => 'RentBy IS NULL',
      'out' => 'RentBy IS NOT NULL',
      'all' => '1=1'
    );

    $orderbyArray = array(
      'name' => 'Nev',
      'uid' => 'UID',
      'status' => 'Status',
      'rentby' => 'RentBy',
      'id' => 'ID',
      'takerestrict' => 'TakeRestrict',
      'type' => 'Tipus',
    );

    $orderDirARR = array(
      'asc' => 'ASC',
      'desc' => 'DESC',
    );

    $sql = "SELECT * FROM leltar WHERE " . $takeRestrictArray[$takeRestrict] . " AND " . $stateArray[$itemState] . " ORDER BY " . $orderbyArray[$orderCriteria] . " " . $orderDirARR[$orderDirection];
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }

  //Returns how many items the user has taken out.
  static function getUserItemCount()
  {
    $sql = "SELECT * FROM leltar WHERE RentBy = ?";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $_SESSION['UserUserName']);
    $stmt->execute();
    $result = $stmt->get_result();
    return mysqli_num_rows($result);
  }

  static function listUserItems($userData)
  {
    //If userdata is empty, return a json with the error message.
    if (empty($userData)) {
      return json_encode(array('type' => 'error', 'text' => 'Invalid api key'));
    }
    $sql = "SELECT * FROM leltar WHERE RentBy = ?";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $userData['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }


  static function getItemsForConfirmation()
  {
    $sql = "SELECT * FROM takelog WHERE Acknowledged=0 AND Event != 'SERVICE' ORDER BY DATE DESC, EVENT";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }

  static function getToBeUserCheckedCount()
  {
    $sql = "SELECT COUNT(*) FROM takelog WHERE Acknowledged=0";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['COUNT(*)'];
  }

  static function getServiceItemCount()
  {
    $sql = "SELECT COUNT(*) FROM leltar WHERE RentBy='Service'";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['COUNT(*)'];
  }
}

class itemHistoryManager
{
  #TODO: Take code from Pathfinder and implement it here.

  static function getItemHistory($itemUID)
  {
    $sql = "SELECT * FROM `takelog` WHERE JSON_CONTAINS(Items, " . "'" . "{" . '"uid" : "' . $itemUID . '"}' . "'" . ") ORDER BY `Date` DESC";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }

}


/**
 * Handle URL requests
 */
if (isset($_POST['mode'])) {

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  if ($_POST['mode'] == 'stageTakeout') {
    echo takeOutManager::stageTakeout($_POST['items'], $_POST['toUserId']);
  }
  //if ($_POST['mode'] == 'takeOutApproval') {
  //  echo takeOutManager::approveTakeout($_POST['value']);
  //}
  if ($_POST['mode'] == 'listUserItems') {
    echo retrieveManager::listUserItems();
  }
  if ($_POST['mode'] == 'retrieveStaging') {
    echo retrieveManager::stageRetrieve();
  }
  //if ($_POST['mode'] == 'retrieveApproval') {
  //  echo retrieveManager::approveRetrieve($_POST['value']);
  //}

  if ($_POST['mode'] == 'confirmItems') {
    echo itemDataManager::confirmItems($_POST['eventID'], $_POST['items'], $_POST['direction']);
  }
  if ($_POST['mode'] == 'getItems') {
    echo itemDataManager::getItems();
  }

  if ($_POST['mode'] == 'listByCriteria') {
    echo itemDataManager::listByCriteria($_POST['itemState'], $_POST['orderCriteria'], $_POST['orderDirection'], $_POST['takeRestrict']);
  }

  if ($_POST['mode'] == 'getItemHistory') {
    echo itemHistoryManager::getItemHistory($_POST['itemUID']);
  }

  if ($_POST['mode'] == 'getPresets') {
    echo itemDataManager::getPresets();
  }

  if ($_POST['mode'] == 'getItemsForConfirmation') {
    echo itemDataManager::getItemsForConfirmation();
  }

  if ($_POST['mode'] == 'getProfileItemCounts') {
    echo itemDataManager::getServiceItemCount();
    echo ",";
    echo itemDataManager::getToBeUserCheckedCount();
    echo ",";
    echo itemDataManager::getUserItemCount();
  }
  exit();
}
