<?php
namespace Mediaio;
use Mediaio\Database;
require_once "../Database.php";
session_start();
include "header.php";


error_reporting(E_ALL ^ E_NOTICE);

// Prevent unauthorized access
if(!in_array("system", $_SESSION["groups"])) {
    echo "Nincs jogosultságod az oldal megtekintéséhez!";
    exit();
}
?>

<html>  
    <head>
  <script>
  var imodal=0;
  
</script>
    </head>
<?php if (
    isset($_SESSION["userId"])
) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems,2);
        });
      </script>
    </ul>
    <ul class="navbar-nav ms-auto navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
  </div>
</nav> <?php } ?>
    <body>  
  <div class="container">
   <br />
   <h1 align="center">Felhasználói jogkörök</h1><br>

   <?php
   $TKI = $_SESSION["UserUserName"];
   $sql = "SELECT * FROM `users`";
   $result = Database::runQuery($sql);
   $imodal = 0;
    $resultArray = array();
   while ($row = $result->fetch_assoc()) {
       array_push($resultArray, $row);
      //  $rangok = ["médiás", "stúdiós", "sadmin", "admin", "sysadmin"];
       $rangColors = ["disabled", "info", "success", "warning", "danger"];
       $rowItem = $row["firstName"] . $row["lastName"];
       //convert AdiitionalData row to JSON, then get the value of presets, userColor
       
        if($row["AdditionalData"] != null){
          
          //implode groups array to string
          $groupData=json_decode($row["AdditionalData"],true);
          //if usernameColor is not set, set it to black
          if(!isset($groupData["presets"]["usernameColor"])){
             $usernameColor = "#000000";
          }else{
            $usernameColor = $groupData["presets"]["usernameColor"];
          }
          
          //store every array value of groupData["groups"] in a string
          $groups=implode(", ",$groupData["groups"]);
        }
        else{
       $rowItem = json_decode($row["AdditionalData"], true);
        $usernameColor = "#000000";
        $groups="?";
        }

       echo '
                <div class="row">
                <div class="col-sm">
                 <h2>' .
           $row["lastName"] .
           " " .
           $row["firstName"] .
           '</h2>
                 <p id=uN' .
           $imodal .
           ">" .
           $row["usernameUsers"] .
           '</p>
                </div>
                <div class="col-2">
                <h2 class="text text-disabled' .
           '" style="color:'.$usernameColor.'">' .
           $groups;
           echo "</h2></div>";
       if (in_array("admin", $_SESSION["groups"])) { ?>
    <?php if ($row["usernameUsers"] != $TKI && in_array("system", $_SESSION["groups"])) {
        echo '
                  <form method="POST" action=../Core.php>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="mediaCheckbox" id="mediaCheckBox"';
          if(in_array("média",$groupData["groups"])){
            echo 'checked';
          }
          echo '>
          <label class="form-check-label" for="eventCheckBoxLabel">média</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="studioCheckbox" id="studioCheckBox"';
          if(in_array("studio",$groupData["groups"])){
            echo 'checked';
          }
          echo '>
          <label class="form-check-label" for="studioCheckBoxLabel">stúdiós</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="eventCheckbox" id="eventCheckBox"';
          if(in_array("event",$groupData["groups"])){
            echo 'checked';
          }
          echo '>
          <label class="form-check-label" for="eventCheckBoxLabel">event</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="teacherCheckbox" id="teacherCheckBox"';
          if(in_array("teacher",$groupData["groups"])){
            echo 'checked';
          }
          echo '>
          <label class="form-check-label" for="teacherCheckBoxLabel">tanár</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="adminCheckbox" value="4"';
          if(in_array("admin",$groupData["groups"])){
            echo 'checked';
          }
          echo '>
          <label class="form-check-label" for="adminCheckBoxLabel">admin</label>
          <input type="hidden" name="userName" value=' .$row["usernameUsers"] .'>
          <input type="hidden" name="pointUpdate" value="1">
        </div>';
        if ($row["usernameUsers"] != $TKI && in_array("system", $_SESSION["groups"])) {
            echo '<button class="btn btn-warning" type="submit">Módosítás</button>';
        }
        echo "</form>";
    } ?></div>
                  <?php $imodal++;}
   }
   ?>
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">        

    </body>  
</html>
<style>
.timeline__item{
  background-color: #ededed;
}
</style>