<script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
<div id="like_button_container"></div>
<script src="React_navBar.js"></script>

<?php
session_start();
if(!isset($_SESSION['userId'])){
  header("Location: ../index.php?error=AccessViolation");}
#echo $_SESSION['color'];
?><html lang='en'>
  <head>
  <div class="se-pre-con"><img src="loading.gif"></div>
  <style>
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
  position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
  text-align: center;
  z-index: 9999;
}

  </style>
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

   
  <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet' />
  <script src="./moment/main.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
  if(($_SESSION['role']=="Default")){
    echo '<script src="./defaultCalendarRender.js"></script>';
  }else{  echo '<script src="./adminCalendarRender.js"></script>';}
?>
  <!-- HOZZÁADÁS MODAL -->
  </head>
  <script>
    $(window).on('load', function () {
  $(".se-pre-con").fadeOut("slow");
 });
  </script>
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


  </body>
</html>


 <script>
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
/*
window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};*/

$( document ).ready(function() {
  $(".mailSend").hide();
});
 </script>