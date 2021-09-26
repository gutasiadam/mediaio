
<html>
    <?php
    header('Pragma: public'); 
    header("Cache-Control: max-age=2592000");
    header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
    
    require("header.php");
    require("../translation.php");?>
    <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>

<script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = $application_DATABASE_PASS;
    $dbname = "mediaio";
    $conn = new mysqli($servername, $username, $password, $dbname);
    $uName = $_SESSION['UserUserName'];
    $sql = "SELECT idUsers FROM users WHERE usernameUsers = '$uName' AND GAUTH_SECRET IS NOT NULL";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        $userGAUTHSTATE = 1;
      }
  } else {
    $userGAUTHSTATE = 0;}

        session_start();
        if(isset($_SESSION['userId'])){
            echo '
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
          
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>';
            if ($_SESSION['role']>=3){
              echo '<li><a class="nav-link disabled" href="#">Admin jogok</a></li>';}
            echo '</ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
                      <div class="menuRight"></div>
					</div>
    </nav>

                    <body>
                    <h1 align=center class="rainbow">Opciók</h1>
                    <table class="logintable">
                    <tr><td><form action="pfcurr.php"><button class="btn btn-dark">Mutasd a nálam levő tárgyakat <i class="fas fa-project-diagram"></i></button></form></td></tr>
                    <tr><td><form action="chpwd.php"><button class="btn btn-warning">Jelszócsere <i class="fas fa-key"></i></button></form></td></tr>
                    <tr><td><form action="userlist.php"><button class="btn btn-dark">Felhasználók eléhetőségeinek megtekintése <i class="fas fa-address-book"></i></i></button></form></td></tr>
                    <tr><td><form action="rules.php"><button class="btn btn-secondary">Dokumentumok <i class="fas fa-folder-open"></i></i></button></form></td></tr>
                    ';
          if ($_SESSION['role']>=3){
            echo '
                    <table class="logintable">
                    <tr><td><form action="points.php"><button class="btn btn-success">Pontszámok <i class="fas fa-calculator"></i></button></form></td></tr>
                    <tr><td><form action="../budget/"><button class="btn btn-info">Költségvetés <i class="fas fa-coins"></i></button>
                    </form> </td></tr>';
                    
                    
                    echo '<tr><td><form action="roles.php"><button class="btn btn-danger">Felhasználói engedélyek módosítása <i class="fas fa-radiation"></i></i></button></form></td></tr>
                    <tr><td><form action="stats.php"><button class="btn btn-dark">Áttekintés <i class="fas fa-chart-pie"></i></i></button></form></td></tr>';
                    if($_SESSION['role']=="Boss"){
                      echo '<tr><td><form action="../utility/refetchData.php"><button class="btn btn-success">Adattáblák frissítése <i class="fas fa-sync"></i></i></button></form></td></tr>';
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
<script>
window.onload = function () {
    var fiveMinutes = 10 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    (function(){
  setInterval(updateTime, 1000);
});
    updateTime();
};
</script>