
<html>
    <?php
    header('Pragma: public'); 
    require("header.php");
    require("../translation.php");?>
    <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>

<script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    <?php
        session_start();
 if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
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
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav>

                    <body>
                    <h1 align=center class="rainbow">Opciók</h1>
                    <table class="logintable">
                    <tr><td><form action="pfcurr.php"><button class="btn btn-dark">Mutasd a nálam levő tárgyakat <i class="fas fa-project-diagram"></i></button></form></td></tr>
                    <tr><td><form action="chpwd.php"><button class="btn btn-warning">Jelszócsere <i class="fas fa-key"></i></button></form></td></tr>
                    <tr><td><form action="userlist.php"><button class="btn btn-dark">Elérhetőségek megtekintése <i class="fas fa-address-book"></i></i></button></form></td></tr>
                    <tr><td><form action="rules.php"><button class="btn btn-secondary">Dokumentumok <i class="fas fa-folder-open"></i></i></button></form></td></tr>
          <?php
                    if ($_SESSION['role']>=3){
            echo '
                    <tr><td><form action="usercheck.php"><button class="btn btn-success">UserCheck <i class="fas fa-user-check"></i></button></form></td></tr>
                    <tr><td><form action="../utility/damage_report/announce_Damage.php"><button class="btn btn-warning">Sérülés bejelentése <i class="fas fa-file-alt"></i></button></form></td></tr>
                    <tr><td><form action="../budget/"><button class="btn btn-info">Költségvetés <i class="fas fa-coins"></i></button></form></td></tr>
					<tr><td><form action="points.php"><button class="btn btn-success">Pontszámok <i class="fas fa-calculator"></i></button>
                    </form> </td></tr>';
                    
                    
                    echo '<tr><td><form action="stats.php"><button class="btn btn-dark">Áttekintés <i class="fas fa-chart-pie"></i></i></button></form></td></tr>';
                    if($_SESSION['role']==5){
                      echo '
                      <tr><td><form action="roles.php"><button class="btn btn-danger">Engedélyek módosítása <i class="fas fa-radiation"></i></i></button></form></td></tr>
                      <tr><td><form action="../utility/refetchData.php"><button class="btn btn-success disabled">Adattáblák frissítése - Frissíts a kivétel oldal betöltésével! <i class="fas fa-sync"></i></i></button></form></td></tr>';
                    } ?>
                    </table><?php
          }
        }else{
            header("Location: ../index.php?error=AccessViolation");
            exit();
        }
    ?>
</html>

<style>
.logintable{
  width: 30%;
  text-align: center;
  margin: 0 auto; 
}


#unavailable{
  font-size:18px;
  color: red;
}
</style>