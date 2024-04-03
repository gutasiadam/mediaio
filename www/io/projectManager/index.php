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
   <h1 class="rainbow">Jelenlegi projektek&nbsp;
      <?php if (isset($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
         <button class="btn btn-success" onclick=createNewProject()><i class="fas fa-plus fa-lg"></i></button>
      <?php } ?>
   </h1>

   <!-- <button type="button" class="btn custom-kurva-anyja">LOFASZ</button> -->

   <div class="container">

      <div class="projectHolder" id="projectHolder">

      </div>

   </div>

</body>

<script src="frontEnd/projektGen.js" crossorigin="anonymous"></script>
<script src="frontEnd/taskGen.js" crossorigin="anonymous"></script>
<script src="frontEnd/fetchData.js" crossorigin="anonymous"></script>
<script src="frontEnd/dragAndDrop.js" crossorigin="anonymous" defer></script>

<?php if (in_array("admin", $_SESSION["groups"])) { ?>
   <script src="frontEnd/projektSettings.js" crossorigin="anonymous"></script>
   <script src="frontEnd/adminButtons.js" crossorigin="anonymous"></script>
<?php } ?>
<script>

   // Disable double tap zoom
   document.addEventListener('dblclick', function (event) {
      event.preventDefault();
   }, { passive: false });


   $(document).ready(function () {

      async function loadPage(mobile = false) {
         if (mobile) {
            let projects = await fetchProjects();
            await generateProjects(projects, true);
         }
         else {
            let projects = await fetchProjects();
            await generateProjects(projects);
         }
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
         })
         documentReady();
      }

      if (window.innerWidth < 768) {
         loadPage(true);
      } else {
         loadPage();
      }
   });

</script>

</html>