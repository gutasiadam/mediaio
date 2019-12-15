<?php 
session_start();

if($_SESSION['role']=="Default") {
    header("Location: ../index.php?notboss");
}
if(isset($_SESSION['userId']) && (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss"))){
    error_reporting(E_ALL ^ E_NOTICE);

//index.php

$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="loginsystem";

?>

<html>  
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="utility/timeline.min.js"></script>
        <link rel="stylesheet" href="utility/pathfinder.css" />
        <link rel="stylesheet" href="utility/timeline.min.css" />
        <script src="utility/jquery.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <title>PathFinder/AuthCodeGen</title>
  
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
   <h1 align="center">Ponttábla</h1><br>
			
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">
    <?php 
        $TKI = $_SESSION['UserUserName'];    
        $conn = new mysqli($serverName, $userName, $password, $dbName);
        $sql = ("SELECT * FROM `users` ORDER BY `UserPoints` DESC");
        $result = $result = mysqli_query($conn, $sql);
        $conn->close();
        $imodal=0;
        $resultArray = [];

          while($row = $result->fetch_assoc()) { 
              array_push($resultArray, $row);

                $conn = new mysqli($serverName, $userName, $password, $dbName);
                $rowItem = $row["firstName"].$row["lastName"];
                /*$query = ("SELECT * FROM `users` ORDER BY `users`.`UserPoints` DESC");
                $result2 = mysqli_query($conn, $query);
                $conn->close();
                while($codeRow = $result2->fetch_assoc()) {$dbCode = $codeRow["Code"]; $dbUser = $codeRow["AuthUser"];}*/
                
                echo '
                <div class="row">
                <div class="col-4">
                 <h2>'. $row["firstName"].'</h2>
                 <p>'. $row["lastName"].'</p>
                </div>
                <div class="col-4 border-left border-right">';

                if($row["UserPoints"] == 0){
                    echo '<h2 class="text text-warning">'.$row["UserPoints"].'</h2>';
                }
                else if($row["UserPoints"] < 0){
                    echo '<h2 class="text text-danger">'.$row["UserPoints"].'</h2>';
                }
                else{
                    echo '<h2>'.$row["UserPoints"].'</h2>';
                }
                echo '<p>Pont</p>
                </div>';
                echo '<div class="col-1"><button class="btn btn-dark " id="auth'.$imodal.'" data-toggle="modal" data-target="#a'.$imodal.'">Módosítás</button></div>
               </div>';
  
              echo              
              '<div class="modal fade" id="a'.$imodal.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pontszámok módosítása '.$row["usernameUsers"].' számára</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="./points.php" class="form-group" method=post>
                <h4>Pontszám</h4> 
                <input type="number" step="0.01" class="form-control" name="points" value=""/> 
                <input type="hidden" class="form-control" name="user" value="'.$row["usernameUsers"].'"/>
                <input type="hidden" class="form-control" name="currentpoints" value="'.$row["UserPoints"].'"/> 
                <h6 id="emailHelp" class="form-text text-muted"> <strong>Negatív és nem egész számot is beírhatsz</strong> ( pl.: -3.14 )<br>A gomb lenyomása után töltsd újra az oldalt, hogy a ponttábla frissüljön!</h6>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
                    <button type="submit" class="btn btn-success authToggle">Módosítás</button>
                    </form>
              </div>
                    </div>      
                </div>
              </div>';$imodal++;}
            }
            
    $connect = null;
    session_start();
      if (isset($_POST["points"])){
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "loginsystem";
        
        $score = $_POST["points"];
        $score_User = $_POST['user'];
        $def_points= $_POST["currentpoints"];
        $newScore=floatval($score+$def_points);
            $conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase);
              if ($conn->connect_error){
                die("Connection failed: ".mysqli_connect_error());}
        $SQL = ("UPDATE `users` SET `UserPoints` = '$newScore' WHERE `users`.`userNameUsers` = '$score_User'");
        $WriteResult = $conn->query($SQL);
        /*if ($WriteResult==TRUE){
            echo "SIKER";
        }
        else{
            echo "OOF";
        }*/
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