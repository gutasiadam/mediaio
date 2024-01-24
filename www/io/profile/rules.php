<?php
session_start();
include "header.php";

if (!isset($_SESSION['userId'])) {
  header("Location: index.php?error=AccessViolation");
} ?>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<title>Arpad Media IO - Dokumentumok</title>

<?php if (isset($_SESSION["userId"])) { ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
<?php } ?>
<h2 class="rainbow" id="doTitle">Dokumentumok</h2>
<table class="logintable" id="documents">
  <?php

  if ($handle = opendir('../data/documents/')) {
    //Doksik beolvasása vagy mi
  
    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
      if ((($entry) != '.') and (($entry) != '..')) {
        ?>
        <tr>
          <td><a target="_blank" href='../data/documents/<?php echo "$entry\n"; ?>'><button
                class="btn btn-secondary mb-2 w-100">
                <?php echo "$entry\n"; ?>
              </button></a></td>
        </tr>
        <?php
      }
    }
    closedir($handle);
  }
  ?>
</table>