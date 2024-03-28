<?php
session_start();
include ("header.php");
include ("../translation.php"); 

if (!isset ($_SESSION["userId"])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}
if (!in_array("admin", $_SESSION["groups"])) {
   echo "<script>window.location.href = './index.php';</script>";
   exit();
}
?>
<html>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
               drawMenuItemsLeft('forms', menuItems, 2);
            });
         </script>
      </ul>
      <ul class="navbar-nav ms-auto navbarPhP">
         <li>
            <a class="nav-link disabled timelock" href="#"><span id="time"> 60:00 </span>
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
               startTimer(display, timeUpLoc, 60);
            };
         </script>
      </form>
   </div>
</nav>

<div class="toast-container position-absolute p-3 indexToasts">
   <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="save_toast">
      <div class="toast-header">
         <img src="../utility/logo.png" height="30">
         <strong class="me-auto" id="save_status"></strong>
         <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
   </div>
</div>


<!-- Title edit modal -->
<div class="modal fade" id="Title_Modal" tabindex="-1" role="dialog" aria-labelledby="title_ModalLabel"
   aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Kérdőív címe</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <input type='text' class='form-control' id='formTitle' placeholder='Új cím'></input>
         </div>
         <div class="modal-footer">
            <button class="btn btn-success col-lg-auto mb-1" id="clear" data-bs-dismiss="modal"
               onclick="save_title()">Kész</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
         </div>
      </div>
   </div>
</div>
<!-- Title edit modal end -->

<!-- Clear Modal -->
<div class="modal fade" id="delete_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_ModalLabel"
   aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Törlés</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <a>Biztosan ki akarod törölni a kérdőívet?</a>
         </div>
         <div class="modal-footer">
            <button class="btn btn-danger col-lg-auto mb-1" id="clear" data-bs-dismiss="modal"
               onclick="deleteForm()">Törlés</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
         </div>
      </div>
   </div>
</div>
<!-- End of Clear Modal -->

<!-- Settings Modal -->
<div class="modal fade" id="settings_Modal" tabindex="-1" role="dialog" aria-labelledby="settings_ModalLabel"
   aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Beállítások</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <label for="cars">Form állapota:</label>
            <select class="form-select form-select-sm" id="formState" name="formState">
               <option value="0">Nem fogad válaszokat</option>
               <option value="1">Fogad válaszokat</option>
            </select>
            </br>
            <div class="mb-3" id="accessForm">
               Elérhetőség:
               <select class="form-select form-select-sm mb-1" id="accessRestrict" name="accessRestrict">
                  <option value="1">Privát</option>
                  <option value="2">Médiás</option>
                  <option value="3">Csak linkkel elérhető</option>
                  <option value="0">Publikus</option>
               </select>
               <div class="input-group" id="linkHolderGroup" style="display: none;">
                  <input type="text" class="form-control" placeholder="Kérdőív link" aria-label="Form link"
                     id="formLinkHolder">
                  <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">Másolás</button>
               </div>
            </div>
            <div class="form-check form-switch">
               <input type="checkbox" class="form-check-input" id="flexSwitchCheckDefault" data-setting="SingleAnswer">
               <label class="form-check-label" for="flexSwitchCheckDefault">Korlátozás egy válaszra (még nem
                  működik)</label>
            </div>
            <div class="form-check form-switch mb-3">
               <input type="checkbox" class="form-check-input" id="flexSwitchCheckDefault" data-setting="Anonim">
               <label class="form-check-label" for="flexSwitchCheckDefault"><b>Anonymous</b> válaszadás</label>
            </div>
            <label class="mb-2" for="background_img">Háttérkép: <a href="#" id="default-background"
                  data-bs-toggle="popover" data-bs-placement="top">(alapértelmezett)</a></label>
            <div class="input-group">

               <input type="file" class="form-control" placeholder="Háttérkép feltöltése" aria-label="Background upload"
                  name="fileToUpload" id="background_img" accept="image/*">
               <button class="btn btn-outline-danger" type="button"
                  onclick="changeBackground(<?php echo $_GET['formId'] ?>,true)">Reset</button>
               <button class="btn btn-outline-success" type="button"
                  onclick="changeBackground(<?php echo $_GET['formId'] ?>)">Feltöltés</button>
            </div>
         </div>
         <div class="modal-footer">
            <button class="btn btn-success col-lg-auto mb-1" id="save" data-bs-dismiss="modal"
               onclick="saveForm(false)">Mentés</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Mégse</button>
         </div>
      </div>
   </div>
</div>
<!-- End of Settings Modal -->



<div class="form">
   <div id="form-header">
      <h2 class="rainbow" id="form_name" style="cursor: pointer;"></h2>
      <textarea class="form-control" id="description"></textarea>
   </div>
   <div class="container">
      <div class="row" id="form-option-buttons">
         <div class="dropdown" id="tools">

            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
               aria-expanded="false">
               Új hozzáadása
            </button>
            <ul class="dropdown-menu">
               <li><a class="dropdown-item" href="#" onclick="addFormElement('email')"><i class="fas fa-at fa-2x"></i>
                     E-Mail</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('shortText')"><i
                        class="fas fa-grip-lines fa-2x"></i> Rövid szöveg</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('longText')"><i
                        class="fas fa-align-justify fa-2x"></i> Hosszú szöveg</a></li>

               <!-- Feleletválasztós -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('radio')"><i
                        class="far fa-dot-circle fa-2x"></i>
                     Feleletválasztós</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('checkbox')"><i
                        class="far fa-check-square fa-2x"></i> Jelölőnégyzet</a>
               </li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('dropdown')"><i
                        class="fas fa-chevron-down fa-2x"></i> Legördülő lista</a></li>

               <!-- Skála -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('scaleGrid')"><i
                        class="fas fa-th fa-2x"></i> Feleletválasztós rács</a></li>

               <!-- Idő -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('date')"><i
                        class="fas fa-calendar-alt fa-2x"></i> Dátum</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('time')"><i class="fas fa-clock fa-2x"></i>
                     Idő</a></li>

               <!-- Fájl -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('fileUpload')"><i
                        class="fas fa-file fa-2x"></i> Fájl feltöltés</a>
               </li>
               <!--
   
   <li><a class="dropdown-item" href="#"> <span draggable="false" ondragstart="drag(event)"
            class="heading toolIcon clickableIcon" name="Szakaszcím"><i
               class="fas fa-heading fa-2x"></i></span></a></li>
   <li><a class="dropdown-item" href="#"><span draggable="false" ondragstart="drag(event)"
            class="paragraph toolIcon clickableIcon" name="Szakasz bekezdés"><i
               class="fas fa-paragraph fa-2x"></i></span></a></li> -->
               <!-- 
   <li><a class="dropdown-item" href="#"><span draggable="false" ondragstart="drag(event)"
            class="scale toolIcon clickableIcon" name="Lineáris skála"><i
               class="fas fa-sort-numeric-up fa-2x"></i></span> Lineáris skála</a></li> -->
            </ul>
         </div>
         <button class="btn btn-primary" onclick="saveForm(false)">Mentés</button>
         <button class="btn btn-danger" onclick="showDeleteModal()"><i class='fas fa-trash-alt fa-lg'></i></button>
         <button class="btn" onclick="showFormAnswers(<?php echo $_GET['formId'] ?>)"><i
               class='fas fa-align-left fa-lg'></i></button>
         <button class="btn" onclick="viewForm(<?php echo $_GET['formId'] ?>)"><i class="fas fa-eye"></i></button>
         <button class="btn" onclick="showSettingsModal()"><i class="fas fa-sliders-h fa-lg"></i></button>
      </div>
      <div class="row" id="editorZone">

      </div>
   </div>
</div>


<script src="frontEnd/elementGenerator.js" type="text/javascript"></script>
<script src="frontEnd/backgroundManager.js" type="text/javascript"></script>
<script src="frontEnd/fetchData.js" type="text/javascript"></script>

<script>
   //Changing this variable if something is changed
   var everythingSaved = true;


   //If the user tries to leave the page without saving, show a warning
   window.onbeforeunload = function () {
      if (!everythingSaved) {
         return "Nem mentett változtatások vannak! Biztosan továbblépsz?";
      }
   }

   //After any keypress something is changed
   document.addEventListener('keypress', function (event) {
      everythingSaved = false;
   });

   //Place popover on default background
   document.addEventListener('DOMContentLoaded', function () {
      var popoverTriggerEl = document.getElementById('default-background');
      var popover = new bootstrap.Popover(popoverTriggerEl, {
         content: '<img src="./backgrounds/default.jpg" width="200px" alt="Popover image">',
         html: true,
         trigger: 'hover',
         placement: 'right'
      });
   });

   //Function to view form
   function viewForm(formId) {
      window.location.href = "viewform.php?formId=" + formId;
   }

   //Function to show form answers
   function showFormAnswers(formId) {
      window.location.href = "formanswers.php?formId=" + formId;
   }

   //Function to show delete modal
   function showDeleteModal() {
      $('#delete_Modal').modal('show');
   }

   //Function to show settings modal
   function showSettingsModal() {
      $('#settings_Modal').modal('show');
   }

   //Function to show title modal
   var i = 0;
   document.getElementById("form_name").addEventListener("click", function () {
      $('#Title_Modal').modal('show');
   });

   //Function to save title
   function save_title() {
      var title = document.getElementById("formTitle").value;
      document.getElementById("form_name").innerHTML = title + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>';
      everythingSaved = false;
   }

   //Function to show link holder

   function showLink(formHash, show = true) {
      if (show) {
         document.getElementById("linkHolderGroup").style.display = "flex";
         document.getElementById("linkHolderGroup").classList.add("mb-1");
         var linkholder = document.getElementById("formLinkHolder");
         linkholder.value = "https://<?php echo $_SERVER['HTTP_HOST']; ?>/forms/viewform.php?form=" + formHash;
      } else {
         document.getElementById("linkHolderGroup").style.display = "none";
         document.getElementById("linkHolderGroup").classList.remove("mb-1");
         var linkholder = document.getElementById("formLinkHolder");
         linkholder.value = "https://<?php echo $_SERVER['HTTP_HOST']; ?>/forms/viewform.php?form=" + formHash;;
      }
   }

   function copyLink() {
      var copyText = document.getElementById("formLinkHolder");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");
   }


   $(document).ready(function () {
      //Load form from server

      let formId = <?php if (isset ($_GET['formId'])) {
         echo $_GET['formId'];
      } else {
         echo '-1';
      } ?>;
      let formHash = <?php if (isset ($_GET['form'])) {
         echo '"' . $_GET['form'] . '"';
      } else {
         echo 'null';
      } ?>;

      async function loadPageAsync(formId, formHash) {

         var form = await FetchData(formId, formHash);
         await loadPage(form, "editor");
      }

      loadPageAsync(formId, formHash);
      $('#accessRestrict').change(function () {
         // Assuming the specific option value is 'specificOption'
         if ($(this).val() === '3') {
            // Assuming your link has an id of 'myLink'
            showLink(formHash);
         } else {
            showLink(formHash, false);
         }
      });
   });

   //Function to check if question id is used
   function checkIdNotUsed(id) {
      var elements = document.getElementById("editorZone").getElementsByClassName("form-member");
      for (var j = 0; j < elements.length; j++) {
         if (elements[j].id.split("-")[1] == id) {
            id++;
            checkIdNotUsed(id);
         }
      }
      return id;
   }


   //Function to add a new form element
   function addFormElement(type) {
      everythingSaved = false;
      i++;
      i = checkIdNotUsed(i); //Check if id is used
      console.log("Adding form element: " + type);
      var place = document.getElementById("editorZone").getElementsByClassName("form-member").length + 1; //Get the place of the new element
      //console.log("Place: " + place);
      document.getElementById("editorZone").appendChild(generateElement(type, i, place, "", "editor")); //Generate the element
   };


   function getCheckSettings(maindiv, type) {
      var checkboxOptions = [];
      var checkbox_holder = maindiv.getElementsByClassName(type + '-holder');
      var checkNames = checkbox_holder[0].querySelectorAll('input[type="text"]');
      for (var i = 0; i < checkNames.length; i++) {
         checkboxOptions.push(checkNames[i].value);
      }
      return checkboxOptions;
   }

   function getGridSettings(maindiv) {
      var grid_holder = maindiv.getElementsByClassName('grid-holder');
      var rows = grid_holder[0].getElementsByClassName('grid-row').length;

      var labels = grid_holder[0].querySelectorAll('input[type="text"]');
      labels = Array.from(labels).map(function (el) {
         return el.value;
      });

      var gridOptions = {
         'rows': rows,
         'columns': 5,
         'options': labels
      };
      return gridOptions;
   }


   //Function to get element settings
   function getElementSettings(type, id) {
      var maindiv = document.getElementById(type + "-" + id); //Get the main div of the element
      var extraOptions = "";
      //Check if the element is a checkbox, radio or dropdown
      if (type == "checkbox" || type == "radio" || type == "dropdown") {
         extraOptions = getCheckSettings(maindiv, type); //Get the options of the element
      }
      if (type == "scaleGrid") {
         extraOptions = getGridSettings(maindiv);
      }
      //Get the question of the element
      var elementQuestion = maindiv.querySelector("#e-settings").getElementsByTagName("input")[0].value;
      //Check if the element is required
      var isRequired = maindiv.querySelector("#flexSwitchCheckDefault").checked;
      //Create settings object
      var elementSettings = {
         question: elementQuestion,
         required: isRequired,
         options: extraOptions
      }
      //Return settings as JSON string
      elementSettings = JSON.stringify(elementSettings).replace(/"/g, '\\"');
      return elementSettings;
   }


   //Function to remove an element
   function removeElement(type, id) {
      everythingSaved = false;
      var element = document.getElementById(type + "-" + id);
      element.remove();
   }

   //Function to move an element up
   function moveUp(type, id) {
      everythingSaved = false;
      var element = document.getElementById(type + "-" + id);

      element.setAttribute('data-position', parseInt(element.getAttribute('data-position')) - 1);
      //Get the previous element
      var prevElement = element.previousElementSibling;
      prevElement.setAttribute('data-position', parseInt(prevElement.getAttribute('data-position')) + 1);
      if (prevElement != null) {
         element.parentNode.insertBefore(element, prevElement);
      }
   }

   //Function to move an element down
   function moveDown(type, id) {
      everythingSaved = false;
      var element = document.getElementById(type + "-" + id);
      element.setAttribute('data-position', parseInt(element.getAttribute('data-position')) + 1);
      var nextElement = element.nextElementSibling;
      nextElement.setAttribute('data-position', parseInt(nextElement.getAttribute('data-position')) - 1);
      if (nextElement != null) {
         element.parentNode.insertBefore(nextElement, element);
      }
   }



   //Every 10 seconds, save the form
   setInterval(function () {
      if (everythingSaved) {
         return;
      }
      //Add growing spinner to the first h6
      //document.getElementById("time").innerHTML = "<div class='spinner-grow text-success' role='status'></div>"
      saveForm(true);
   }, 10000);


   function saveForm(auto) {
      //Get all elements
      var formEditor = document.getElementById("editorZone");
      var elements = formEditor.getElementsByClassName("form-member");
      var formName = document.getElementById("form_name").innerHTML.split("&nbsp")[0];

      //If form name is empty, set it to "Névtelen"
      if (formName == "") {
         formName = "Névtelen";
      }

      //console.log(elements);
      var formElements = [];
      //Get all elements and their settings
      for (var k = 0; k < elements.length; k++) {
         var elementType = elements[k].id.split("-")[0];
         var elementId = elements[k].id.split("-")[1];
         var elementPlace = k + 1;
         var elementSettings = getElementSettings(elementType, elementId);

         //Create element object
         var formElement = {
            "type": elementType,
            "place": elementPlace,
            "id": elementId,
            "settings": elementSettings
         };
         console.log(formElement);
         formElements.push(formElement);
      }



      var formState = document.getElementById("formState").value;
      var accessRestrict = document.getElementById("accessRestrict").value;
      var formHeader = document.getElementById("description").value;

      var formAnonim = document.querySelector('[data-setting="Anonim"]').checked ? 1 : 0;
      var formSingleAnswer = document.querySelector('[data-setting="SingleAnswer"]').checked ? 1 : 0;

      var form = {
         "name": formName,
         "header": formHeader,
         "elements": formElements,
         "state": formState,
         "access": accessRestrict,
         "anonim": formAnonim,
         "singleAnswer": formSingleAnswer
      };
      var formJson = JSON.stringify(form);

      var formId = <?php if (isset ($_GET['formId'])) {
         echo $_GET['formId'];
      } else {
         echo '-1';
      } ?>;
      var formHash = <?php if (isset ($_GET['form'])) {
         echo '"' . $_GET['form'] . '"';
      } else {
         echo 'null';
      } ?>;

      console.log(formJson);
      //Send form to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { form: formJson, mode: "save", id: formId, formHash: formHash },
         success: function (data) {
            console.log(data);

            const toastLiveExample = document.getElementById('save_toast');
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample, { delay: 3000 });

            //If data is 200, append a span with a message after time
            if (data == 200) {
               if (auto) {
                  document.getElementById("save_status").innerHTML = "Automatikusan mentve!";
               } else {
                  document.getElementById("save_status").innerHTML = "Sikeres mentés";
               }
               everythingSaved = true;
            } else {
               document.getElementById("save_status").innerHTML = "Sikertelen mentés";
            }
            toastBootstrap.show();

         }
      });
   }

   function deleteForm() {
      var formId = <?php if (isset ($_GET['formId'])) {
         echo $_GET['formId'];
      } else {
         echo '-1';
      } ?>;
      var formHash = <?php if (isset ($_GET['form'])) {
         echo '"' . $_GET['form'] . '"';
      } else {
         echo 'null';
      } ?>;
      //Send request to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "deleteForm", id: formId, formHash: formHash },
         success: function (data) {
            //console.log(data);
            window.location.href = "index.php";
         }
      });
   }
</script>