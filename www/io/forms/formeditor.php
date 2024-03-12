<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>


<?php if (isset($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
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
                  startTimer(display, timeUpLoc, 60);
               };
            </script>
         </form>
      </div>
   </nav>
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
               Csak nem szerkesztés alatt levő form esetén:
               <select class="form-select form-select-sm" id="accessRestrict" name="accessRestrict">
                  <option value="1">Privát</option>
                  <option value="2">Médiás</option>
                  <option value="3">Csak linkkel elérhető</option>
                  <option value="0">Publikus</option>
               </select>
               <br>
               <div class="form-check form-switch">
                  <input type="checkbox" class="form-check-input" id="flexSwitchCheckDefault" data-setting="SingleAnswer">
                  <label class="form-check-label" for="flexSwitchCheckDefault">Korlátozás egy válaszra (még nem
                     működik)</label>
               </div>
               <div class="form-check form-switch">
                  <input type="checkbox" class="form-check-input" id="flexSwitchCheckDefault" data-setting="Anonim">
                  <label class="form-check-label" for="flexSwitchCheckDefault"><b>Anonymous</b> válaszadás</label>
               </div>
               <br>
               <label for="background_img">Háttérkép: <a href="#" id="default-background" data-bs-toggle="popover"
                     data-bs-placement="top">(alapértelmezett)</a></label>
               <input type="file" class="form-control" name="fileToUpload" id="background_img" accept="image/*">
               <button class="btn btn-success" type="submit" onclick="changeBackground()">Feltöltés</button>
               <button class="btn btn-danger" onclick="changeBackground(true)">Törlés</button>
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

   <div class="toast-container position-absolute p-3 indexToasts">
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="save_toast">
         <div class="toast-header">
            <img src="../utility/logo.png" height="30">
            <strong class="me-auto" id="save_status"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
         </div>
      </div>
   </div>


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
            <button class="btn btn-secondary" onclick="showFormAnswers(<?php echo $_GET['formId'] ?>)"><i
                  class='fas fa-check fa-lg'></i> Válaszok</button>
            <button class="btn" onclick="viewForm(<?php echo $_GET['formId'] ?>)"><i class="fas fa-eye"></i></button>
            <button class="btn" onclick="showSettingsModal()"><i class="fas fa-sliders-h fa-lg"></i></button>
         </div>
         <div class="row" id="editorZone">

         </div>
      </div>
   </div>

   <?php
} else {
   echo "<h3 id='titleBar'>Ehhez a tartalomhoz nincs hozzáférésed.</h3>";
}
?>

<script src="backend/elementGenerator.js" type="text/javascript"></script>

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

   $(document).ready(function () {
      //Load form from server
      console.log(<?php echo $_GET['formId'] ?>);
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "getForm", id: <?php echo $_GET['formId'] ?> },
         success: function (data) {
            //console.log(data);
            //if data is 404, redirect to index.php
            if (data == 404) {
               window.location.href = "index.php?invalidID";
            }
            var form = JSON.parse(data);
            var formStatus = JSON.parse(form.Status);
            var formAccess = JSON.parse(form.AccessRestrict);
            var formElements = JSON.parse(form.Data);
            var formAnonim = form.Anonim;
            var formSingleAnswer = form.SingleAnswer;
            var formName = form.Name;

            console.log(formElements);
            //Set form state
            document.getElementById("formState").value = formStatus;

            //Set form access
            document.getElementById("accessRestrict").value = formAccess;

            //Set form Name
            document.getElementById("form_name").innerHTML = formName + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>';
            document.getElementById("description").value = form.Header;

            //Set form settings
            if (formAnonim == 1) {
               document.querySelector('[data-setting="Anonim"]').checked = true;
            }
            if (formSingleAnswer == 1) {
               document.querySelector('[data-setting="SingleAnswer"]').checked = true;
            }

            //Set background
            var style = document.createElement('style');
            style.innerHTML = `
               body::before {
               content: "";
               position: fixed;
               top: 0;
               right: 0;
               bottom: 0;
               left: 0;
               background-image: url(../forms/backgrounds/` + form.Background + `);
               background-size: cover;
               background-position: center;
               z-index: -1;
               }`;
            document.head.appendChild(style);

            formContainer = document.getElementById("editorZone");
            //Load form elements
            if (formElements == null) {
               return;
            }
            i = formElements.length;
            for (var pos = 1; pos <= formElements.length; pos++) {
               //Find element with the same position
               for (var j = 0; j < formElements.length; j++) {
                  if (formElements[j].place == pos) {
                     var element = formElements[j];
                  }
               }
               console.log(element);

               var elementType = element.type;
               var elementId = element.id;
               var elementPlace = element.place;
               var elementSettings = element.settings;


               //Add settings, where possible
               console.log("Id: " + elementId + " Place:" + elementPlace + " Type: " + elementType + " Settings: " + elementSettings);
               formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, "editor"));

            }
         }
      })
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
      console.log("Place: " + place);
      document.getElementById("editorZone").appendChild(generateElement(type, i, place, "")); //Generate the element
   };


   function getCheckSettings(maindiv) {
      var checkboxOptions = [];
      var checkbox_holder = maindiv.getElementsByClassName('select-holder');
      var checkNames = checkbox_holder[0].querySelectorAll('input[type="text"]');
      for (var i = 0; i < checkNames.length; i++) {
         checkboxOptions.push(checkNames[i].value);
      }
      return checkboxOptions;
   }


   //Function to get element settings
   function getElementSettings(type, id) {
      var maindiv = document.getElementById(type + "-" + id); //Get the main div of the element
      var checkOptions = "";
      //Check if the element is a checkbox, radio or dropdown
      if (type == "checkbox" || type == "radio" || type == "dropdown") {
         checkOptions = getCheckSettings(maindiv); //Get the options of the element
      }
      //Get the question of the element
      var elementQuestion = maindiv.querySelector("#e-settings").getElementsByTagName("input")[0].value;
      //Check if the element is required
      var isRequired = maindiv.querySelector("#flexSwitchCheckDefault").checked;
      //Create settings object
      var elementSettings = {
         question: elementQuestion,
         required: isRequired,
         options: checkOptions
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

   //Function to change background TODO!!!!
   function changeBackground(clear) {

      if (clear) {
         var formId = <?php echo $_GET['formId'] ?>;
         $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "changeBackground", id: formId, name: "default.jpg" },
            success: function (data) {
               console.log(data);
            }
         });
         /*          var form_data = new FormData();
                  $.ajax({
                  url: './upload-handler.php', // point to server-side PHP script
                  dataType: 'text', // what to expect back from the PHP script
                  cache: false,
                  contentType: false,
                  processData: false,
                  data: form_data,
                  type: 'POST',
                  success: function (response) {
                     console.log(response);
                  }
               }); */
         return;
      }

      var file_data = $('#background_img').prop('files')[0];
      var formId = <?php echo $_GET['formId'] ?>;

      var form_data = new FormData();
      form_data.append('fileToUpload', file_data);
      form_data.append('formId', formId);

      console.log(form_data);

      $.ajax({
         url: './upload-handler.php', // point to server-side PHP script
         dataType: 'text', // what to expect back from the PHP script
         cache: false,
         contentType: false,
         processData: false,
         data: { form_data: form_data, mode: "uploadBackground" },
         type: 'POST',
         success: function (response) {

            console.log(response);
            /* $.ajax({
               type: "POST",
               url: "../formManager.php",
               data: { mode: "changeBackground", id: formId, name: response },
               success: function (data) {
                  console.log(data);
               }
            }); */
         }
      });

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

      console.log(elements);
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

      var form = {
         "name": formName,
         "elements": formElements
      };
      var formJson = JSON.stringify(form);

      var formState = document.getElementById("formState").value;
      var accessRestrict = document.getElementById("accessRestrict").value;
      var formHeader = document.getElementById("description").value;

      var formAnonim = document.querySelector('[data-setting="Anonim"]').checked ? 1 : 0;
      var formSingleAnswer = document.querySelector('[data-setting="SingleAnswer"]').checked ? 1 : 0;

      //Send form to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { form: formJson, formState: formState, formHeader: formHeader, accessRestrict: accessRestrict, formAnonim: formAnonim, formSingleAnswer: formSingleAnswer, mode: "save", id: <?php echo $_GET['formId'] ?> },
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
      //Send request to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "deleteForm", id: <?php echo $_GET['formId'] ?> },
         success: function (data) {
            //console.log(data);
            window.location.href = "index.php";
         }
      });
   }
</script>