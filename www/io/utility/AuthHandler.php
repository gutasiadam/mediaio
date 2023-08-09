<?php 

session_start();
      if (isset($_POST["authItem"])){
        if($_POST["TargetUser"] == ''){
          echo "Target User can not be NULL";
        }else{echo 'Data recieved, process!'.$_POST["authItem"].' --> '.$_POST["TargetUser"];
        echo 'Your AuthCode is'.$_POST["authGen"];
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "leltar_master";
        
        $authCode = $_POST["authGen"];
        $authItem = $_POST["authItem"];
        $SESSuserName = $_SESSION['UserUserName'];
        $authTarget = $_POST["TargetUser"];


            $conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase); // FIRST, This database stores the currently used authCodes,

              if ($conn->connect_error){
                die("Connection failed: ".mysqli_connect_error());}
        $sql=("INSERT IGNORE INTO authcodedb (`ID`,`Code`, `AuthBy`, `AuthUser`, `TakeID`, `Item`) VALUES (NULL, '$authCode', '$SESSuserName', '$authTarget', '1', '$authItem')");
        //$sql=("INSERT INTO authcodedb (`ID`,`Code`, `AuthBy`, `AuthUser`, `TakeID`, `Item`)
        //SELECT * FROM (SELECT '$authItem') AS tmp
        //WHERE NOT EXISTS (
        //    SELECT name FROM authcodedb WHERE name = 'name1'
        //) LIMIT 1;");
        $result = $conn->query($sql);
        
        if ($result===TRUE){
          echo"Success!! AuthCode given, and done.";
          $conn->close();
          $conn = new mysqli($serverName, $dbUserName, $dbPassword, 'leltar_master'); // SECOND CONN, Sets status in master!
          $sql=("UPDATE leltar SET `AuthState` = 1 WHERE `leltar`.`Nev` = '$authItem'");
          $result = $conn->query($sql); if($result===TRUE){echo "AuthCode Update DONE...";}
        }
        header("Location: ../profile/pfcurr.php");
        }
      }
?>
