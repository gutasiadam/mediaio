<?php
session_start();
include("header.php");
 if ($_SESSION['role']>=3){ ?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      
    <a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
  
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto navbarUl">
    </ul>
    <ul class="navbar-nav navbarPhP">
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
$search = date('Y-m-d');
$search2 = date('Y-m-d', strtotime($search. ' - 7 days'));

$conn = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
$sql = ("SELECT * FROM `events` WHERE add_Date BETWEEN '$search2' AND '$search'");
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
<tr><td><?php echo $row_cnt5 ?> felhasználó regisztrálva.</td></tr></table>
</body>

 <?php }else{echo "<h2 class='text text-danger'>Nincs jogosultságod az oldal megtekintéséhez.</h2>";}?>