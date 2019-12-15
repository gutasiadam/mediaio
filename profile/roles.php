<?php 
session_start();
if(($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
    error_reporting(E_ALL ^ E_NOTICE);

$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="loginsystem";

?>

<html>  
    <head>
        <script src="utility/timeline.min.js"></script>
        <link rel="stylesheet" href="utility/pathfinder.css" />
        <link rel="stylesheet" href="utility/timeline.min.css" />
        <script src="utility/jquery.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <title>Felhasználói jogok</title>
    </head>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="../index.php">Arpad Media IO</a>
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
						<li class="nav-item">
						  <a class="nav-link" href="../retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						  <a class="nav-link" href="../adatok.php"><i class="fas fa-database fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        	<a class="nav-link" href="../pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            			</li>
            <li class="nav-item">
                        <a class="nav-link active" href="../profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
						<li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form></form>
              <a class="nav-link  my-2 my-sm-0" href="../help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
</nav>
    <body>  
        <div class="container">
   <br />
   <h1 align="center">Felhasználói jogok</h1><br>
			
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">
    <?php 
        $TKI = $_SESSION['UserUserName'];    
        $conn = new mysqli($serverName, $userName, $password, $dbName);
        $sql = ("SELECT * FROM `users`");
        $result = $result = mysqli_query($conn, $sql);
        $conn->close();
        $imodal=0;
        $resultArray = [];

          while($row = $result->fetch_assoc()) { 
              array_push($resultArray, $row);

                $conn = new mysqli($serverName, $userName, $password, $dbName);
                $rowItem = $row["firstName"].$row["lastName"];
                $query = ("SELECT * FROM `users`");
                $result2 = mysqli_query($conn, $query);
                $conn->close();
                while($codeRow = $result2->fetch_assoc()) {$dbCode = $codeRow["Code"]; $dbUser = $codeRow["AuthUser"];}
                
                echo '
                <div class="row">
                <div class="col-4">
                 <h2>'.$row["lastName"]." ".$row["firstName"].'</h2>
                 <p>'.$row["usernameUsers"].'</p>
                </div>
                <div class="col-2">';
                if($row["usernameUsers"]==$_SESSION['UserUserName']){
                    echo '<h2 class="text text-success">'.$_SESSION['role'].'</h2><p>jogok</p></div></div>';
                }else{
                  if($row["Userrole"] == "Boss"){echo '<h2 class="text text-danger">Boss</h2><p>jogok</p></div></div>';}
                if($row["Userrole"] == "Admin"){
                    echo '<h2 class="text text-info">Admin</h2><p>jogok</p>
                    </div>';
                    echo '<div class="col-1"><button class="btn btn-danger " id="auth'.$imodal.'" data-toggle="modal" data-target="#a'.$imodal.'">Lerontás</button></div>
               </div>';
  
              echo              
              '<div class="modal fade" id="a'.$imodal.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">'.$row["usernameUsers"].' admin jogának megvonása</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="./roles.php" class="form-group" method=post>
                <input type="hidden" class="form-control" name="user" value="'.$row["usernameUsers"].'"/>
                <input type="hidden" class="form-control" name="mode" value="revert"/> 
                <h6 id="emailHelp" class="form-text text-muted"> A módosítás gomb megynomásával megvonod '.$row["usernameUsers"].' felhasználót admin jogaitól.<br>A gomb lenyomása után töltsd újra az oldalt, hogy a tábla frissüljön!</h6>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
                    <button type="submit" class="btn btn-danger authToggle">Módosítás</button>
                    </form>
              </div>
                    </div>      
                </div>
              </div>';
                }
                if($row["Userrole"] == "Default"){
                    echo '<h2>Default</h2><p>jogok</p>
                    </div>';
                    echo '<div class="col-1"><button class="btn btn-success " id="auth'.$imodal.'" data-toggle="modal" data-target="#a'.$imodal.'">Felemelés</button></div>
               </div>';
  
              echo              
              '<div class="modal fade" id="a'.$imodal.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Admin jogok megadása '.$row["usernameUsers"].' számára</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="./roles.php" class="form-group" method=post>
                <input type="hidden" class="form-control" name="user" value="'.$row["usernameUsers"].'"/>
                <input type="hidden" class="form-control" name="mode" value="grant"/> 
                <h6 id="emailHelp" class="form-text text-muted">A módosítás gomb megynomásával felruházod '.$row["usernameUsers"].' felhasználót admin jogokkal.<br>A gomb lenyomása után töltsd újra az oldalt, hogy a tábla frissüljön!</h6>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
                    <button type="submit" class="btn btn-danger authToggle">Módosítás</button>
                    </form>
              </div>
                    </div>      
                </div>
              </div>';
                }}
                $imodal++;}
            }
            
    $connect = null;
    //session_start();
      if (isset($_POST["mode"])){
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "loginsystem";
        $targetUser = $_POST["user"];
        $conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase);
              if ($conn->connect_error){
                die("Connection failed: ".mysqli_connect_error());}

          if ($_POST["mode"]=="grant"){
            $SQL = ("UPDATE `users` SET `Userrole` = 'Admin' WHERE `users`.`userNameUsers` = '$targetUser'");
          }
          if ($_POST["mode"]=="revert"){
            $SQL = ("UPDATE `users` SET `Userrole` = 'Default' WHERE `users`.`userNameUsers` = '$targetUser'");
          }
        $WriteResult = $conn->query($SQL);
      }
?>
    </body>  
</html>
<style>
.timeline__item{
  background-color: #ededed;
}
</style>

<script>

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
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};

/*$(document).on('click', '.authToggle', function(){
  setTimeout(function(){ window.location.href = "./utility/logout.ut.php"; }, 3000);});*/
</script>