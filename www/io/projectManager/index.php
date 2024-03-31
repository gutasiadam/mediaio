<?php
session_start();
include ("header.php");
include ("../translation.php"); ?>

<html>
<?php
if (!isset ($_SESSION["userId"])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}
?>

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
   <!-- Are you sure? modal -->

   <div class="modal fade" id="areyousureModal" tabindex="-1" aria-labelledby="areyousureModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Biztosan törölni szeretnéd?</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
               <button type="button" class="btn btn-danger" id="deleteTaskSure">Törlés</button>
            </div>
         </div>
      </div>
   </div>



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
                     <label for="projectVisibility" class="col-form-label">Projekt láthatósága:</label>
                     <select class="form-select" id="projectVisibility">
                        <option value="0">Mindenki</option>
                        <option value="1">Médiás</option>
                        <option value="2">Stúdiós</option>
                        <option value="3">Admin</option>
                        <option value="4">Hozzáadott emberek (Még nem működik)</option>
                     </select>
                  </div>
                  <div class="mb-3 input-group">
                     <span class="input-group-text">Projekt határideje: </span>
                     <input type="date" class="form-control" id="projectDate">
                     <input type="time" class="form-control" id="projectTime">
                  </div>
                  <div class="mb-3 input-group">
                     <span class="input-group-text">Projekt törlése: </span>
                     <input type="text" class="form-control" id="deleteText">
                     <button type="button" class="btn btn-outline-danger" id="deleteButton">Törlés</button>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-success" id="saveButton">Mentés</button>
            </div>
         </div>
      </div>
   </div>


   <!-- Description modal -->
   <div class="modal fade" id="projectDescModal" tabindex="-1" aria-labelledby="projectDescModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Leírás</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form>
                  <textarea class="form-control" id="projectDescription"></textarea>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-success" id="saveDescButton">Mentés</button>
            </div>
         </div>
      </div>
   </div>

   <!-- Task modal -->
   <div class="modal fade" id="taskEditorModal" tabindex="-1" aria-labelledby="taskEditorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="taskTitle">Új feladat hozzáadása</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form>
                  <div class="mb-3" id="taskName">
                     <label for="textTaskName" class="col-form-label">Feladat neve:</label>
                     <input type="text" class="form-control" id="textTaskName">
                  </div>
                  <div class="mb-3" id="taskData">
                  </div>
                  <div class="mb-3" id="taskMembers">
                  </div>
                  <div class="mb-3 input-group">
                     <span class="input-group-text">Feladat határideje: </span>
                     <input type="date" class="form-control" id="taskDate" placeholder="Nap">
                     <input type="time" class="form-control" id="taskTime" placeholder="Időpont">
                  </div>
                  <div class="form-check mb-3">
                     <label for="taskSubmittable" class="form-check-label">Elvégzendő feladat</label>
                     <input type="checkbox" class="form-check-input" name="taskSubmittable" id="taskSubmittable">
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-target="#areyousureModal"
                  data-bs-toggle="modal" id="deleteTask" style="display: none;">Törlés</button>
               <button type="button" class="btn btn-success" id="saveNewTask">Mentés</button>
            </div>
         </div>
      </div>
   </div>

   <!-- UI modal -->
   <div class="modal fade" id="taskFillModal" tabindex="-1" aria-labelledby="taskFillModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="taskFillTitle">Feladat kitöltése</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form>
                  <div class="mb-3" id="taskFillData">
                  </div>
                  <div class="mb-3 input-group" id="taskFillDeadline">
                     <span class="input-group-text">Feladat határideje: </span>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-success" id="submitAnswer">Mentés</button>
            </div>
         </div>
      </div>
   </div>

   <!-- New member modal -->
   <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Tagok szerkesztése</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form>
                  <div class="mb-3">
                     <label for="projectMembers" class="col-form-label">Projekt tagjai:</label>
                     <div id="projectMembersSelect"></div>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
               <button type="button" class="btn btn-success" id="saveProjectMembers">Mentés</button>
            </div>
         </div>
      </div>
   </div>


   <h1 class="rainbow">Projekt Menedzsment&nbsp;
      <?php if (isset ($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
         <button class="btn btn-success" onclick=createNewProject()><i class="fas fa-plus fa-lg"></i></button>
      <?php } ?>
   </h1>

   <div class="container">

      <div class="row optionsHolder">

         <input type="checkbox" class="btn-check" id="editorON" autocomplete="off">
         <label class="btn btn-outline-secondary" for="editorON">Szerkesztő mód</label>
      </div>


      <div class="projectHolder" id="projectHolder">

      </div>

   </div>

</body>

<script src="frontEnd/projektGen.js" crossorigin="anonymous"></script>
<script src="frontEnd/taskGen.js" crossorigin="anonymous"></script>
<script src="frontEnd/projektSettings.js" crossorigin="anonymous"></script>
<script src="frontEnd/fetchData.js" crossorigin="anonymous"></script>

<?php if (in_array("admin", $_SESSION["groups"])) { ?>
   <script src="frontEnd/adminButtons.js" crossorigin="anonymous"></script>
<?php } ?>
<script>

   // Disable double tap zoom
   document.addEventListener('dblclick', function (event) {
      event.preventDefault();
   }, { passive: false });

   var editorON = false;

   $(document).ready(function () {

      async function loadPage() {
         let projects = await fetchProjects();
         generateProjects(projects);
      }

      loadPage();

      // Add event listener to the checkbox
      document.getElementById('editorON').addEventListener('change', function () {
         var elements = document.getElementsByClassName('taskCard');

         if (this.checked) {
            editorON = true;
            document.getElementById('editorON').classList.add('btn-secondary');
            document.getElementById('editorON').classList.remove('btn-outline-secondary');
            for (var i = 0; i < elements.length; i++) {
               elements[i].classList.add('editorOn');
               elements[i].draggable = true;
            }
         } else {
            editorON = false;
            document.getElementById('editorON').classList.remove('btn-secondary');
            document.getElementById('editorON').classList.add('btn-outline-secondary');
            for (var i = 0; i < elements.length; i++) {
               elements[i].classList.remove('editorOn');
               elements[i].draggable = false;
            }
         }
      });
   });

</script>

</html>