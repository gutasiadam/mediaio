
<html>
    <?php 
    require("../header.php");
    require("../translation.php");
    $servername = "localhost";
    $username = "root";
    $password = $application_DATABASE_PASS;
    $dbname = "loginsystem";
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
            <a class="navbar-brand" href="index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item ">
						    <a class="nav-link" href="../index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						    <a class="nav-link" href="../takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item ">
						    <a class="nav-link" href="../retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						    <a class="nav-link" href="../adatok.php"><i class="fas fa-database fa-lg"></i></a>
						</li>
            <li class="nav-item">
                        <a class="nav-link" href="../pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="../events/"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item active">
                        <a class="nav-link" href="#"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>';
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}
            echo '</ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
                      <a class="nav-link my-2 my-sm-0" href="../help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
    </nav>
    <footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p>'.$applicationTitleFull.' <strong>ver. '.$application_Version.'</strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a></p></div></footer>

                    <body>
                    <h1 align=center>Opciók</h1>
                    <table class="logintable">
                    <tr><td><form action="pfcurr.php"><button class="btn btn-dark">Mutasd a nálam levő tárgyakat <i class="fas fa-project-diagram"></i></button></form></td></tr>
                    <tr><td><form action="chpwd.php"><button class="btn btn-warning">Jelszócsere <i class="fas fa-key"></i></button></form></td></tr>
                    <tr><td><form action="userlist.php"><button class="btn btn-dark">Felhasználók eléhetőségeinek megtekintése <i class="fas fa-address-book"></i></i></button></form></td></tr>
                    ';
          if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
            echo '
                    <table class="logintable">
                    <tr><td><form action="points.php"><button class="btn btn-success">Pontszámok <i class="fas fa-calculator"></i></button></form></td></tr>
                    <tr><td><form action="../budget/"><button class="btn btn-info">Költségvetés <i class="fas fa-coins"></i></button>
                    </form> </td></tr>';
                    if($_SESSION['role']=="Boss"){
                      echo '<tr><td><form action="../utility/refetchData.php"><button class="btn btn-success">Adattáblák frissítése <i class="fas fa-sync"></i></i></button></form></td></tr>';
                    }
                    
                    echo '<tr><td><form action="roles.php"><button class="btn btn-danger">Felhasználói engedélyek módosítása <i class="fas fa-radiation"></i></i></button></form></td></tr>
                    <tr><td><form action="stats.php"><button class="btn btn-dark">Áttekintés <i class="fas fa-chart-pie"></i></i></button></form></td></tr>
                    
                    </table>';
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
            window.location.href = "../utility/logout.ut.php"
        }
    }, 1000);
}

window.onload = function () {
    var fiveMinutes = 10 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};
</script>