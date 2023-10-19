
<?php 
  include "translation.php";
  require "logincheck.php";
  include "header.php";
  error_reporting(E_ALL ^ E_NOTICE);
?>
<!DOCTYPE html>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<link rel="stylesheet" href="../style/help.css">
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>MediaIO-Help</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
          <script>
        $(document).ready(function() {
          menuItems = importItem("./utility/menuitems.json");
          drawMenuItemsLeft('', menuItems);
        });
      </script>
</head>
<body>
    <header>
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="./utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
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
</nav>
                    <?php
                }else{
                    echo '';
                }
          // Handle specific GET requests
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
              echo '<div class="row justify-content-center" style="text-align: center; width:80%; margin: 0 auto;">
              <div class="col-6 col-sm-2 kivetel" id="kivetel"><a class="nav-link ab" href="#"><i class="fas fa-upload fa-3x"></i><br><h5>'.$index_takeOut.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1 kivetel" id="visszahozas"><a class="nav-link ab" href="#"><i class="fas fa-download fa-3x"></i><br><h5>'.$index_Retrieve.'</h5></a></div>
              </div>
              <!-- Force next columns to break to new line at md breakpoint and up -->

              <br>
              <div class="row justify-content-center" style="text-align: center; width:80%; margin: 0 auto;">
              <div class="col-6 col-sm-2 kivetel" id="adatok"><a class="nav-link ab" href="#"><i class="fas fa-database fa-3x"></i><br><h5>'.$index_Data.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1 kivetel" id="pathfinder"><a class="nav-link ab" href="#"><i class="fas fa-project-diagram fa-3x"></i><br><h5>'.$index_PathFinder.'</h5></a></div>
              </div>
              <br>
              <div class="row justify-content-center" style="text-align: center; width:80%; margin: 0 auto;">
              <div class="col-6 col-sm-2 kivetel" id="profil"><a class="nav-link ab" href="#"><i class="fas fa-user-alt fa-3x"></i> <br><h5>'.$index_Profile.'</h5></a></div>
              <div class="col-6 col-sm-2 offset-md-1 kivetel" id="segitseg"><a class="nav-link ab " href="#"><i class="fas fa-exclamation-circle fa-3x"></i><i class="fas fa-bug fa-3x"></i><br><h6>'.$index_Help_Further.'</h6></a></div>
            </div>
            
            ';
            }?>
    <div id="szoveg-kivetel" class="szoveg"><p><h2><?php echo $index_takeOut; ?></h2><br><h4>A kivétel oldalon a leltárban szereplő tárgyakat tudod kivenni. A folyamat a következő:</h4>
    <ol><li>A beviteli mezőbe kezdd el írni annak a tárgynak a nevét, amit ki szeretnél venni. Ahogy elkezded írni a tárgy nevét, felbukkannak a kereső által ajánlott tárgyak.<br><strong>Vedd, figyelembe, hogy a beviteli mező nem enged semmi olyat beírni, ami nem szerepel a leltárban.</strong></li>
    <li>Az "Hozzáad"gomb, vagy az ENTER gomb lenyomásával add hozzá a tárgyat a kivenni kívánt tárgyak listájához. <ol><li><h6 class="text text-muted">Ha már valaki kivette az adott tárgyat, azt az oldal egy hibaüzenettel jelzi majd feléd.</li><h6><li>Bármikor visszavonhatod az adott tárgy kivételét, szimplán kattints a mellette található X-re.</li></ol></li>
    <li>Ha minden tárgyat kiválasztottál, amit szerettél volna, nyomd meg a GO! -gombot.</li>
    <li>Ha minden rendben történt, az oldalon egy zöld- , a sikeres kivételt visszaigazoló jelzést fogsz látni. Kész is vagy!</li>
    </ol>
    </p></div>

    <div id="szoveg-visszahozas" class="szoveg"><p><h2><?php echo $index_Retrieve; ?></h2><br><h4>A visszahozás oldalon a nálad levő tárgyakat tudod visszahozottnak beállítani. A folyamat a következő:</h4>
    <ol><li>A beviteli mezőbe kezdd el írni annak a tárgynak a nevét, amit vissza szeretnél hozni. Ahogy elkezded írni a tárgy nevét, felbukkannak a kereső által ajánlott tárgyak.
    <br>Vedd, figyelembe, hogy a beviteli mező nem enged semmi olyat beírni, ami nem szerepel a leltárban.</li>
<li>Az "Hozzáad"gomb, vagy az ENTER gomb lenyomásával add hozzá a tárgyat az elvinni kívánt tárgyak listájához.</li>
<ol><li>Ha már valaki visszahozta az adott tárgyat, azt az oldal egy hibaüzenettel jelzi majd feléd.</li>
<li>Bármikor visszavonhatod az adott tárgy visszahozatalát, szimplán kattints a mellette található piros X-re.</li></ol>
<li>Ha minden tárgyat kiválasztottál, amit szerettél volna, nyomd meg a GO! -gombot.</li>
<li>Amennyiben olyan tárgyakat is vissza akarsz hozni amelyek nem általad lettek kivéve akkor oda egyesével kell beírnod a kódokat majd a mellette található plusz gombra nyomni.</li>
<li>Ha minden rendben történt, az oldalon egy zöld- , a sikeres kivételt visszaigazoló jelzést fogsz látni. Kész is vagy!</li>
    </ol>
    </p></div>

    <div id="szoveg-adatok" class="szoveg"><p><h2><?php echo $index_Data; ?></h2><br><h4>AItt az összes tárgy megtalálható ami az ÁMÖK leltárjába föl van véve. </p></h4>
    <ul><li>UID: A tárgyak leltári neve.</li>
    <li>NAME: A tárgyak neve részletesen leírva.</li>
    <li>TYPE: A kategória amelyben az adott tárgy van.</li>
    <li>OUT BY: Az a felhasználó aki épp kivette a tárgyat.</li>
    </ul>
    </p></div>

    <div id="szoveg-pathfinder" class="szoveg"><p><h2><?php echo $index_PathFinder  ; ?></h2><br>A keresőbe elkezded gépelni a keresett tárgy nevét. <br>Amikor megtaláltad kiválasztod és rányomsz az keress gombra, ekkor a rendszer kiírja neked a keresett tárgy útját, ki mikor vitte el/ hozta vissza, illetve külön van jelölve ha kóddal került vissza a leltárba.<br> Ha másik tárgyat szeretnél megnézni, kezdd újra a folyamatot.
    </p></div>

    <div id="szoveg-profil" class="szoveg"><p><h2><?php echo $index_Profile; ?></h2><br><ul><li>Első gomb: Itt meg tudod tekinteni az éppen nálad levő dolgokat.Kódot is itt tudsz generálni amivel más tudja visszahozni a cuccaid.

    </li><li>Második gomb: A jelszavad tudod megváltoztatni.</li><li>Harmadik gomb: A többiek elérhetősége itt található meg.</ul>
    </p></div>

    <div id="szoveg-segitseg" class="szoveg"><p><h2><?php echo $index_Help_Further; ?></h2><br><h4 class="text text-danger">Ha a weboldalon hibát észleltél, vagy további kérdésed van, keresed minket Facebookon, az arpadmedia.io@gmail.com e-mail címen, vagy <a href="mailto:arpadmedia.io@gmail.com?Subject=MediaIO%20Hibabejelent%C3%A9s" target="_top">írj most egy e-mailt!</a></h4>
    </p></div>
      
	</body>
<style>
/* .successtable{
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
 */
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
    var fiveMinutes = 10 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    updateTime();
};
</script>