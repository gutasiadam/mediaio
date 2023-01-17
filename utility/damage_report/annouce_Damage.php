<?php
namespace Mediaio;
require_once __DIR__.'/../../Mailer.php';
require_once __DIR__.'/../../Database.php';
use Mediaio\MailService;
use Mediaio\Database;
error_reporting(E_ALL ^ E_NOTICE);
session_start();
$TKI = $_SESSION['UserUserName'];  
?>

<!--Hibabejelentő űrlap, tartalmazza:
- A tárgy nevét, amivel gond van
- A bejelentő nevét
- Képfeltöltés lehetőségét
- Hiba leírását
-->
<head>
  <link href='../../main.css' rel='stylesheet' />
  <div class="UI_loading"><img class="loadingAnimation" src="../mediaIO_loading_logo.gif"></div>
    <meta charset='utf-8' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <script src="../../utility/_initMenu.js" crossorigin="anonymous"></script>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
    $(window).on('load', function () {
      console.log("Finishing UI");
      setInterval(() => {
        $(".UI_loading").fadeOut("slow");
      }, 200);
 });
  </script>
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      
            <a class="navbar-brand" href="../index.php"><img src="../../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
          
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){ ?>
              <li><a class="nav-link disabled" href="#">Admin jogok</a></li> <?php  }?>
            </ul>
            <form method="post" class="form-inline my-2 my-lg-0" action=../userLogging.php>
                <button class="btn btn-danger my-2 my-sm-0" name="logout-submit" type="submit">Kijelentkezés</button>
            </form>
                      <div class="menuRight"></div>
					</div>
    </nav>
<script> $( document ).ready(function() {
              menuItems = importItem("../../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,3);
              drawMenuItemsRight('profile',menuItems,3);
            });</script>
<div class="contianer">
  <div class="row" style="width: 80%; margin: 0 auto;">
  <div class="col-sm">
  <form>
            <select id="selectItem" id='currOutItems' class="form-select" aria-label="Default select example" onchange="changeFunc();">
                <option  selected ">Válassz a nálad levő tárgyak közül</option>
                <?php 
                $sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI'");
                $result = Database::runQuery($sql);
              while($row = $result->fetch_assoc()) { 
                echo '<option value='.$row['UID'].'>'.$row['Nev'].'- ('.$row['UID'].')</option>';
              }
                ?>
              </select>
              <form action="../upload-image.php" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload Image" name="submit">
</form>
            <div class="form-group">
              <label for="message-text" class="col-form-label">Leírás (mi történt pontosan?)</label>
              <textarea class="form-control" id="err_description_long"></textarea>
            </div>
            <a href="../index.php"><button type="button" class="btn btn-secondary">Mégsem</button></a>
          <button type="button" class="btn btn-info" data-toggle="modal" data-target="#checkModal">Küldés</button>
  </form>
  </div>

<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="checkLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ellenőrizd az adatokat beküldés előtt!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Mégsem">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="text-align: center;">
      <h1 id="itemName">-</h1>
    </hr>
    <h2 id="itemUID">-</h2>
    <h4 id="userName"><?php echo $TKI; ?></h4>
    <h6 id="error_description">-</h6>
    <p> Beküldés után mihamarabb megkeres majd egy Vezetőségi tag. Köszönjük, hogy jelentetted a sérülést!</p>
      </div>
      <div class="modal-footer">
        <p id="mailSendState"></p>
        <button type="button" class="btn btn-success" onclick="send_report()">Küldés</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
      </div>
    </div>
  </div>
</div>
<script>
function changeFunc() {
    console.log('CHANGE');
    var selectBox = document.getElementById("selectItem");
    var selectedValue = selectBox.options[selectBox.selectedIndex].innerHTML;
    var selectedUID = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("itemName").innerHTML = selectedValue;
    document.getElementById("itemUID").innerHTML = selectedUID;
   }


var typingTimer;                //timer identifier
var doneTypingInterval = 50;  //time in ms, 5 second for example
var input = $('#err_description_long');

//on keyup, start the countdown
input.on('keyup', function () {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(doneTyping, doneTypingInterval);
});

//on keydown, clear the countdown 
input.on('keydown', function () {
  clearTimeout(typingTimer);
});

//user is "finished typing," do something
function doneTyping () {
  //do something
  document.getElementById("error_description").innerHTML = document.getElementById("err_description_long").value;
}

function upload_image(){
  document.getElementById("mailSendState").innerHTML="E-mail küldése...";
  $.ajax({
    method: 'POST',
    url: './send_damage_report.php',
    data: {data :mailJSON},
    success: function (response){
      //alert('Válasz:'+response);
      document.getElementById("mailSendState").innerHTML =('Sikeres művelet! Az oldal hamarosan újratölt.');
      setTimeout(function(){location.reload();},5000);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      document.getElementById("mailSendState").innerHTML =("Hiba: " + errorThrown); 
        setTimeout(function(){location.reload();},5000);
    }
    
});
}


function send_report(){
  changeFunc();
  var nev= document.getElementById("itemName").innerHTML;
  var uid= document.getElementById("itemUID").innerHTML;
  data={
    Nev: nev.split('-')[0],
    UID: uid,
    err_description: document.getElementById('error_description').innerHTML
  };
  mailJSON = JSON.stringify(data);
  document.getElementById("mailSendState").innerHTML="E-mail küldése...";
  $.ajax({
    method: 'POST',
    url: './send_damage_report.php',
    data: {data :mailJSON},
    success: function (response){
      alert('Válasz:'+response);
      document.getElementById("mailSendState").innerHTML =('Sikeres művelet! Az oldal hamarosan újratölt.');
      setTimeout(function(){location.reload();},5000);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      document.getElementById("mailSendState").innerHTML =("Hiba: " + errorThrown); 
        setTimeout(function(){location.reload();},5000);
    }
    
});
}
</script>