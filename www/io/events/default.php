<?php
session_start();
if(!isset($_SESSION['userId'])){
  header("Location: ../index.php?error=AccessViolation");}
#echo $_SESSION['color'];
?><html lang='en'>
  <head>
    <meta charset='utf-8' />
    <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>

    <link href='./core/main.css' rel='stylesheet' />
    <link href='./daygrid/main.css' rel='stylesheet' />
    <link href='./timegrid/main.css' rel='stylesheet' />
    <script src='./interaction/main.css'></script>

    <script src='./core/main.js'></script>
    <script src='./daygrid/main.js'></script>
    <script src='./timegrid/main.js'></script>
    <script src='./interaction/main.js'></script>

  <title>Event Calendar @ default</title>
  <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet' />
  <script src="./moment/main.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
  if(($_SESSION['role']=="Default")){
    echo '<script src="./defaultCalendarRender.js"></script>';
  }else{  echo '<script src="./adminCalendarRender.js"></script>';}
?>
  <!-- HOZZÁADÁS MODAL -->


  <link href='../style/events.css' rel='stylesheet'/>
  </head>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php">Arpad Media IO</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item  ">
						    <a class="nav-link" href="../index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						    <a class="nav-link" href="../takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item ">
						    <a class="nav-link" href="../retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						    <a class="nav-link" href="../adatok.php"><i class="fas fa-database fa-lg"></i></a>
						</li>
            <li class="nav-item">
                        <a class="nav-link" href="../pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            </li>
            <li class="nav-item active">
                        <a class="nav-link" href="#"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="../profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
					  <a class="nav-link my-2 my-sm-0" href="#"><span onclick="openNav()"><i class="fas fa-question-circle fa-lg"></i></span></a>
					</div>
</nav>
    <body>
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
        <form id="sendAddEvent">
        <select class="form-control" id="eventTypeSelect" required>
      <option value="" selected disabled hidden>Típus</option>
      <?php if(($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
        echo '<option value="#faa0a0">Délelőtti iskolai esemény</option>
        <option value="#faa0a0">Délutáni iskolai esemény</option>
        <option value="#faa0a0">Hétvégi iskolai esemény</option>
        <option value="#59ffba">Workshop</option>
        <option value="#f2f0a2">Szünet</option>
        <option value="#93ba6d">Gyűlés</option>';
      } ?>
      <option value="#f7e2b7">Külsős esemény</option>
      <option value="#fdffcf">Otthoni munka</option>
      <option value="#c9c9c9">Egyéb</option></select>
        <input class="form-control" id="addEventName" type="text" placeholder="esemény címe"></input></br>
        <h6 class="mailSend"><i class="fas fa-exclamation-circle"></i> Hozzáadás után az e-mail címedre (<?php echo $_SESSION['email'];?>) érkezni fog egy levél. Kérlek ellenőrizd az adatokat, és az <strong>esemény hozzáadása</strong> linkkel erősítsd meg
        szándékodat. <u>(megerősítés után már nem tudod törölni az eseményt.)</u></h6>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" class="btn btn-primary" value="Hozzáadás" ></button>
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
        <h5 class="modal-title" id="exampleModalLabel">Opcíók</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="sendDelEvent">
        <input type="submit" class="btn btn-danger" value="Törlés"></button>
        <input type="hidden" id="delEventId"></input>
        <input type="hidden" id="delEventTitle"></input>
        </form>
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
</table>


    <div id="sideHelp1" class="sideHelp">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><i class="fas fa-times fa-2x"></i></a>
  <h3 class="text-white">Eseménynaptár - segítség</h3>
  <span class="badge badge-success">Hozzáadás</span><h6 class="text-white">Jelöld ki a naptárban az időszakot, majd töltsd ki a felugró ablakot</h6>
  <span class="badge badge-danger">Törlés</span><h6 class="text-white">Kattints rá az adott eseményre, majd válaszd ki a törlés opciót</h6>
  <span class="badge badge-info">Áttevés</span><h6 class="text-white">Húzd át az eseményt egy másik napra/időpontra</h6>
  <span class="badge badge-dark">Rövidítés/hosszabítás</span><h6 class="text-white">Heti nézetben kezdd el az eseményt le/felfele húzni, akkár több napon át.</h6>
  <p><h4 class="text-white">Eseménytípusok színei</h4><table style="table-layout: fixed;">
  <tr><td class="text-dark" style="background-color: #faa0a0;"> <strong>Délelőtti iskolai esemény</strong> </td>  <td class="text-dark" style="background-color: #59ffba;"> Workshop </td>  <td class="text-dark" style="background-color: #fdffcf;"> Otthoni munka </td></tr>
  <tr><td class="text-dark" style="background-color: #f7e2b7;"> Külsős esemény </td>  <td class="text-dark" style="background-color: #ff4d4d;"> Délutáni iskolai esemény </td>     <td class="text-dark" style="background-color: #f2f0a2;"> Szünet </td></tr>
  <tr><td class="text-dark" style="background-color: #93ba6d;"> Gyűlés </td>   <td class="text-dark" style="background-color: #c9c9c9;"> Egyéb </td>    <td class="text-dark" style="background-color: #faa0a0;"> Hétvégi iskolai esemény </td></tr>
  </table></p>
</div>
  </body>
</html>

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