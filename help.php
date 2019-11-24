<?php 
  include "translation.php";
  require "logincheck.php";
  include "version.php";
  error_reporting(E_ALL ^ E_NOTICE);
?>
<!DOCTYPE html>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO Help</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <header>
                <?php 
                if(isset($_SESSION['userId'])){
                    date_default_timezone_set("Europe/Budapest"); 
                    echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">
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
                        <a class="nav-link" href="./profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
            
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
					</div>
		</nav>
';
                    ?>
                    <?php
                }else{
                    echo '';
                }
          // Handle specific GET requests
          if($_GET['signup'] == "success"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-success"><strong>Hiba - </strong>Sikeres regisztráció! </div></tr></td></table>';
          }
          if($_GET['logout'] == "success"){
            echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-info">Sikeres kijelentkezés! </div></tr></td></table>';
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
		<h1 align=center class="rainbow"><?php echo $help?> </h1>
		<h4 align=center><?php echo $help_description?></h4>
    <?php if(!isset($_SESSION['userId'])){echo '
                    
                    <form action="utility/login.ut.php" method="post" class="formmain" autocomplete="off" >
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="text" name="useremail" placeholder="Username/E-mail" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="password" name="pwd" placeholder="Password" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><button class="btn btn-dark" type="submit" name="login-submit" align=center>Login</button></div></div>
                    </div>
                    </form>';}
            else{
              echo '<div class="row justify-content-center" style="text-align: center;">
              <div class="col-6 col-sm-2 kivetel" id="kivetel"><a class="nav-link ab" href="#"><i class="fas fa-upload fa-3x"></i><br><h5>'.$index_takeOut.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1 kivetel" id="visszahozas"><a class="nav-link ab" href="#"><i class="fas fa-download fa-3x"></i><br><h5>'.$index_Retrieve.'</h5></a></div>
              </div>
              <!-- Force next columns to break to new line at md breakpoint and up -->

              <br>
              <div class="row justify-content-center" style="text-align: center;">
              <div class="col-6 col-sm-2 kivetel" id="adatok"><a class="nav-link ab" href="#"><i class="fas fa-database fa-3x"></i><br><h5>'.$index_Data.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1 kivetel" id="pathfinder"><a class="nav-link ab" href="#"><i class="fas fa-project-diagram fa-3x"></i><br><h5>'.$index_PathFinder.'</h5></a></div>
              </div>
              <br>
              <div class="row justify-content-center" style="text-align: center;">
              <div class="col-6 col-sm-2 kivetel" id="profil"><a class="nav-link ab" href="#"><i class="fas fa-wrench fa-3x"></i><i class="fas fa-user-alt fa-3x"></i> <br><h5>'.$index_Profile.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1 kivetel" id="segitseg"><a class="nav-link ab " href="#"><i class="fas fa-exclamation-circle fa-3x"></i><i class="fas fa-bug fa-3x"></i><br><h6>További segítség</h6></a></div>
            </div>';
            }?>
    <div id="szoveg-kivetel" class="szoveg"><p><h2>Kivétel</h2><br><h4>A kivétel oldalon a leltárban szereplő tárgyakat tudod kivenni. A folyamat a következő:</h4>
    <ol><li>A beviteli mezőbe kezdd el írni annak a tárgynak a nevét, amit ki szeretnél venni. Ahogy elkezded írni a tárgy nevét, felbukkannak a kereső által ajánlott tárgyak.<br><strong>Vedd, figyelembe, hogy a beviteli mező nem enged semmi olyat beírni, ami nem szerepel a leltárban.</strong></li>
    <li>Az "Add"gomb, vagy az ENTER gomb lenyomásával add hozzá a tárgyat a kivenni kívánt tárgyak listájához. <ol><li><h6 class="text text-muted">Ha már valaki kivete az adott tárgyat, azt az oldal egy hibaüzenettel jelzi majd feléd.</li><h6><li>Bármikor visszavonhatod az adott tárgy kivételét, szimplán kattints a mellete található X-re.</li></ol></li>
    <li>Ha minden tárgyat kiválasztottál, amit szerettél volna, nyomd meg a GO! -gombot.</li>
    <li>Ha minden rendben történt, az oldalon egy zöld- , a sikeres kivételt visszaigazoló jelzést fogsz látni. Kész is vagy!</li>
    </ol>
    </p></div>

    <div id="szoveg-visszahozas" class="szoveg"><p><h2>Visszahozás</h2><br><h4>A visszahozás oldalon a nálad levő tárgyakat tudod visszahozottnak beállítani. A folyamat a következő:</h4>
    <ol><li>Szöveg</li>
    </ol>
    </p></div>

    <div id="szoveg-adatok" class="szoveg"><p><h2>Adatok</h2><br><h4>AItt az összes tárgy megtalálható ami az ÁMÖK leltárjába föl van véve. </p></h4>
    <ul><li>UID: A tárgyakleltári neve.</li>
    <li>NAME: A tárgyak neve részletesen leírva.</li>
    <li>TYPE: A kategória amelyben az adott tárgy van.</li>
    <li>OUT BY: Az a felhasználó aki épp kivette a tárgyat.</li>
    </ul>
    </p></div>

    <div id="szoveg-pathfinder" class="szoveg"><p><h2>PathFinder</h2><br><h4>A PathFinder segítségével meg tudod nézni, mikor kinél volt egy adott tárgy.</h4>
    <ol><li>Szöveg</li>
    </ol>
    </p></div>

    <div id="szoveg-profil" class="szoveg"><p><h2>Profil</h2><br><h4>A Profil oldalon a felhasználói fiókoddal kapcsolatos beállításokat tudod elvégezni, illetve kódot tudsz generálni a tárgyaidhoz.</h4>
    <ol><li>Szöveg</li>
    </ol>
    </p></div>

    <div id="szoveg-segitseg" class="szoveg"><p><h2>További segítség</h2><br><h4 class="text text-danger">Ha a weboldalon hibát észleltél, vagy további kérdésed van, keresed <strong>Gutási Ádám</strong>-ot személyesen, <br/>Facebookon, a gutasiadm@gmail.com e-mail címen, vagy <a href="mailto:gutasiadm@gmail.com?Subject=MediaIO%20Hibabejelent%C3%A9s" target="_top">írj most egy e-mailt!</a></h4>
    </p></div>
      
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

.rainbow {
  -webkit-animation: color 15s linear infinite;
  animation: color 15s linear infinite;  
}

.szoveg{
    width:90%;
    margin:auto;
}
@-webkit-keyframes color {
  0% { color: #000000; }
  20% { color: #c91d2b; } 
  40% { color: #ba833e; }
  60% { color: #0f6344; }
  80% { color: #09457a; }
  100% { color: #5f0976; }
}

@keyframes background {
  0% { color: #000000; }
  20% { color: #c91d2b; } 
  40% { color: #ba833e; }
  60% { color: #0f6344; }
  80% { color: #09457a; }
  100% { color: #5f0976; }
}

</style>

<script type="text/javascript">
$(".szoveg").hide();
$(document).ready(function(){
    

    $(".kivetel").one(function(){
    var szovegNev = $(this).attr("id");
    console.log(szovegNev)
    var showHelp = ('#szoveg'+szovegNev);
    $('#szoveg-'+szovegNev).show().animate({'opacity': 0}, 0);
    $('#szoveg-'+szovegNev).show().animate({'opacity': 1}, 300);
  });

  $(".kivetel").click(function(){
    var szovegNev = $(this).attr("id");
    console.log(szovegNev)
    $(".szoveg").hide().animate({'opacity': 0}, 300);
    var showHelp = ('#szoveg'+szovegNev);
    $('#szoveg-'+szovegNev).show().animate({'opacity': 0}, 100);
    $('#szoveg-'+szovegNev).show().animate({'opacity': 1}, 300);
  });
});

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

        if (--timer < 0) {
            timer = duration;
            window.location.href = "./utility/logout.ut.php"
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