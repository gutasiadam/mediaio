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
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../../index.php">
    <img src="../../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("../../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems,3);
        });
      </script>
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
    </form>
    <a class="nav-link my-2 my-sm-0" href="../../help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav> <?php  } ?> 
<div class="contianer">
  <div class="row" style="width: 80%; margin: 0 auto;">
  <div class="col-sm">
  <form name="damageForm">
            <select id="selectItem" id='currOutItems' class="form-select" aria-label="Default select example" onchange="changeFunc();">
                
                <?php 
                if ($_SESSION["role"] >= 3) {
                  echo "<option  selected>Válassz a nálad levő, kivitt vagy megerősítésre váró tárgyak közül</option>";
                  $sql = ("SELECT * FROM `leltar` WHERE `Status` != 1");
                }else{
                  $sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI'");
                  echo "<option  selected>Válassz a nálad levő tárgyak közül</option>";
                }
                
                $result = Database::runQuery($sql);
              while($row = $result->fetch_assoc()) { 
                echo '<option value='.$row['UID'].'>'.$row['Nev'].'- ('.$row['UID'].')</option>';
              }
                ?>
              </select>

            <div class="form-group">
              <label for="message-text" class="col-form-label">Leírás (mi történt pontosan?)</label>
              <textarea class="form-control" id="err_description_long"></textarea>
            </div>
  Itt tudsz képet feltölteni:
  <input id="fileToUpload" type="file" accept="image/*" name="image" />
  <img id="imagePreview" src="#" alt="your image" style="max-width: 250px; margin: 0 auto;" />
  <!--<input class="button btn-success" type="submit" value="Feltöltés" name="submit">-->
            </br>
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
    Csatolt kép(ha van):<br>
    <img id="imagePreview_beforeSend" src="#" alt="your image" style="max-width: 250px; margin: 0 auto;" />
    <p> Beküldés után mihamarabb megkeres majd egy Vezetőségi tag. Köszönjük, hogy jelentetted a sérülést!</p>
      </div>
      <div class="modal-footer">
        <p id="mailSendState"></p>
        <button type="button" id="sendBtn" class="btn btn-success">Küldés</button>
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

$(document).ready(function (e) {

  $("#sendBtn").on('click',(function(e) {
//   e.preventDefault();
//   $.ajax({
//          url: "upload.php",
//    type: "POST",
//    data:  new FormData($("#damageForm")[0]),
//    contentType: false,
//          cache: false,
//    processData:false,
//    success: function(data)
//       {
//     alert(data);
//     if(data=='invalid')
//     {
//      // invalid file format.
//      //$("#err").html("Invalid File !").fadeIn();
//     }
//     else
//     {
//      // view uploaded file.
//      //$("#preview").html(data).fadeIn();
//      $("#form")[0].reset(); 
//     }
//       }        
//     });
send_report();
}));
 
});

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
      setTimeout(function(){location.reload();},2000);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      document.getElementById("mailSendState").innerHTML =("Hiba: " + errorThrown); 
        setTimeout(function(){location.reload();},2000);
    }
    
});
}

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


    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
              
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreview_beforeSend').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#fileToUpload").change(function(){
        readURL(this);
        document.getElementById("mailSendState").innerHTML =('A képek feltöltése jelenleg nem működik.');
    });

</script>

