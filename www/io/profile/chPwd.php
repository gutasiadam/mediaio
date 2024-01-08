<?php
session_start();
include "header.php";
$username = $_SESSION['userId'];
if (isset($_SESSION['userId'])) { ?>
  <html>
  <?php if (isset($_SESSION["userId"])) { ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php">
        <img src="../utility/logo2.png" height="50">
      </a>
      <!-- Breadcrumb for mobilne navigation -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto navbarUl">
          <script>
            $(document).ready(function () {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft('profile', menuItems, 2);
            });
          </script>
        </ul>
        <ul class="navbar-nav ms-auto navbarPhP">
          <li>
            <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span>
              <?php echo ' ' . $_SESSION['UserUserName']; ?>
            </a>
          </li>
        </ul>
        <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
          <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
          <script type="text/javascript">
            window.onload = function () {
              display = document.querySelector('#time');
              var timeUpLoc = "../utility/userLogging.php?logout-submit=y"
              startTimer(display, timeUpLoc);
            };
          </script>
        </form>
      </div>
    </nav>
  <?php } ?>
  <?php

  echo '<table class="logintable" id="chpass"><tr><td><p>Jelszócsere <br><h3 class="rainbow">' . $_SESSION['lastName'] . ' ' . $_SESSION['firstName'] . '</h3><br>Számára</br>Sikeres jelszócsere esetén az oldal kijelentkeztet, és e-mailt is küld.</p></td></tr>
            <form action="../Core.php" method="post">
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-Old" placeholder="Jelenlegi jelszó"></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-New" placeholder="Új jelszó" ></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-New-Check" placeholder="Új jelszó még egyszer"></td></tr> <br>
            <tr><td><br><button class="btn btn-success" id="submitPwdCh"align=center type="submit" name="pwdCh-submit">Mehet</button></td></tr>
            <tr><td><div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
            </div></tr></td>
            
            </form>
            ';
  if (isset($_GET['error'])) {
    if ($_GET['error'] == 'emptyField') {
      echo '<tr><td><h5 class="registererror text-danger">Kérlek MINDEN mezőt tölts ki!</h5></td></tr>';
    } else if ($_GET['error'] == 'PasswordCheck') {
      echo '<tr><td><h5 class="registererror text-danger">A megadott jelszavak nem egyeznek, vagy túl rövid jelszót adtál meg!</h5></td></tr>';
    } else if ($_GET['error'] == 'PasswordLenght') {
      echo '<tr><td><h5 class="registererror text-danger">Az új jelszónak legalább 8 karakter hosszúnak kell lennie!</h5></td></tr>';
    } else if ($_GET['error'] == 'OldPwdError') {
      echo '<tr><td><h5 class="registererror text-danger">Hibásan adtad meg a jelenlegi jelszavadat!</h5></td></tr>';
    } else if ($_GET['error'] == 'none') {
      echo '<tr><td><p class="success">Successfully changed password! Please log out in order to use your brand new, shiny password! </p></td></tr>';
      session_unset();
      session_destroy();
      header("Location: ../utility/userLogging.php?logout-submit=1");
    }
  }
  echo "</table>";
} else {
  // header("Location: ../index.php?XD");
  exit();
}
?>

</html>
<script>
  $("#submitPwdCh").click(function () {
    $(".spinner-border").fadeIn();
  });

  $(document).ready(function () {
    $(".spinner-border").hide();
  });
</script>