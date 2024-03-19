<?php
session_start();
include ("header.php");
include ("../translation.php"); ?>

<html>
<?php

if (isset ($_SESSION["userId"])) { ?>
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                  drawMenuItemsLeft('projectmanager', menuItems, 2);
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

   <body>
      <!-- Project settings modal -->

      <div class="modal fade" id="projectSettingsModal" tabindex="-1" aria-labelledby="projectSettingsModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="projectSettingsModalLabel">Projekt beállítások</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <form>
                     <div class="mb-3">
                        <label for="projectName" class="col-form-label">Projekt neve:</label>
                        <input type="text" class="form-control" id="projectName">
                     </div>
                     <div class="mb-3">
                        <label for="projectDescription" class="col-form-label">Projekt leírása:</label>
                        <textarea class="form-control" id="projectDescription"></textarea>
                     </div>
                     <div class="mb-3">
                        <label for="projectMembers" class="col-form-label">Projekt tagjai:</label>
                        <input type="text" class="form-control" id="projectMembers">
                     </div>
                     <div class="mb-3">
                        <label for="projectDeadline" class="col-form-label">Projekt határideje:</label>
                        <input type="date" class="form-control" id="projectDeadline">
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                  <button type="button" class="btn btn-primary">Mentés</button>
               </div>
            </div>
         </div>
      </div>


      <h1 class="rainbow">Projekt Menedzsment</h1>

      <div class="container" id="projectHolder">
         <?php if (isset ($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
            <div class="row" id="admin_opt">
               <button class="btn btn-success noprint mb-2 mr-sm-2" onclick=createNewProject()><i
                     class="fas fa-plus fa-lg"></i></button>
            </div>
         <?php } ?>

      </div>

   </body>

   <script src="frontEnd/projektGen.js" crossorigin="anonymous"></script>
   <script src="frontEnd/projektSettings.js" crossorigin="anonymous"></script>
   <script src="frontEnd/fetchData.js" crossorigin="anonymous"></script>

   <script>

      $(document).ready(function () {

         async function loadPage() {
            let projects = await fetchProjects();
            console.log(projects);
            generateProjects(projects);
         }

         loadPage();
      });

   </script>

   <?php

} else {
   header("Location: ../index.php?error=AccessViolation");
   exit();
}
?>


</html>