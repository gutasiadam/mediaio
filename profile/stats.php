<?php
session_start();
include("header.php");
 if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){


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