<?php 
session_start();
if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}
?>
<head>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
</head>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
					<a class="navbar-brand" href="index.php">Arpad Media IO</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item ">
						  <a class="nav-link" href="./index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						  <a class="nav-link" href="./takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item">
						  <a class="nav-link" href="./retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item active">
						  <a class="nav-link" href="./adatok.php"><i class="fas fa-database fa-lg"></i></a>
						</li>
						<li class="nav-item">
                        	<a class="nav-link" href="./pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
						</li>
						<li class="nav-item">
                        <a class="nav-link" href="./profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            			</li>
						<li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
					  <a class="nav-link disabled my-2 my-sm-0" href="#"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
</nav>


<?php 
//$allowedIP = array("31.46.204.50", "80.99.70.46", "192.168.0.1", "127.0.0.1", "77.234.86.20");
//if(!in_array($_SERVER['REMOTE_ADDR'], $allowedIP) && !in_array($_SERVER["HTTP_X_FORWARDED_FOR"], $allowedIP)) {
  //header("Location: https://www.google.com"); //redirect
  //exit();
//}

$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="leltar_master";
	$countOfRec=0;

	$conn = new mysqli($serverName, $userName, $password, $dbName);

	if ($conn->connect_error) {
		die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
	}
	$sql = "SELECT * FROM leltar ORDER BY Nev ASC";
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo "<table width='50' align=center class="."table"."><th>UID</th><th>Name</th><th>Type</th><th>Out by</th>";
     //output data of each row
    //Displays amount of records found in leltar_master DB
    while($row = $result->fetch_assoc()) {
		if ($countOfRec == 50){
		}

		if($row["Status"]==0){
			echo "<tr style='background-color:#F5B8B8;' ><td>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}else{
			echo "<tr><td>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td>". "</td></tr>";
		}
       
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

