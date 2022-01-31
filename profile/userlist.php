<?php 
include "header.php";
session_start();
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}?>

<head>
<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
</head>
<title>Elérhetőségek</title>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      
            <a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
          
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>
            <?php if ($_SESSION['role']>=3){ ?>
              <li><a class="nav-link disabled" href="#">Admin jogok</a></li> <?php  }?>
            </ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
                      <div class="menuRight"></div>
					</div>
          <script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    </nav>


<?php 
$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="mediaio";
	$countOfRec=0;

	$conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);

	if ($conn->connect_error) {
		die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
	}
	$sql = "SELECT usernameUsers, emailUsers, lastName, firstName, teleNum FROM users ORDER BY usernameUsers ASC";
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo "<table width='50' align=center class="."table"."><th>Vezetéknév</th><th>Keresztnév</th><th>Felhasználónév</th><th>e-mail cím</th><th>Telefonszám</th>";
     //output data of each row
    //Displays amount of records found in leltar_master DB
    while($row = $result->fetch_assoc()) {
		echo "<tr><td>".$row["lastName"]."</td><td>".$row["firstName"]."</td><td>".$row["usernameUsers"]. "</td><td><a href=mailto:".$row["emailUsers"]." target=_top>".$row["emailUsers"]."</a></td><td>".$row["teleNum"]."</td><td></tr>";
       
		$countOfRec += 1;
	}
} else {
    echo "0 results";
}
echo "</table>";
$conn->close();?>
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

