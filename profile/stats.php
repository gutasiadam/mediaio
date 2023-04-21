<?php
use Mediaio\Database;
require_once "../Database.php";
session_start();
include("header.php");
 if ($_SESSION['role']>=3){ ?>

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

$conn = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
$sql = ("SELECT * FROM `events");
$result = $conn->query($sql) or die($conn->error);
//echo $search2;

$row_cnt = $result->num_rows;//esemény lett létrehozva az elmúlt 7 napban.

$sql = ("SELECT * FROM `eventprep`");
$result = $conn->query($sql) or die($conn->error);
$prep_cnt = $result->num_rows;//esemény lett létrehozva az elmúlt 7 napban.

$sql = ("SELECT * FROM `events`");
$result = $conn->query($sql) or die($conn->error);
$row_cnt2 = $result->num_rows;//db esemény található a naptárban.

$conn = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
$sql = ("SELECT * FROM `leltar`");
$result = $conn->query($sql) or die($conn->error);
$row_cnt3 = $result->num_rows;//Max_tárgy a raktárban

$sql = ("SELECT * FROM `leltar` WHERE Status = 1");
$result = $conn->query($sql) or die($conn->error);
$row_cnt4 = $result->num_rows;//Jelenleg benn tárgyak

$sql = ("SELECT userNameUsers FROM `users`");
$result = $conn->query($sql) or die($conn->error);
$row_cnt5 = $result->num_rows;//regiszztrált felhasználók
$conn->close();
?>

<body>
<h1 align=center >Statisztika</h1>
<table>
<tr><td><h2><?php echo $row_cnt3 ?>/<span class="text text-success"><?php echo $row_cnt4 ?></span> tárgy van benn.</h2><h6>(<?php echo number_format((float)(($row_cnt4/$row_cnt3)*100),2,'.', ''); ?>%)</h6></td></tr>
<tr><td>Az elmúlt 7 napban <span class="text text-success"><?php echo $row_cnt ?></span>db eseményt hoztak létre. <span class="text text-danger"><?php echo $prep_cnt ?></span> megerősítésre vár. (összesen <?php echo $row_cnt2 ?> esemény a naptárban.)  </td></tr>
<tr><td><?php echo $row_cnt5 ?> felhasználó regisztrálva.</td></tr>
</table>
      </br>
<h3 align=left >Legutóbbi hét eseményei</h1>
<table class="table table-bordered">
  <?php
  // $conn = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
  // $sql = ("SELECT * FROM takelog ORDER BY Date DESC LIMIT 10");
  // $result = $conn->query($sql) or die($conn->error);

  $connectionObject=Database::runQuery_mysqli();
        $query = "SELECT * FROM takelog WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY);";
       // echo $query;
        $result=mysqli_query($connectionObject,$query);
        echo '<tr><th>Dátum</th><th>Felhasználó</th><th>Tárgy</th><th>Esemény</th><th>✔?</th><th>Usercheckelte:</th></tr>';
        foreach($result as $row)
           {
            $ackcol="?";
            if($row['Acknowledged']=="1"){
              $ackcol="✔";
            }else{
              $ackcol="❌";
            }

            $event="?";
            if($row['Event']=="SERVICE"){
              $event="🔧";
            }else{$event=$row['Event'];} 
            echo '<tr><td>'.$row['Date'].'</td><td>'.$row['User'].'</td><td>'.$row['Item'].'</td><td>'.$event.'</td><td>'.$ackcol.'</td><td>'.$row['ACKBY'].'</td></tr>';
           }
  ?>
</table>
</body>

 <?php }else{echo "<h2 class='text text-danger'>Nincs jogosultságod az oldal megtekintéséhez.</h2>";}?>