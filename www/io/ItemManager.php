<?php 

/**
 * ItemManager.php
 * Manages the takeout and retrieve processes, stats and user Lists.
 */
namespace Mediaio;
require_once __DIR__.'/Database.php';
require_once __DIR__.'/Core.php';
use Mediaio\Core;
use Mediaio\Database;


class takeOutManager{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageTakeout(){
    //Accesses post and Session Data.
  }

  //Usercheck approved takeout process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  static function approveTakeout($value){
    if($value=='true'){/*  If approved (value=true)  */
        $userName = $_SESSION['UserUserName'];
        if(empty($userName)){
          return 400; // Session data is empty (e.g User is not loggged in.)
        }else{
          $data = json_decode(stripslashes($_POST['data']),true);
          $dataArray=array();
          foreach($data as $d){
            array_push($dataArray,$d["name"]);
          }
          //For Use in the SQL query.
          $dataString = "'" . implode ( "','", $dataArray ) . "'";
          //Restore Items allowing others to take it out.
          $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=0 AND RentBy='".$_POST['user']."' WHERE leltar.Nev IN (".$dataString.");";
          //Acknowledge events in log.
          $sql.="UPDATE takelog SET Acknowledged=1, ACKBY='".$userName."' WHERE User='".$_POST['user']."' AND Date='".$_POST['date']."' AND EVENT='OUT' AND JSON_CONTAINS(Items, '".$_POST['data']."'); COMMIT;";
      
          $connection=Database::runQuery_mysqli();
          if(!$connection->multi_query($sql)){
            printf("Error message: %s\n", $connection->error);
          }else{
            //All good, return OK message
            //echo $sql;
            echo 200;
            return;
          }
        }
    }else{
      /*  If not approved (value=false) - Decline takeout  */
        $userName = $_SESSION['UserUserName'];
        if(empty($userName)){
          return 400; // Session data is empty (e.g User is not loggged in.)
        }else{
          $data = json_decode(stripslashes($_POST['data']),true);
          $dataArray=array();
          foreach($data as $d){
            array_push($dataArray,$d["name"]);
          }
          // var_dump($dataArray);
          //Preparing items for dataset in sql command.
          $dataString = "'" . implode ( "','", $dataArray ) . "'";
      
          //Restore Items allowing others to take it out.
          $sql="START TRANSACTION; UPDATE leltar SET Status=1, RentBy='NULL' WHERE Nev IN (".$dataString.");";
          //Remove takeOut event form log.
          $sql.="DELETE FROM takelog WHERE Acknowledged=0 AND User='".$_POST['user']."'AND Event='OUT' AND JSON_CONTAINS(Items, '".$_POST['data']."') AND Date='".$_POST['date']."'; COMMIT;";
          
          $connection=Database::runQuery_mysqli();
          if(!$connection->multi_query($sql)){
            printf("Error message: %s\n", $connection->error);
          }else{
            //All good, return OK message
            echo 200;
            return;
          }
        }     
    }

    
  }

  /*Take out items from the database. Sets the item status to 0 (taken out)

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.
  Currenty limited behaviour (Only empty takerestrict items work!)*/

  //TODO: update this behaviour.
  static function REST_takeout($items,$userData){
    $successfulTakeouts=0;
    $successfulItems=array();
    foreach ($items as $item) {
      # Check if it is taken out or marked as restri
      $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
      //Get a new database connection
      $connection=Database::runQuery_mysqli();
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("s", $item);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      
      if($row['Status']==0 && $row['RentBy']!=NULL && $row['TakeRestrict']!=""){ //TODO: Update this line!
        //Item is taken out, or currenty limited by api (Only empty takerestrics items work!)
        continue;
      }else{
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
    return array('successfulTakeouts'=>$successfulTakeouts,'successfulItems'=>$successfulItems);
  }

  /*Retrieve items to the database. Sets the item status to 1.

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.*/

  //TODO: update this behaviour.
  static function REST_retrieve($items,$userData){
    $successfulRetrieves=0;
    $successfulItems=array();
    foreach ($items as $item) {
      # Check if it is taken out or marked as restri
      $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
      //Get a new database connection
      $connection=Database::runQuery_mysqli();
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("s", $item);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      
      if($row['Status']==0 && $row['RentBy']==$userData['username']){
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
      }else{
        continue;
      }
    }
    return array('successfulRetrieves'=>$successfulRetrieves,'successfulItems'=>$successfulItems);
  }
}

class retrieveManager{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageRetrieve(){
    //Accesses post and Session Data.
    //CHECK if sesison data is empty!
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }
    date_default_timezone_set('Europe/Budapest');
    $currDate= date("Y/m/d H:i:s");
    $data = json_decode(stripslashes($_POST['data']));
    $dataArray=array();
    $countOfRec=0;
    //New query - reduced to single query containing all items using the implode function;
    $countOfRec+=1;
      $data = json_decode(stripslashes($_POST['data']));
      $dataArray=array();
      foreach($data as $d){
        //push 
      array_push($dataArray,array("name" => $d));
    }
    //var_dump($dataArray);
    //For Use in SQL query.
    
    $itemNamesString='';
    foreach ($dataArray as $array) {
      $itemNamesString=$itemNamesString."'".$array['name']."',";
    }

    //strip last comma
    $itemNamesString=substr($itemNamesString,0,-1);
    // //Convert DataArraz to JSON
    $dataString = json_encode($dataArray);
    // Database init  - create a mysqli object
      
      $connection=Database::runQuery_mysqli();
      if(in_array("admin",$_SESSION['groups'])){//Auto accept 
        $sql=" START TRANSACTION; UPDATE leltar SET leltar.Status=1, leltar.RentBy=NULL WHERE leltar.Nev IN (".$itemNamesString.");";
        $sql.="INSERT INTO takelog VALUES";
        $sql.="(NULL, '$currDate', '$userName', '".$dataString."', 'IN',1,'$userName')";
        $sql.="; COMMIT;";;
        if(!$connection->multi_query($sql)){
           printf("Error message: %s\n", $connection->error);
         }else{
            //All good, return OK message
            echo 200;
            exit();
            return;
          }
      }else{ // Manual accept in usercheck required
              $sql=" 
      START TRANSACTION; UPDATE leltar SET leltar.Status=2, leltar.RentBy=NULL WHERE leltar.Nev IN (".$itemNamesString.");";
      $sql.="INSERT INTO takelog VALUES";
      $sql.="(NULL, '$currDate', '$userName', '".$dataString."', 'IN',0,NULL)";
      $sql.="; COMMIT;";
      if(!$connection->multi_query($sql)){
        printf("Error message: %s\n", $connection->error);
      }else{
        //All good, return OK message
        echo 200;
        return;
      }
      }
  }

  //Usercheck approved retrieve process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  static function approveRetrieve($value){
    /*  If not approved (value=false) - RETRIEVE CANNOT BE declined!  */
    /*  If approved (value=true) - RETRIEVE CANNOT BE declined!  */
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }else{
      $data = json_decode(stripslashes($_POST['data']),true);
      $dataArray=array();

      foreach($data as $d){
        array_push($dataArray,$d["name"]);
      }
      //For Use in the SQL query.
      $dataString = "'" . implode ( "','", $dataArray ) . "'";

      //Restore Items allowing others to take it out.
      $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=1 WHERE leltar.Nev IN (".$dataString.");";
      //Acknowledge events in log.
      $sql.="UPDATE takelog SET Acknowledged=1, ACKBY='".$userName."', Date='".date("Y/m/d H:i:s")."' WHERE User='".$_POST['user']."' AND Date='".$_POST['date']."' AND EVENT='IN' AND JSON_CONTAINS(Items, '".$_POST['data']."'); COMMIT;";

      $connection=Database::runQuery_mysqli();
      if(!$connection->multi_query($sql)){
        echo "Error message: %s\n".$connection->error;
      }else{
        //All good, return OK message
        echo 200;
        return;
      }
    }
    /*  If approved (value=true)  */
  }
}

class itemDataManager{
    static function getNumberOfTotalItems(){}
    static function getNumberOfTakenItems(){}
    static function getItemData($itemTypes){
        $displayed="";
        if ($itemTypes['rentable']!=1 & $itemTypes['studio']!=2 & $itemTypes['nonRentable']!=3 & $itemTypes['Out']!=4 & $itemTypes['Event']!=5){
            return NULL;
        }
        $sql='';
        //Kölcsönözhető
        if ($itemTypes['rentable']==1){
          $sql .= 'SELECT * FROM leltar WHERE TakeRestrict=""';
          $displayed=$displayed." Médiás";
        }
        //Stúdiós
        if ($itemTypes['studio']==2){
          if (isset($_GET['rentable'])){
            $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="s"';
            $displayed=$displayed.", Stúdiós";
          }else{
            $sql = 'SELECT * FROM leltar WHERE TakeRestrict="s"';
            $displayed=$displayed." Stúdiós";
          }
        }

        //Eventes
        if ($itemTypes['Event']==5){
          if (isset($_GET['rentable']) || isset($_GET['studio']) ){
            $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="e"';
            $displayed=$displayed.", Eventes";
          }else{
            $sql =' SELECT * FROM leltar WHERE TakeRestrict="e"';
            $displayed=$displayed."eventes";
          }
        }  
        //Nem kölcsönözhető
        if ($itemTypes['nonRentable']==3){
          //Speciális eset, ha csak a nem kölcsönözhető, stúdiós elemeket akarjuk kilistázni
              if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['Event'])){
                $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="*"';
                $displayed=$displayed.", Nem kölcsönözhető";
              }else{
                $sql =' SELECT * FROM leltar WHERE TakeRestrict="*"';
                $displayed=$displayed."Nem kölcsönözhető";
              }

        }
        //Kinnlevő
        if ($itemTypes['Out']==4){
          if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['nonRentable']) || isset($_GET['Event'])){
            $sql .= 'UNION SELECT * FROM leltar WHERE RentBy IS NOT NULL';
            $displayed=$displayed.", Kinnlevő";
          }else{
            $sql = 'SELECT * FROM leltar WHERE RentBy IS NOT NULL';
            $displayed=$displayed."Kinnlevő";
          }
        }


        $sql= $sql." ORDER BY ".$_GET['orderByField']." ".$_GET['order'];
        //echo $sql;
        return Database::runQuery($sql);
    }
    /** Generates JSON data for takeout page, showing available and unavailable items. */
    static function generateTakeoutJSON(){
      $mysqli = Database::runQuery_mysqli();
      $rows = array();
      $mysqli->set_charset("utf8");
      $query = "SELECT Nev, ID, UID, Category, TakeRestrict, ConnectsToItems, Status FROM leltar"; //AND Status=1 
      if ($result = $mysqli->query($query)) {
          while ($row = $result->fetch_assoc()) {
              if($row['Status']!="1"){
                  $row['state']=['disabled' => true];
              }else{
                  $row['state']=['disabled' => false];
              }
              $rows[] = $row;

      }
      $a=json_encode($rows);
          //var_dump($a);
          $itemsJSONFile = fopen(__DIR__.'/data/takeOutItems.json', 'w');
          fwrite($itemsJSONFile, $a);
          fclose($itemsJSONFile);
      }
      return;
    }

    static function listItems(){
      //Refresh takeoutJSON
      self::generateTakeoutJSON();
      //Return json
      $itemsJSONFile = fopen(__DIR__.'/data/takeOutItems.json', 'r');
      $itemsJSON = fread($itemsJSONFile, filesize(__DIR__.'/data/takeOutItems.json'));
      fclose($itemsJSONFile);
      return $itemsJSON;
    }

    static function listByCriteria($itemState,$orderCriteria){
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

      $sql = "SELECT * FROM leltar WHERE ".$stateArray[$itemState]." ORDER BY ".$orderbyArray[$orderCriteria];
      //Get a new database connection
      $connection=Database::runQuery_mysqli();
      $stmt = $connection->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      $rows = array();
      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      $result=json_encode($rows);
      return $result;
      
    }

    static function listUserItems($userData){
      //If userdata is empty, return a json with the error message.
      if(empty($userData)){
        return json_encode(array('type'=>'error', 'text' => 'Invalid api key'));
      }
      $sql = "SELECT * FROM leltar WHERE RentBy = ?";
      //Get a new database connection
      $connection=Database::runQuery_mysqli();
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("s", $userData['username']);
      $stmt->execute();
      $result = $stmt->get_result();
      $rows = array();
      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      $result=json_encode($rows);
      return $result;

    }

}

class itemHistoryManager{
  #TODO: Take code from Pathfinder and implement it here.
}

class userManager{
  /**
   * Get every user from the database
   */
  static function getUsers(){
    $mysqli = Database::runQuery_mysqli();
    $rows = array();
    $mysqli->set_charset("utf8");
    $query = "SELECT usernameUsers FROM users";
    if ($result = $mysqli->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    $a=json_encode($rows);
        //var_dump($a);
        echo $a;
    }
    return;
  }

  /**
   * Get present presets from the database
   */

  static function getPresets(){
    $mysqli = Database::runQuery_mysqli();
    $rows = array();
    $mysqli->set_charset("utf8");
    $query = "SELECT Name, Items FROM takeoutpresets";
    if ($result = $mysqli->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    $a=json_encode($rows);
        //var_dump($a);
        echo $a;
    }
    return;
  }
}

/**
 * Handle URL requests
 */
if(isset($_POST['mode'])){

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  if($_POST['mode']=='takeOutStaging'){
    echo takeOutManager::stageTakeout();
    //Header set.
    exit();
  }
  if($_POST['mode']=='takeOutApproval'){
    echo takeOutManager::approveTakeout($_POST['value']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if($_POST['mode']=='retrieveStaging'){
    echo retrieveManager::stageRetrieve();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if($_POST['mode']=='retrieveApproval'){
    echo retrieveManager::approveRetrieve($_POST['value']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if($_POST['mode']=='getUsers'){
    echo userManager::getUsers();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }

  if($_POST['mode']=='getPresets'){
    echo userManager::getPresets();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  
}

?>