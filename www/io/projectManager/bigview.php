<?php
session_start();
include ("header.php");
include ("../translation.php"); ?>

<html>
<?php
if (!isset($_SESSION["userId"])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}
?>


<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
   <a class="navbar-brand" href="./index.php">
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
            <a class="nav-link disabled timelock" href="#"><span id="time"> 30:00 </span>
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
               startTimer(display, timeUpLoc, 30);
            };
         </script>
      </form>
   </div>
</nav>


<body>
   <?php include "modals.php"; ?>
   <!-- <h1 class="rainbow" id="projectTitle">Project</h1> -->

   <div class="container">
      <div class="projectHolder" id="projectHolder">

      </div>

   </div>

</body>

<script>

   // Disable double tap zoom
   document.addEventListener('dblclick', function (event) {
      event.preventDefault();
   }, { passive: false });

   $(document).ready(function () {

      async function loadPage() {
         let project = await fetchProject(<?php echo $_GET['projectID']; ?>);
         /* document.getElementById('projectTitle').textContent = project.Name; */

         await generateBigView(project);

         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
         })

         documentReady();
      }

      if (window.innerWidth > 768) {
         window.location.href = "./index.php";
      }

      loadPage();
   });

</script>