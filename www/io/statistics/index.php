<?php

namespace Mediao;

session_start();

if (!isset($_SESSION['userId'])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}

include "header.php";
error_reporting(E_ERROR | E_PARSE);

?>

<body>


   <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="../index.php">
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
                  drawMenuItemsLeft('adatok', menuItems, 2);
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


   <h2 class="rainbow">Leltár adatok</h2>

   <div class="container">
      <div class="row justify-content-center">
         <div class="btn-group mb-1" role="group" aria-label="Beállítások" style="max-width: 600px">
            <input type="checkbox" class="btn-check filterButton" id="medias" autocomplete="off">
            <label class="btn btn-outline-secondary" for="medias">Médiás</label>

            <input type="checkbox" class="btn-check filterButton" id="studios" autocomplete="off">
            <label class="btn btn-outline-secondary" for="studios">Stúdiós</label>

            <input type="checkbox" class="btn-check filterButton" id="event" autocomplete="off">
            <label class="btn btn-outline-secondary" for="event">Event</label>

            <input type="checkbox" class="btn-check filterButton" id="isOut" autocomplete="off">
            <label class="btn btn-outline-secondary" for="isOut">Kinnlévő</label>

            <input type="checkbox" class="btn-check filterButton" id="isAvailable" autocomplete="off">
            <label class="btn btn-outline-secondary" for="isAvailable">Nem kölcsönözhető</label>
         </div>
      </div>

      <div class="statsTable" id="tableContainer">

      </div>
   </div>

</body>