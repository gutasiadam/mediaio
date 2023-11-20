<?php

include "translation.php";
include "header.php";
//Suppresses error messages
error_reporting(E_ERROR | E_PARSE);

?>
<!DOCTYPE html>
<link rel="stylesheet" href="../style/common.css">
<?php if (isset($_SESSION["userId"])) { ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
      <img src="./utility/logo2.png" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
        <script>
          $(document).ready(function () {
            menuItems = importItem("./utility/menuitems.json");
            drawMenuItemsLeft('index', menuItems);
          });
        </script>
      </ul>
      <ul class="navbar-nav navbarPhP">
        <li>
          <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span>
            <?php echo ' ' . $_SESSION['UserUserName']; ?>
          </a>
        </li>
      </ul>
      <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
        <button class="btn btn-danger my-2 my-sm-0" id="logoutBtn" name='logout-submit'
          type="submit">Kijelentkezés</button>
        <script type="text/javascript">
          window.onload = function () {
            display = document.querySelector('#time');
            var timeUpLoc = "utility/userLogging.php?logout-submit=y"
            startTimer(display, timeUpLoc);
          };
        </script>
      </form>
      <a class="nav-link my-2 my-sm-0" href="./help.php">
        <i class="fas fa-question-circle fa-lg"></i>
      </a>
    </div>
  </nav>
<?php } ?>

<body id="index">
  <?php
  if (!isset($_SESSION["userId"])) { ?>

    <form class="login" action="utility/userLogging.php" method="post" autocomplete="off">
      <fieldset>
        <legend id="zsoka" class="legend text"> MediaIO </legend>
        <div class="input">
          <input type="text" name="useremail" placeholder="Felhasználónév/E-mail" required />
          <span>
            <i class="fa fa-envelope-o"></i>
          </span>
        </div>
        <div class="input">
          <input type="password" name="pwd" placeholder="Jelszó" required />
          <span>
            <i class="fa fa-lock"></i>
          </span>
        </div>
        <button class="btn btn-dark" type="submit" name="login-submit">
          <i class="fa fa-long-arrow-right"></i>
        </button>
      </fieldset>
      <div class="feedback"> átirányítás.. <br />
      </div>
      <div>

      </div>

    </form>
    <h6 align=center id="SystemMsg" class="successtable2" style="display:none;"></h6>
    <footer class="page-footer font-small blue">
      <div class="fixed-bottom" align="center">
        <h3 id="errorbox"></h3>
        <?php
        //If a motd exists, print it
        if (file_exists("./data/loginPageSettings.json")) {
          $file = fopen("./data/loginPageSettings.json", "r");
          $message = fread($file, filesize("./data/loginPageSettings.json"));
          $message = json_decode($message, true);
          echo "<h6 class='text text-success'><p style='color:" . $message["color"] . "'>" . $message["message"] . "</p></h6>";
          fclose($file);
        }
        ?>
        <a href="./profile/lostPwd.php">
          <h6>Elfelejtett jelszó?</h6>
        </a>
        <p class="Footer">
          <!-- <h6 class="text success text-success">🔧 Az e-mailek küldése ismét üzemel. </h6> -->
          Code by <a href="https://github.com/gutasiadam">Adam Gutasi</a>
        </p>
      </div>
    </footer>
    </div>
    <script>
      $(".input").focusin(function () {
        $(this).find("span").animate({
          "opacity": "0"
        }, 200);
      });
      $(".input").focusout(function () {
        $(this).find("span").animate({
          "opacity": "1"
        }, 300);
      });
      $(".login").submit(function () {
        $(this).find(".submit i").removeAttr('class').addClass("fa fa-check").css({
          "color": "#fff"
        });
        $(".submit").css({
          "background": "#2ecc71",
          "border-color": "#2ecc71"
        });
        $(".feedback").show().animate({
          "opacity": "1",
          "bottom": "-80px"
        }, 400);
        $("input").css({
          "border-color": "#2ecc71"
        });
        $(".login").submit();
      });
    </script>
  <?php } else { ?>
    <div class="alert alert-warning alert-dismissible fade show" id="note" role="alert"><button type="button"
        class="close" data-dismiss="alert" aria-label="Close"><span
          aria-hidden="true">&times;</span></button><strong>Kedves
        <?php if (isset($_SESSION["firstName"])) {
          echo $_SESSION["firstName"];
        } ?>!
      </strong> Az oldal <u>folyamatos fejlesztés</u> alatt áll. Ha hibát szeretnél bejelenteni/észrevételed van, írj az
      arpadmedia.io@gmail.com címre, vagy <a href="mailto:arpadmedia.io@gmail.com?Subject=MediaIO%20Hibabejelent%C3%A9s"
        target="_top">írj most egy e-mailt!</a></div>
    <h1 align=center class="rainbow">Árpád Média IO</h1>
    <div class="row justify-content-center mainRow1"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
    <div class="row justify-content-center mainRow2"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
    <div class="row justify-content-center mainRow3"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
    <div class="row justify-content-center mainRow4"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div>
    <br>
    <script type="text/javascript">
      $(document).ready(function () {
        drawMenuItemsRight('index', menuItems);
        drawIndexTable(menuItems, 0);
      });


    </script>
  <?php }
  //GET változók kezelése
  
  if ($_GET["signup"] == "success") {
    echo '
<script>document.getElementById("errorbox").innerHTML="Sikeres regisztráció!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
  }
  if ($_GET["logout"] == "success") {
    echo '
<script>document.getElementById("errorbox").innerHTML="Sikeres kijelentkezés!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
  } // ÁTMÁSOLNI
  if ($_GET["logout"] == "pwChange") {
    echo '
<script>document.getElementById("errorbox").innerHTML="Sikeres jelszócsere!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
  }
  if ($_GET["error"] == "WrongPass") {
    echo '
<script>document.getElementById("errorbox").innerHTML="Helytelen jelszó!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
  }
  if ($_GET["error"] == "NoUser") {
    echo '
<script>document.getElementById("errorbox").innerHTML="Hibás felhasználónév / jelszó!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
  }
  if ($_GET["error"] == "AccessViolation") {
    echo '
<script>document.getElementById("errorbox").innerHTML="Ehhez a funkcióhoz be kell jelentkezned!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
  }
  if ($_GET["error"] == "loginLimit") {
    echo '
<script>document.getElementById("errorbox").innerHTML="A belépések átmenetileg korlátozva vannak. Próbáld újra később.";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut();
              window.location.href = "index.php"; }, 6000);
              </script>';
  }
  ?>
</body>