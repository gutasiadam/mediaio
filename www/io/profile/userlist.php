<?php 
session_start();
include "header.php";
use Mediaio\Core;
use Mediaio\Database;
use Mediaio\MailService;
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../Core.php';
require_once __DIR__.'/../Database.php';
require_once __DIR__.'/../Mailer.php';
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

<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems,2);
        });
      </script>
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
</nav> <?php  } ?>


<?php 

	$countOfRec=0;
	$sql = "SELECT usernameUsers, emailUsers, lastName, firstName, teleNum, AdditionalData FROM users ORDER BY lastName, firstName ASC";
  $conn=Database::runQuery_mysqli();
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo "<table width='50' align=center class="."table"."><th>Vezetéknév</th><th>Keresztnév</th><th>Felhasználónév</th><th>e-mail cím</th><th>Telefonszám</th><th>Csoportok</th>";
     //output data of each row
    //Displays amount of records found in leltar_master DB
    while($row = $result->fetch_assoc()) {
      if(!empty($row["AdditionalData"])){
      $groupData=json_decode($row["AdditionalData"],true);


      $userGroups=implode(", ",$groupData["groups"]);
      }else{
        $userGroups="Nincs csoport";
      }

		echo "<tr><td>".$row["lastName"]."</td><td>".$row["firstName"]."</td><td>".$row["usernameUsers"]. "</td><td><a href=mailto:".$row["emailUsers"]." target=_top>".$row["emailUsers"]."</a></td><td>".$row["teleNum"]."</td><td>".$userGroups."</td><td></tr>";
       
		$countOfRec += 1;
	}
} else {
    echo "0 results";
}
echo "</table>";
$conn->close();?>
