<?php 
namespace Mediaio;
use Mediaio\Database;
require_once '../Database.php';
include "header.php";

session_start();
error_reporting(E_ALL ^ E_NOTICE);
if(($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
    error_reporting(E_ALL ^ E_NOTICE);}
    $serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }


?>

<html>  
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <script>
  var imodal=0;
  function rangTipus(i){
    switch(i){
      case(2):
        return "studio";
      case(4):
        return "admin";
      case(6):
        return "sadmin";
      default:
        return "default";
    }
  }
//$('input#adminCheckBox.form-check-input')[0].checked
</script>
<script>
function ertek(imodal){
var ertek;
if($('input#adminCheckBox'+imodal+'.form-check-input')[0].checked==false && $('input#studioCheckBox'+imodal+'.form-check-input')[0].checked==true){
  ertek=2;
}else if($('input#adminCheckBox'+imodal+'.form-check-input')[0].checked==true && $('input#studioCheckBox'+imodal+'.form-check-input')[0].checked==false){
  ertek=4;
}else if($('input#adminCheckBox'+imodal+'.form-check-input')[0].checked==true && $('input#studioCheckBox'+imodal+'.form-check-input')[0].checked==true){
  ertek=3;
}else{
  ertek=1;
}
  var uName=$('p#uN'+imodal)[0].innerText;
  alert(uName);
  return ertek;
}
imodal++;
</script>
  <title>Felhasználói jogok</title>
    </head>
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span><?php if ($_SESSION['role']>=3){echo' Admin jogok';}?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav> <?php  } ?>
    <body>  
  <div class="container">
   <br />
   <h1 align="center">Felhasználói jogok</h1><br>

   <?php 
        $TKI = $_SESSION['UserUserName'];    
        $sql = ("SELECT * FROM `users`");
        $result = Database::runQuery($sql);
        $imodal=0;
        $resultArray = [];
        
          while($row = $result->fetch_assoc()) { 
              array_push($resultArray, $row);
                $rangok=["default","stúdiós","sadmin","admin","sysadmin"];
                $rangColors=["disabled","info","success","warning","danger"];
                $rowItem = $row["firstName"].$row["lastName"];
                echo '
                <div class="row">
                <div class="col-4">
                 <h2>'.$row["lastName"]." ".$row["firstName"].'</h2>
                 <p id=uN'.$imodal.'>'.$row["usernameUsers"].'</p>
                </div>
                <div class="col-2">
                <h2 class="text text-'.$rangColors[$row["Userrole"]-1].'">'.$rangok[$row["Userrole"]-1].'</h2></div>';
                if($_SESSION['role']>="3"){
                  ?>
    <?php if($rangok[$row["Userrole"]-1]!='sysadmin'){
      echo '
                  <form method="POST" action=../Core.php>
                  <div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="adminCheckbox" value="4">
  <label class="form-check-label" for="adminCheckBoxLabel">admin</label>
  <input type="hidden" name="userName" value='.$row["usernameUsers"].'>
  <input type="hidden" name="pointUpdate" value="1">
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="studioCheckbox" id="studioCheckBox" value="2">
  <label class="form-check-label" for="studioCheckBoxLabel">stúdiós</label>
</div>';
    if($row["usernameUsers"]!=$TKI){
      echo '<button class="btn btn-warning" type="submit">Módosítás</button>';
    }
    echo '</form>';
}?></div>
                  <?php
                  $imodal++;
                }

            }?>
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
<script>
function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
            window.location.href = "../utility/logout.ut.php";
        }
    }, 1000);
}

window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};

</script>