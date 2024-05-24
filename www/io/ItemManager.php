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
  static function stageTakeout($takeoutItems, $plannedData = NULL)
  {
    $takeoutItems = json_decode($takeoutItems, true);
    // Escape special characters in the item names like "
    foreach ($takeoutItems as &$item) {
      $item['name'] = addslashes($item['name']);
    }
    $takeoutItems = json_encode($takeoutItems, JSON_UNESCAPED_UNICODE);
    //Accesses post and Session Data.
    // Set time zone to Budapest
    date_default_timezone_set('Europe/Budapest');
    $currDate = date("Y/m/d H:i:s");
    $connection = Database::runQuery_mysqli();

    $instantTakeOut = false;
    $UID = $_SESSION['userId'];

    // Planned takeout code
    $plannedData = json_decode($plannedData, true);

    // --------  Check for any conflicts with the planned takeout -------//
    $sql = "SELECT * FROM takeoutPlanner";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
      $rows = $result->fetch_all(MYSQLI_ASSOC);

      $plannedStart = strtotime($plannedData['StartingDate']);
      $plannedEnd = strtotime($plannedData['EndDate']);

      $plannedItems = json_decode($takeoutItems, true);
      foreach ($rows as $row) {
        $items = json_decode($row['Items'], true);

        $rangeStart = strtotime($row['StartTime']);
        $rangeEnd = strtotime($row['ReturnTime']);

        if ($row['eventState'] == 2 || $row['eventState'] == -1)
          continue; // Skip if the event is already returned or disabled

        // If the submitted time frame matches with any planned takeout
        if (
          ($plannedStart >= $rangeStart && $plannedStart < $rangeEnd) ||
          ($plannedEnd > $rangeStart && $plannedEnd <= $rangeEnd)
        ) {
          // Check if there are any conflicts with the items
          $conflict = array_intersect(array_column($items, 'uid'), array_column($plannedItems, 'uid'));
          if (count($conflict) > 0) {
            return 409;
          }
        }
      }
    }
    // Check if the planned takeout is in the past
    if (strtotime($plannedData['StartingDate']) < strtotime($currDate)) {
      $instantTakeOut = true;
    }
    $eventState = $instantTakeOut ? 1 : 0; // 1 = Instant, 0 = Planned

    // Is user an admin?
    $acknowledged = in_array("admin", $_SESSION['groups']) ? 1 : 0; // Stageing happens here
    // Set the ackBy field to the user's name if the user is an admin
    $ackBy = $acknowledged ? $_SESSION['UserUserName'] : NULL;

    try {
      // TAKELOG
      if ($instantTakeOut) {
        $sql = "INSERT INTO takelog (`ID`, `Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
            VALUES (NULL, '$currDate', '$UID', '$takeoutItems', 'OUT', $acknowledged, '$ackBy')";
        $connection->query($sql);
        $takelogID = $connection->insert_id;
      } else {
        $takelogID = 0;
      }

      // Prevent XSS attacks by html special characters
      $plannedData['Name'] = htmlspecialchars($plannedData['Name']);
      $plannedData['Desc'] = htmlspecialchars($plannedData['Desc']);

      // TAKEOUTPLANNER
      $sql = "INSERT INTO takeoutPlanner (`ID`, `Name`, `Description`, `UserID`, `Items`, `takelogID`, `StartTime`, `ReturnTime`, `eventState`) 
              VALUES (NULL, '" . $plannedData['Name'] . "', '" . $plannedData['Desc'] . "', '" . $_SESSION['userId'] . "', '" . $takeoutItems . "', $takelogID, '" . $plannedData['StartingDate'] . "', '" . $plannedData['EndDate'] . "', $eventState)";
      $connection->query($sql);
    } catch (\Exception $e) {
      echo "Error: " . $e->getMessage();
      return 500;
    }


    // Change every item as taken in the database
    $takeoutItems = json_decode($takeoutItems, true);

    try {
      // Start transaction
      $connection->begin_transaction();
      // Check if planned takeout start time is in the future
      $status = in_array("admin", $_SESSION['groups']) ? 0 : 2;

      if ($instantTakeOut) {
        $sql = "UPDATE leltar SET Status = $status, RentBy = '" . $_SESSION['userId'] . "' WHERE `UID` = ?";
      } else {
        $sql = "UPDATE leltar SET isPlanned=1 WHERE `UID` = ?";
      }

      // Update leltar
      $stmt = $connection->prepare($sql);
      foreach ($takeoutItems as $item) {
        $stmt->bind_param("s", $item['uid']);
        $stmt->execute();
      }

      // Commit transaction
      $connection->commit();
      $connection->close();
    } catch (\Exception $e) {
      // Rollback transaction if there is an error
      $connection->rollback();
      printf("Error message: %s\n", $e->getMessage());
    }

    return 200;
  }

  /*Take out items from the database. Sets the item status to 0 (taken out)

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.
  Currenty limited behaviour (Only empty takerestrict items work!)*/

  //TODO: update this behaviour.
  //static function REST_takeout($items, $userData)
  //{
  //  $successfulTakeouts = 0;
  //  $successfulItems = array();
  //  foreach ($items as $item) {
  //    # Check if it is taken out or marked as restri
  //    $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
  //    //Get a new database connection
  //    $connection = Database::runQuery_mysqli();
  //    $stmt = $connection->prepare($sql);
  //    $stmt->bind_param("s", $item);
  //    $stmt->execute();
  //    $result = $stmt->get_result();
  //    $row = $result->fetch_assoc();
//
  //    if ($row['Status'] == 0 && $row['RentBy'] != NULL && $row['TakeRestrict'] != "") { //TODO: Update this line!
  //      //Item is taken out, or currenty limited by api (Only empty takerestrics items work!)
  //      continue;
  //    } else {
  //      $sql = "UPDATE leltar SET Status = 0, RentBy = ? WHERE UID = ?";
  //      $stmt = $connection->prepare($sql);
  //      $stmt->bind_param("ss", $userData['username'], $item);
  //      $stmt->execute();
//
  //      // Check affected rows on the prepared statement
  //      if ($stmt->affected_rows == 1) {
  //        // All good, return OK message
  //        $successfulItems[] = $item;
  //        $successfulTakeouts++;
  //      }
//
  //      $stmt->close();
  //    }
  //  }
  //  return array('successfulTakeouts' => $successfulTakeouts, 'successfulItems' => $successfulItems);
  //}

  /*Retrieve items to the database. Sets the item status to 1.

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.*/

  //TODO: update this behaviour.
  //static function REST_retrieve($items, $userData)
  //{
  //  $successfulRetrieves = 0;
  //  $successfulItems = array();
  //  foreach ($items as $item) {
  //    # Check if it is taken out or marked as restri
  //    $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
  //    //Get a new database connection
  //    $connection = Database::runQuery_mysqli();
  //    $stmt = $connection->prepare($sql);
  //    $stmt->bind_param("s", $item);
  //    $stmt->execute();
  //    $result = $stmt->get_result();
  //    $row = $result->fetch_assoc();

  //    if ($row['Status'] == 0 && $row['RentBy'] == $userData['username']) {
  //      //Item is taken out by this user.
  //      $sql = "UPDATE leltar SET Status = 1, RentBy = NULL WHERE UID = ?";
  //      $stmt = $connection->prepare($sql);
  //      $stmt->bind_param("s", $item);
  //      $stmt->execute();

  //      // Check affected rows on the prepared statement
  //      if ($stmt->affected_rows == 1) {
  //        // All good, return OK message
  //        $successfulItems[] = $item;
  //        $successfulRetrieves++;
  //      }
  //      $stmt->close();
  //    } else {
  //      continue;
  //    }
  //  }
  //  return array('successfulRetrieves' => $successfulRetrieves, 'successfulItems' => $successfulItems);
  //}
}

class retrieveManager
{
  // Function to list the items that are taken out by the user
  static function listUserItems($COUNT = false)
  {
    //Get the items that are currently by the user
    $connection = Database::runQuery_mysqli();

    $sql = "SELECT * FROM leltar WHERE RentBy = ? AND Status = 0";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $_SESSION['userId']);
    $stmt->execute();
    $result = $stmt->get_result();
    $response = $result->fetch_all(MYSQLI_ASSOC);

    $itemCount = count($response);

    // Add prepared items to the response
    $sql = "SELECT * FROM takeoutPlanner WHERE UserID = ? AND eventState=0";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $_SESSION['userId']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $row) {
      $items = json_decode($row['Items'], true);
      foreach ($items as $item) {
        $response[] = array(
          'UID' => $item['uid'],
          'Nev' => $item['name'],
          'Status' => 1,
          'RentBy' => $_SESSION['userId'],
          'isPlanned' => 1
        );
      }
    }

    return $COUNT ? $itemCount : json_encode($response);
  }

  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageRetrieve()
  {
    date_default_timezone_set('Europe/Budapest');
    $currDate = date("Y/m/d H:i:s");
    $retrieveItems = json_decode($_POST['data'], true);

    // Database init  - create a mysqli object
    $connection = Database::runQuery_mysqli();

    $status = in_array("admin", $_SESSION['groups']) ? 1 : 2;
    $acknowledged = in_array("admin", $_SESSION['groups']) ? 1 : 0;
    $ackBy = in_array("admin", $_SESSION['groups']) ? $_SESSION['UserUserName'] : NULL;
    $RentBy = in_array("admin", $_SESSION['groups']) ? 'NULL' : $_SESSION['userId'];
    try {
      // Start transaction
      $connection->begin_transaction();

      // Get the uids from retrieveItems
      $uids = array_map(function ($item) {
        return $item['uid'];
      }, $retrieveItems);

      // Convert the uids to a string for the SQL query
      $uids_str = implode(',', array_map('intval', $uids));

      // Get the planned takeouts that are in retrieveItems
      $sql = "SELECT * FROM takeoutPlanner WHERE eventState=0 AND Items IN ($uids_str)";
      $result = $connection->query($sql);
      $plannedTakeouts = $result->fetch_all(MYSQLI_ASSOC);

      // Convert the items from JSON to arrays
      $plannedTakeouts = array_map(function ($item) {
        return json_decode($item['Items'], true);
      }, $plannedTakeouts);

      // Flatten the array
      $plannedTakeouts = array_merge(...$plannedTakeouts);

      // Update leltar
      $stmt = $connection->prepare("UPDATE `leltar` SET `Status`=$status, `RentBy`=$RentBy, `isPlanned`=? WHERE `UID`=?;");
      foreach ($retrieveItems as $item) {
        // Check if the item is in the planned takeouts
        $isPlanned = in_array($item['uid'], array_column($plannedTakeouts, 'uid')) ? 1 : 0;
        $stmt->bind_param("is", $isPlanned, $item['uid']);
        $stmt->execute();
      }

      //Convert data to JSON
      $dataJSON = json_encode($retrieveItems);

      // Insert into takelog
      $stmt = $connection->prepare("INSERT INTO takelog VALUES (NULL, ?, ?, ?, 'IN', ?, ?);");
      $stmt->bind_param("sssis", $currDate, $_SESSION['userId'], $dataJSON, $acknowledged, $ackBy);
      $stmt->execute();

      // Update takeoutPlanner table
      $stmt = $connection->prepare("SELECT * FROM takeoutPlanner WHERE eventState=1 AND UserID=?");
      $stmt->bind_param("s", $_SESSION['userId']);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) { // For safety reasons
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        // Check if all the items are returned
        foreach ($rows as $row) {
          // Make a list of the items for IN statement
          $items = json_decode($row['Items'], true);
          $items = array_map(function ($item) {
            return "'" . $item['uid'] . "'";
          }, $items);

          // Check if all the items are returned
          $items_str = implode(",", $items);
          $sql = "UPDATE takeoutPlanner 
              SET eventState=2 
              WHERE ID='{$row['ID']}' 
              AND (
                  SELECT COUNT(*) 
                  FROM leltar 
                  WHERE UID IN ($items_str) 
                  AND Status=0 
                  AND RentBy='{$_SESSION['userId']}'
              ) = 0";
          $connection->query($sql);
        }
      }

      // Commit transaction
      $connection->commit();

      // All good, return OK message
      echo 200;
      exit();
    } catch (\Exception $e) {
      // Rollback transaction if there is an error
      $connection->rollback();
      printf("Error message: %s\n", $e->getMessage());
    }
  }
}

class itemDataManager
{

  // TAKEOUT PLANNING FUNCTIONS ---------------------------

  static function getPlannedTakeouts()
  {
    $sql = "SELECT * FROM takeoutPlanner";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    $rows['events'] = array();
    while ($row = $result->fetch_assoc()) {
      $rows['events'][] = $row;
    }
    $rows['currentUser'] = $_SESSION['userId'];
    $rows['isAdmin'] = in_array("admin", $_SESSION['groups']);
    $result = json_encode($rows);
    return $result;
  }


  static function startPlannedTakeout($eventID)
  {
    date_default_timezone_set('Europe/Budapest');
    $currDate = date("Y/m/d H:i:s");

    $sql = "SELECT * FROM takeoutPlanner WHERE ID=" . $eventID;
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $result = $result->fetch_assoc();

    if ($result['UserID'] != $_SESSION['userId']) {
      return 403;
    }

    $acknowledged = in_array("admin", $_SESSION['groups']) ? 1 : 0;
    $ackBy = in_array("admin", $_SESSION['groups']) ? $_SESSION['UserUserName'] : NULL;

    // Update takelog
    $sql = "INSERT INTO takelog (`ID`, `Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
          VALUES (NULL, '$currDate', " . $result['UserID'] . ", '" . $result['Items'] . "', 'OUT', $acknowledged, '$ackBy')";
    $connection->query($sql);
    $takelogID = $connection->insert_id;

    $sql = "UPDATE takeoutPlanner SET eventState=1, takelogID=$takelogID WHERE ID=" . $eventID;
    $connection->query($sql);


    // Change every item as taken in the database
    $items = json_decode($result['Items'], true);
    $status = in_array("admin", $_SESSION['groups']) ? 0 : 2;
    $stmt = $connection->prepare("UPDATE leltar SET Status = $status, RentBy=? WHERE `UID` = ?");
    foreach ($items as $i) {
      $stmt->bind_param("ss", $_SESSION['userId'], $i['uid']);
      $stmt->execute();
    }

    return 200;
  }


  static function disableTakeout($eventID)
  {
    // Get details of the event
    $sql = "SELECT * FROM takeoutPlanner WHERE ID=" . $eventID;
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $result = $result->fetch_assoc();

    $userID = $result['UserID'];
    $takelogID = $result['takelogID'];

    $sql = "UPDATE takeoutPlanner SET eventState=-1 WHERE ID=" . $eventID;
    $connection->query($sql);

    //Update leltar
    $sql = "UPDATE `leltar` SET `Status`=1 WHERE `UID`=?";
    foreach (json_decode($result['Items'], true) as $item) {
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("s", $item['uid']);
      $stmt->execute();
    }
    $connection->query($sql);

    // Update takelog
    $sql = "UPDATE takelog SET Event='DISABLED' WHERE ID=" . $takelogID;
    $connection->query($sql);

    $connection->close();
    return 200;
  }


  static function deletePlannedTakeout($eventID)
  {
    $sql = "SELECT * FROM takeoutPlanner WHERE ID=" . $eventID;
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $result = $result->fetch_assoc();

    if ($result['UserID'] != $_SESSION['userId'] && !in_array("admin", $_SESSION['groups'])) {
      return 403;
    }
    // Check if deleting is aviavable
    if ($result['eventState'] == 1 || $result['eventState'] == 2) {
      return 409;
    }

    // If the items have been taken out or already returned, dont delete the event
    if ($result['eventState'] != 1 && $result['eventState'] != 2) {

      // Change every item as taken in the database
      $items = json_decode($result['Items'], true);
      $stmt = $connection->prepare("UPDATE leltar SET Status = 1 WHERE `UID` = ?");
      foreach ($items as $i) {
        $stmt->bind_param("s", $i['uid']);
        $stmt->execute();
      }

    }
    $sql = "DELETE FROM takeoutPlanner WHERE ID=" . $eventID;
    $connection->query($sql);
    return 200;
  }

  // ______________________________________________________________________________________

  /*

  Confirm items in the database. Sets the item status to 0 (taken out) or 1 (available)

  */
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
        $status = ($direction == 'OUT') ? 1 : 0;
        $RentBy = ($direction == 'OUT') ? 'NULL' : $transUser;
        $sql = "UPDATE `leltar` SET `Status` = $status, `RentBy`=$RentBy WHERE `UID` = '" . $item['uid'] . "'";
        $connection->query($sql);
        // Add the declined item to the list
        $declinedItems[] = $item['uid'];
        continue;
      }

      if ($direction == 'OUT') {
        $sql = "UPDATE `leltar` SET `Status` = 0 WHERE `UID` = '" . $item['uid'] . "'";
      } else {
        $sql = "UPDATE `leltar` SET `Status` = 1, `RentBy`=NULL WHERE `UID` = '" . $item['uid'] . "'";
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

        // If everything was declined, delete the original takelog entry
        if (count($originalItems) == count($declinedItems)) {
          $sql = "DELETE FROM takelog WHERE ID=" . $eventID;
          $connection->query($sql);
        }
      }
      $connection->close();
      return 200;
    }
    return 500;
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
    // Get a new database connection
    $connection = Database::runQuery_mysqli();

    // Prepare the SQL query
    $sql = "SELECT leltar.*, COALESCE(leltar.RentBy, tp.UserID) as RentBy
    FROM leltar
    LEFT JOIN (
        SELECT tp1.Items, tp1.UserID, tp1.StartTime
        FROM takeoutPlanner tp1
        JOIN (
            SELECT Items, MIN(StartTime) as MinStartTime
            FROM takeoutPlanner
            WHERE eventState=0
            GROUP BY Items
        ) as tp2
        ON tp1.Items = tp2.Items AND tp1.StartTime = tp2.MinStartTime
    ) as tp
    ON leltar.isPlanned = 1 AND leltar.Status = 1 AND JSON_EXTRACT(tp.Items, '$[*].uid') LIKE CONCAT('%\"', leltar.UID, '\"%')
    ORDER BY leltar.ID";

    // Execute the query
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Encode the result to JSON
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
      'in' => 'Status = 1',
      'out' => '(Status = 0 OR Status = 2)',
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

    $sql = "SELECT * FROM leltar WHERE " . $takeRestrictArray[$takeRestrict] . " AND " . $stateArray[$itemState] .
      " ORDER BY " . $orderbyArray[$orderCriteria] . " " . $orderDirARR[$orderDirection];

    // Get a new database connection
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
    $sql = "SELECT COUNT(*) FROM leltar WHERE Status=-1";
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


  static function getInventoryHistory()
  {
    // Select only the last week's data
    $sql = "SELECT * FROM `takelog` WHERE `Date` > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY `Date` DESC";
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
    echo takeOutManager::stageTakeout($_POST['items'], $_POST['plannedData']);
  }
  if ($_POST['mode'] == 'listUserItems') {
    echo retrieveManager::listUserItems();
  }
  if ($_POST['mode'] == 'retrieveStaging') {
    echo retrieveManager::stageRetrieve();
  }

  if ($_POST['mode'] == 'getPlannedTakeouts') {
    echo itemDataManager::getPlannedTakeouts();
  }
  if ($_POST['mode'] == 'startPlannedTakeout') {
    echo itemDataManager::startPlannedTakeout($_POST['eventID']);
  }

  if ($_POST['mode'] == 'deletePlannedTakeout') {
    echo itemDataManager::deletePlannedTakeout($_POST['ID']);
  }


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

  if ($_POST['mode'] == 'getInventoryHistory') {
    echo itemHistoryManager::getInventoryHistory();
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
    echo retrieveManager::listUserItems(true);
  }
  exit();
}
