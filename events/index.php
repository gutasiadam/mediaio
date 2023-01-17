<?php
session_start();
if(!isset($_SESSION['userId'])){
  header("Location: ../index.php?error=AccessViolation");}
#echo $_SESSION['color'];
?><html lang='en'>
  <head>
  <link href='../main.css' rel='stylesheet' />
  <div class="UI_loading"><img class="loadingAnimation" src="../utility/mediaIO_loading_logo.gif"></div>
    <meta charset='utf-8' />
    <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>
    <link href='./core/main.css' rel='stylesheet' />
    <link href='./daygrid/main.css' rel='stylesheet' />
    <link href='./timegrid/main.css' rel='stylesheet' />
    <script src='./interaction/main.css'></script>

    <script src='./core/main.js'></script>
    <script src='./daygrid/main.js'></script>
    <script src='./timegrid/main.js'></script>
    <script src='./interaction/main.js'></script>

   
  <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet' />
  <script src="./moment/main.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
  if(($_SESSION['role']==1)){
    echo '<script src="./defaultCalendarRender.js"></script>';
  }else{  echo '<script src="./adminCalendarRender.js"></script>';}
?>
  <!-- HOZZÁADÁS MODAL -->
  </head>
  <script>
    $(window).on('load', function () {
  $(".UI_loading").fadeOut("slow");
 });
  </script>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
						<script>
            $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft('events',menuItems,2);
            });
            </script>
           </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>
            <?php if ($_SESSION['role']>=3){
              echo '<li><a class="nav-link disabled" href="#">Admin jogok</a></li>';}?>
					  </ul>
	<form method="post" class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
                      <button class="btn btn-danger my-2 my-sm-0" name="logout-submit" type="submit">Kijelentkezés</button>
                      </form>
            <a class="nav-link my-2 my-sm-0" href="../help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
		</nav>
    <body>

    
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <strong>Kedves <?php echo $_SESSION['firstName'];?>!</strong> Az oldal nem támogatja a Firefox böngészőt. Ha azt használod, kérlek válts egy másik böngészőre.
</div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Esemény hozzáadása</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6>Esemény hozzáadása <span id="addEventInterval"></span> időben</h6>
        <form id="sendAddEvent" class="form-group">
        <select class="form-control" id="eventTypeSelect" required>
      <option value="" selected disabled hidden>Típus</option>
      <?php if ($_SESSION['role']>=3){
        echo '<option value="#ff6363">Délelőtti iskolai esemény</option>
        <option value="#db4040">Délutáni iskolai esemény</option>
        <option value="#bd7966">Hétvégi iskolai esemény</option>
        <option value="#59ffba">Workshop</option>
        <option value="#fffd6b">Szünet</option>
        <option value="#81c773">Gyűlés</option>';
      } ?>
      <option value="#ffb145">Külsős esemény</option>
      <option value="#917fe3">Otthoni munka</option>
      <option value="#787878">Egyéb</option></select>
      </br>
        <input class="form-control" id="addEventName" type="text" placeholder="esemény címe"></input></br>
        <h6 class="mailSend"><i class="fas fa-exclamation-circle"></i> Hozzáadás után az e-mail címedre (<?php echo $_SESSION['email'];?>) érkezni fog egy levél. Kérlek ellenőrizd az adatokat, és az <strong>esemény hozzáadása</strong> linkkel erősítsd meg
        szándékodat. <u>(megerősítés után már nem tudod törölni az eseményt.)</u></h6>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" class="btn btn-primary"></button>
        <input type="hidden" id="addEventStartVal"></input>
        <input type="hidden" id="addEventEndVal"></input>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- OPCIÓK MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="optionsLabel">Opcíók</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <?php if ($_SESSION['role']>=3){
        echo ' <form id="sendDelEvent">
        <input type="submit" class="btn btn-danger" value="Törlés"></button>
        <input type="hidden" id="delEventId"></input>
        <input type="hidden" id="delEventTitle"></input>
        </form>';
      } ?>
        <form id="worksheetShow" name="worksheetShow" onsubmit="workSheetPrepare(this);">
        <input type="submit" class="btn btn-dark" value="Munkalap megtekintése"></button>
        <input type="hidden" id="delEventId"></input>
        <input type="hidden" id="delEventTitle"></input>
        </form>
      </div>
      <div class="modal-footer">
      <span id="deleteEventName"></span>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
      </div>
    </div>
  </div>
</div>


<table class="table table-bordered" height=90%><tr>
    <td width=100%><div id='calendar'></td></div>
    <td>
  <h3 class="text-dark">Eseménynaptár - segítség</h3>
  <span class="badge badge-success">Hozzáadás</span><h6 class="text-dark">Jelöld ki a naptárban az időszakot, majd töltsd ki a felugró ablakot</h6>
  <span class="badge badge-danger">Törlés</span><h6 class="text-dark">Kattints rá az adott eseményre, majd válaszd ki a törlés opciót</h6>
  <span class="badge badge-info">Áttevés</span><h6 class="text-dark">Húzd át az eseményt egy másik napra/időpontra</h6>
  <span class="badge badge-dark">Rövidítés/hosszabítás</span><h6 class="text-dark">Heti nézetben kezdd el az eseményt le/felfele húzni, akkár több napon át.</h6>
  <p><h4 class="text-dark">Eseménytípusok színei</h4><table style="table-layout: fixed;">
  <tr><td class="text-dark" style="background-color:#ff6363;"> Délelőtti iskolai esemény </td>  <td class="text-dark" style="background-color: #81c773; "> Gyűlés </td>  <td class="text-dark" style="background-color: #59ffba;"> Workshop </td></tr>
  <tr><td class="text-dark" style="background-color: #ffb145;"> Külsős esemény </td>  <td class="text-dark" style="background-color: #db4040;">  Délutáni iskolai esemény </td>     <td class="text-dark" style="background-color: #917fe3;"> Otthoni munka </td></tr>
  <tr><td class="text-dark" style="background-color: #fffd6b;"> Szünet </td>   <td class="text-dark" style="background-color: #787878;"> Egyéb </td>    <td class="text-dark" style="background-color: #bd7966;"> Hétvégi iskolai esemény </td></tr>
  </table></p></td>
</table>

  </body>
</html>
<style>
.sideHelp {
  height: 100%; /* 100% Full-height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  z-index: 1; /* Stay on top */
  top: 0; /* Stay at the top */
  left: 0;
  background-color: #222; /* Black*/
  overflow-y: hidden;
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px; /* Place content 60px from the top */
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
  padding-left: 10px;
}

table{
  text-align: left;
}

.closebtn{
  color:white;
  transition: .8s ease-in-out;
  display: block;
}
.closebtn:hover{
  color:red;
  transform: rotateX(45deg);
  transition: 0.5s;
  -webkit-transform:rotateX(45deg);
   -moz-transform:rotateX(45deg);
   -o-transform:rotateX(45deg); 
}
#calendar{
  margin-left: 2%;
  width: 90%;
}

#deleteEventName{
  position: relative;
  color: #dbdbdb;
  text-align: left;
  font-size: 10;
  align:right;
}

#exampleModalLabel{
  position: absolute;
  font-size: 30;
}
</style>

 <script>
function openNav() {
  document.getElementById("sideHelp1").style.width = "250px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("sideHelp1").style.width = "0";
}

(function(){
  setInterval(updateTime, 1000);
});

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
      minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (timer > 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html(display.textContent).animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").text(display.textContent).animate({'opacity': 1}, 250); }, 700);;});
        }

        if (timer < 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 250); }, 700);;});
        }

        if (--timer < 0) {
            timer = duration;
            window.location.href = "../utility/logout.ut.php"
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

$( document ).ready(function() {
  $(".mailSend").hide();
});
 </script>