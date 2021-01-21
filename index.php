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
  <script src="utility/_initMenu.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
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
					<a class="navbar-brand" href="index.php"><img src="./utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            <li>
            <a class="nav-link disabled" id="ServerMsg" href="#"></a>
            </li></ul>';?>
            <script>
            $( document ).ready(function() {
              menuItems = importItem("./utility/menuitems.json");
              drawMenuItemsLeft('index',menuItems);
            });
            </script>
            
            <?php
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<ul class="navbar-nav navbarPhP">';
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <div class="menuRight"></div>
            </div>
      </nav>
      ';
            }
            else{
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <div class="menuRight"></div>
            </div>
      </nav>
      ';
            }
					  
                    ?>
                    
                    <?php
                }
          // Handle specific GET requests
          
                ?>
                <!-- </ul -->
        </nav>
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
  <strong>Kedves <?php echo $_SESSION['firstName'] ?>!</strong> Az oldal <u>folyamatos fejlesztés</u> alatt áll. Ha hibát szeretnél bejelenteni/észrevételed van, írj az arpadmedia.io@gmail.com címre, vagy <a href="mailto:arpadmedia.io@gmail.com?Subject=MediaIO%20Hibabejelent%C3%A9s" target="_top">írj most egy e-mailt!</a>
</div>
              <h1 align=center class="rainbow">Árpád Média IO </h1>
		                <h4 align=center><?php echo $application_version_text.$application_Version; ?>'</h4>
                    <div class="row justify-content-center mainRow1" style="text-align: center; width:100%; margin: 0 auto;"></div><br>
                    <div class="row justify-content-center mainRow2" style="text-align: center; width:100%; margin: 0 auto;"></div><br>
                    <div class="row justify-content-center mainRow3" style="text-align: center; width:100%; margin: 0 auto;"></div><br>
                    <div class="row justify-content-center mainRow4" style="text-align: center; width:100%; margin: 0 auto;"></div><br>
              <footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p><?php echo $applicationTitleFull; ?> <strong>ver. <?php echo $application_Version; ?></strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a>
            Socket kommunikáció állapota: <span id='webSocketState' style="width: 10px; height:10px; display: inline-block;"></span>
            </p></div></footer>';
            <script type = "text/javascript">
            $( document ).ready(function() {             
              //WebSocketTest();
              drawMenuItemsRight('index',menuItems);
              drawIndexTable(menuItems);
            });
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
<script type="text/javascript">
/*window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};*/
</script>