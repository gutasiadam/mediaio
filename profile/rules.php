<?php 
include "header.php";
session_start();
if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}?>

<head>
<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
</head>
<title>mediaIO - Dokumentumok</title>

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
	            <form method="post" class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
                <button class="btn btn-danger my-2 my-sm-0" name="logout-submit" type="submit">Kijelentkezés</button>
              </form>
                      <div class="menuRight"></div>
					</div>
          <script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    </nav>
    <h2 class="rainbow" align="center" style="margin-top:50px;" id="doTitle">Dokumentumok</h2><br />
    <table class="logintable">
    <?php

if ($handle = opendir('../data/documents/')) {
    //Doksik beolvasása vagy mi

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        if((($entry)!='.') and (($entry)!='..')){
        ?>
        <tr><td><a target="_blank" href='../data/documents/<?php echo "$entry\n";?>'><button class="btn btn-light"> <?php echo "$entry\n";?></button></a></td></tr>
       <?php
        }
    }
    closedir($handle);
}
?>
</table>
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

<style>
    .logintable button {
        font-size: 1.75rem;
        min-width: 400px;
        margin-top: 20px;
    }
</style>

