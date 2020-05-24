<?php 
  include "translation.php";
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
                  $host=$ftp_ip;
$output=shell_exec('ping -n 1 '.$host);

if (strpos($output, 'out') !== false) {
    $state = "red";
}
    elseif(strpos($output, 'expired') !== false)
{
  $state = "yellow";
}
    elseif(strpos($output, 'data') !== false)
{
  $state = "green";
}
else
{
  $state = "black";
}
                    date_default_timezone_set("Europe/Budapest"); 
                    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-all" id="nav-head">
					<a class="navbar-brand" href="index.php"><img src="./utility/logo.png" height="30"></a>
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
                        <a class="nav-link" href="./events/"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="./profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">'.$nav_timeLockTitle.' <span id="time">'.$nav_timeLock_StartValue.'</span></a>
            </li>
            <li>
            <a class="nav-link disabled" id="ServerMsg" href="#"></a>
            </li>';
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#"></a></li>';
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
                }
          // Handle specific GET requests
          
                ?>
            </ul>
        </nav>
    </header>
	<body>
		
    <?php if(!isset($_SESSION['userId'])){?>
                    <div class="loginbox">
                    <form action="utility/login.ut.php" method="post" class="formmain" id="formmain" autocomplete="off" >
                    <h6 align=center width="50%" id="SystemMsg" class="successtable2" style="display:none;">XD</h6>
                    <h1 align=center class="rainbow"><?php echo $applicationTitleFull;?></h1>
		                <h4 align=center><?php echo $application_version_text.$application_Version;?></h4>
                   <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="text" name="useremail" placeholder="Felhasználónév/E-mail" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="password" name="pwd" placeholder="Jelszó" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><button class="btn btn-dark" type="submit" name="login-submit" align=center>Bejelentkezés</button></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><a href="./pwReset.php">Elfelejtett jelszó</a></div></div>
                    </div>
                    </form><footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p><?php echo $applicationTitleFull; ?> <strong>ver. <?php echo $application_Version; ?></strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a></p></div></footer>
                    </div> <?php ;}
               else{ ?>
              <div class="alert alert-warning alert-dismissible fade show" id="note" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <strong>Kedves <?php echo $_SESSION['firstName'] ?>!</strong> Az oldal <u>folyamatos fejlesztés</u> alatt áll. Ha hibát szeretnél bejelenteni/észrevételed van, írj az arpad.media.io@gmail.com címre, vagy <a href="mailto:arpad.media.io@gmail.com?Subject=MediaIO%20Hibabejelent%C3%A9s" target="_top">írj most egy e-mailt!</a>
</div>
              <h1 align=center class="rainbow">Árpád Média IO </h1>
		                <h4 align=center><?php echo $application_version_text.$application_Version; ?>'</h4>
              <div class="row justify-content-center" style="text-align: center; width:100%; margin: 0 auto;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./takeout.php"><i class="fas fa-upload fa-3x"></i><br><h5><?php echo $index_takeOut; ?></h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="./retrieve.php"><i class="fas fa-download fa-3x"></i><br><h5><?php echo $index_Retrieve ?></h5></a></div>
              </div>
              <br>
              <div class="row justify-content-center" style="text-align: center; width:100%; margin: 0 auto;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./adatok.php"><i class="fas fa-database fa-3x"></i><br><h5><?php echo $index_Data; ?></h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="./pathfinder.php"><i class="fas fa-project-diagram fa-3x"></i><br><h5><?php echo $index_PathFinder ?></h5></a></div>
              </div>
              <br>
            <div class="row justify-content-center" style="text-align: center; width:100%; margin: 0 auto;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./adatok.php"><i class="fas fa-calendar-alt fa-3x"></i><br><h5>Naptár</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="http://80.99.70.46/mftp" target="_blank"><i class="fas fa-server fa-3x" style="color:<?php echo $state; ?>"></i><br><h5>Fájlszerver</h5></a></div>
              </div>
              <br>
              <div class="row justify-content-center" style="text-align: center; width:100%; margin: 0 auto;">
              <div class="col-6 col-sm-2"><a class="nav-link ab" href="./profile/index.php"><i class="fas fa-user-alt fa-3x"></i><br><h5><?php echo $index_Profile; ?></h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="./help.php"><i class="fas fa-question-circle fa-3x"></i><br><h5><?php echo $index_Help; ?></h5></a></div>
            </div>
              <footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p><?php echo $applicationTitleFull; ?> <strong>ver. <?php echo $application_Version; ?></strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a>
            Socket kommunikáció állapota: <span id='webSocketState' style="width: 10px; height:10px; display: inline-block;"></span>
            </p></div></footer>';
            <script type = "text/javascript">
            $( document ).ready(function() {
              WebSocketTest();
            });
            //Websocket kommunikáció felállítása.
            function WebSocketTest() {
            
            if ("WebSocket" in window) {
               console.log("WebSocket is supported by your Browser!");
               var ws = new WebSocket("ws://192.168.0.24:3000/ws");
               // Let us open a web socket
				
               ws.onopen = function() {
                  
                  // Web Socket is connected, send data using send()
                  //ws.send("I Joined the network!");
                  document.getElementById('webSocketState').style.backgroundColor = ('lime');
                  console.log("Message is sent to the network");
               };
				
               ws.onmessage = function (evt) { 
                var received_msg = evt.data;

                try {
                    let m = JSON.parse(evt.data);
                     handleMessage(m);
                } catch (err) {
                    console.log('[Client] Message is not parseable to JSON.');
                }

                  console.log("Message recieved: " + received_msg);
                  document.getElementById('recMsg').innerHTML = (received_msg);
               };
				
               ws.onclose = function() { 
                  
                  // websocket is closed.
                  console.log("Connection is closed..."); 
                  document.getElementById('webSocketState').style.backgroundColor = ('red');
                  document.getElementById("ServerMsg").style.backgroundColor = ('LightCoral');
                  document.getElementById("ServerMsg").style.color = ('white');
                  document.getElementById('ServerMsg').innerHTML = ('A szerverrel való kommunikáció megszakadt. Próbáld meg újratölteni az oldalt.');
               };

               let handlers = {
                "set-background-color": function(m) {
        // ...
                console.log('[Client] set-background-color handler running.');
                console.log('[Client] Color is ' + m.params.color);
                document.getElementById('webSocketState').style.backgroundColor = (m.params.color);
                }
            };


               function handleMessage(m) {

                if (m.method == undefined) {
                    return;
                }

                let method = m.method;

                if (method) {

                    if (handlers[method]) {
                        let handler = handlers[method];
                        handler(m);
                    } else {
                        console.log('[Client] No handler defined for method ' + method + '.');
                    }

                }
        }
            } else {
              
               // The browser doesn't support WebSocket
               console.log("WebSocket NOT supported by your Browser!");
            }
         }
            </script>
            <?php }
            //GET változók kezelése
            
            if($_GET['signup'] == "success"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Sikeres regisztráció!";
              document.getElementById("SystemMsg").className = "alert alert-success successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['logout'] == "success"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Sikeres kijelentkezés!";
              document.getElementById("SystemMsg").className = "alert alert-success successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';} // ÁTMÁSOLNI
            if($_GET['logout'] == "pwChange"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Sikeres jelszócsere!";
              document.getElementById("SystemMsg").className = "alert alert-success successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['error'] == "WrongPass"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Helytelen jelszó!";
              document.getElementById("SystemMsg").className = "alert alert-danger successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['error'] == "NoUser"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Hibás felhasználónév / jelszó!";
              document.getElementById("SystemMsg").className = "alert alert-danger successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['error'] == "AccessViolation"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Ehhez a funkcióhoz be kell jelentkezned!";
              document.getElementById("SystemMsg").className = "alert alert-danger successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['logout'] == "WrongAuth"){
              echo '<script>document.getElementById("SystemMsg").innerHTML="Hibás Authenticator kód!";
              document.getElementById("SystemMsg").className = "alert alert-danger successtable";
              $("#SystemMsg").fadeIn();
              setTimeout(function(){ $("#SystemMsg").fadeOut(); }, 6000);
              </script>';}?>

	</body>
<style>
.successtable{
  width: 30%;
  margin: 0 auto;
  margin-bottom: 10px;
}

.successtable2{
  width: 30%;
  margin: 0 auto;
  margin-bottom: 10px;
  color: white;
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

#note {
  z-index: 10;
}

.formmain{
  position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        width: 30%;
        transform: translate(-50%, -50%);
        background-color: #ededed;
        border-radius: 50px;
        box-shadow: 10px 6px 50px grey; }
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
            window.location.href = "utility/logout.ut.php"
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