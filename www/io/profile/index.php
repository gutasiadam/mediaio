<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<script src="../utility/_initMenu.js" crossorigin="anonymous"></script>

    <html>
<script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    <?php
        
 if (isset($_SESSION["userId"])) { ?> 
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <!-- Breadcrumb for mobilne navigation -->
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
    </ul>
    <ul class="navbar-nav ms-auto navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
  </div>
</nav>

  <body>
    <h1 align=center class="rainbow">Opciók</h1>
    <table class="help-logintable">
      <tr>
        <td>
          <form action="pfcurr.php"><button class="btn btn-dark">Mutasd a nálam levő tárgyakat <i
                class="fas fa-project-diagram"></i></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="chPwd.php"><button class="btn btn-warning">Jelszócsere <i class="fas fa-key"></i></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="userlist.php"><button class="btn btn-dark">Elérhetőségek megtekintése <i
                class="fas fa-address-book"></i></i></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="rules.php"><button class="btn btn-secondary">Dokumentumok <i
                class="fas fa-folder-open"></i></i></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="../utility/damage_report/announce_Damage.php"><button class="btn btn-warning">Sérülés bejelentése
              <i class="fas fa-file-alt"></i></button></form>
        </td>
      </tr>
      <?php
      if (in_array("admin", $_SESSION["groups"])) {
        echo '
                    <tr><td><form action="../utility/damage_report/service.php"><button class="btn btn-warning position-relative">Szervíz <i class="fas fa-wrench"></i></i>  <span id="serviceItemCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0<span class="visually-hidden">unread messages</span></button></form></td></tr>
                    <tr><td><form action="usercheck.php"><button class="btn btn-success position-relative">UserCheck <i class="fas fa-user-check"></i>  <span id="usercheckItemCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0
                    <spanclass="visually-hidden">unread messages</span></button></form></td></tr>
                    <tr><td><form action="stats.php"><button class="btn btn-dark">Áttekintés <i class="fas fa-chart-pie"></i></i></button></form></td></tr>
                    ';
      }
      if (in_array("system", $_SESSION["groups"]) or in_array("teacher", $_SESSION["groups"])) { //SYSADMIN OR TEACHER
        echo '
                    <tr><td><form action="../budget/"><button class="btn btn-info">Költségvetés <i class="fas fa-coins"></i></button></form></td></tr>
					          <tr><td><form action="points.php"><button class="btn btn-success">Pontszámok <i class="fas fa-calculator"></i></button></form> </td></tr>';
                    }
                    if(in_array("system", $_SESSION["groups"])){
                      echo '
                      <tr><td><form action="roles.php"><button class="btn btn-danger">Engedélyek módosítása <i class="fas fa-radiation"></i></button></form></td></tr>
                      <tr><td><form action="../utility/loginPageSettings.php"><button class="btn btn-danger">Motd/Belépések korlátozása <i class="fas fa-user-shield"></i></button></form></td></tr>
                      <tr><td><form action="../utility/refetchData.php"><button class="btn btn-success disabled">Adattáblák frissítése - Frissíts a kivétel oldal betöltésével! <i class="fas fa-sync"></i></i></button></form></td></tr>';
      } ?>
    </table>
    <?php

} else {
  header("Location: ../index.php?error=AccessViolation");
  exit();
}
?>

</html>

<script>
  //Make a Jquery call to the ItemManager.php to get the number of items in the service
  $.ajax({
    url: "../ItemManager.php",
    type: "POST",
    data: {
      mode: "getProfileItemCounts"
    },
    success: function (data) {
      var dataArray = data.split(",");
      //Set the service item count
      document.getElementById("serviceItemCount").innerHTML = dataArray[0];
      //Set the user check item count
      document.getElementById("usercheckItemCount").innerHTML = dataArray[1];
    }
  });
  
</script>