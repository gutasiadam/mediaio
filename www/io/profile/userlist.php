<?php
use Mediaio\Core;
use Mediaio\Database;
use Mediaio\MailService;

// require __DIR__ . '/../vendor/autoload.php'; ---> CSAK AZÉRT VAN KOMMENTELVE MERT NÁLAM ENÉLKÜL MŰKÖDIK
require_once __DIR__ . '/../Core.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Mailer.php';
include ("header.php");

//Suppresses error messages
error_reporting(E_ERROR | E_PARSE);

session_start();
if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
} 
?>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<title>Elérhetőségek</title>


<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
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
      <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
        type="submit">Kijelentkezés</button>
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



<?php

$countOfRec = 0;
$sql = "SELECT usernameUsers, emailUsers, lastName, firstName, teleNum, AdditionalData FROM users ORDER BY lastName, firstName ASC";
$conn = Database::runQuery_mysqli();
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo "<table class=" . "table" . " id=" . "userlist" . "><th>Vezetéknév</th><th>Keresztnév</th><th>Felhasználónév</th><th>e-mail cím</th><th>Telefonszám</th><th>Csoportok</th>";
  //output data of each row
  //Displays amount of records found in leltar_master DB
  while ($row = $result->fetch_assoc()) {
    if (!empty($row["AdditionalData"])) {
      $groupData = json_decode($row["AdditionalData"], true);


      $userGroups = implode(", ", $groupData["groups"]);
    } else {
      $userGroups = "Nincs csoport";
    }

    echo "<tr><td>" . $row["lastName"] . "</td><td>" . $row["firstName"] . "</td><td>" . $row["usernameUsers"] . "</td><td><a href=mailto:" . $row["emailUsers"] . " target=_top>" . $row["emailUsers"] . "</a></td><td>" . $row["teleNum"] . "</td><td>" . $userGroups . "</td><td></tr>";

    $countOfRec += 1;
  }
} else {
  echo "0 results";
}
echo "</table>";
$conn->close(); ?>