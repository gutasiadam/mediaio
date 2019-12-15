<?php 
  include "translation.php";
  include "version.php";
  error_reporting(E_ALL ^ E_NOTICE);
  //require 'header.php'; NOT NECESSARY, SHOULD BE USED IN THE FUTURE
?>
<!DOCTYPE html>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<html data-theme='dark'>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="./main.css">
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <header>
                <?php 
                if(isset($_SESSION['userId'])){
                    date_default_timezone_set("Europe/Budapest"); 
                    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php">Arpad Media IO</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item active ">
						    <a class="nav-link" href="./index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						    <a class="nav-link" href="./takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item ">
						    <a class="nav-link" href="./retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						    <a class="nav-link" href="./adatok.php"><i class="fas fa-database fa-lg"></i></a>
						</li>
            <li class="nav-item">
                        <a class="nav-link" href="./pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="./events/xd.php"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="./profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">'.$nav_timeLockTitle.' <span id="time">'.$nav_timeLock_StartValue.'</span></a>
            </li>';
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <a class="nav-link my-2 my-sm-0" href="./help.php"><i class="fas fa-question-circle fa-lg"></i></a>
            </div>
      </nav>
      ';
            }
            else{
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <a class="nav-link my-2 my-sm-0" href="./help.php"><i class="fas fa-question-circle fa-lg"></i></a>
            </div>
      </nav>
      ';
            }
					  
                    ?>
                    <?php
                }else{
                    echo '';
                }
          // Handle specific GET requests
          if($_GET['signup'] == "success"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-success"><strong> - </strong>Sikeres regisztráció! </div></tr></td></table>';
          }
          if($_GET['logout'] == "success"){
            echo '<table align=center width=400px class=successtable><tr><td><div class="alert alert-info">'.$alert_logout_successful.' </div></tr></td></table>';
          }
          if($_GET['logout'] == "pwChange"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-info">Successfully changed password! </div></tr></td></table>';
          }
          if($_GET['error'] == "WrongPass"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-danger"><strong>Hiba - </strong>Helytelen jelszó! </div></tr></td></table>';
          }
          if($_GET['error'] == "NoUser"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-danger"><strong>Hiba - </strong>Hibás felhasználónév / jelszó! </div></tr></td></table>';
          }
          if($_GET['error'] == "AccessViolation"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-danger"><strong>Hiba - </strong>Ehhez a funkcióhoz be kell jelentkezned! </div></tr></td></table>';
          }
                ?>
            </ul>
        </nav>
    </header>
	<body>
		
    <?php if(!isset($_SESSION['userId'])){echo '
                    
                    <form action="utility/login.ut.php" method="post" class="formmain" id="formmain" autocomplete="off" >
                    <h1 align=center class="rainbow">'.$applicationTitleShort.' </h1>
		                <h4 align=center>'.$application_version_text.$application_Version.'</h4>
                   <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="text" name="useremail" placeholder="Felhasználónév/E-mail" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="password" name="pwd" placeholder="Jelszó" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><button class="btn btn-dark" type="submit" name="login-submit" align=center>Bejelentkezés</button></div></div>
                    </div>
                    </form>';}
            else{
              echo '
              <h1 align=center class="rainbow">Árpád Média IO </h1>
		                <h4 align=center>'.$application_version_text.$application_Version.'</h4>
              <div class="row justify-content-center" style="text-align: center;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./takeout.php"><i class="fas fa-upload fa-3x"></i><br><h5>'.$index_takeOut.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="./retrieve.php"><i class="fas fa-download fa-3x"></i><br><h5>'.$index_Retrieve.'</h5></a></div>
              </div>
              <br>
              <div class="row justify-content-center" style="text-align: center;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./adatok.php"><i class="fas fa-database fa-3x"></i><br><h5>'.$index_Data.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="./pathfinder.php"><i class="fas fa-project-diagram fa-3x"></i><br><h5>'.$index_PathFinder.'</h5></a></div>
              </div>
              <br>
              <div class="row justify-content-center" style="text-align: center;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./profile/index.php"><i class="fas fa-user-alt fa-3x"></i><br><h5>'.$index_Profile.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="./help.php"><i class="fas fa-question-circle fa-3x"></i><br><h5>'.$index_Help.'</h5></a></div>
            </div>';
            }?>
    
    
      
	</body>
<style>
.successtable{
  width: 30%;
}

.logintable{
  width: 15%;
  text-align: center;
  margin: 0 auto; 
}

.nav{
  align:left;
}

.col-6{
  border-style: solid;
  border-width: 1px;
  transition: 0.5s;
  text-align:center;
}

.col-6:hover{
  background-color:#3b3b3b;
  border-radius: 25px;
}

.ab{
  color: #3b3b3b;
  transition: 0.5s;
}

.ab:hover{
  color: #ffffff;
}

.formmain{
  position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        width: 90%;
        transform: translate(-50%, -50%) }
}
</style>

<script type="text/javascript">

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
            window.location.href = "/utility/logout.ut.php"
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