<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>


<?php if (isset($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
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
                  startTimer(display, timeUpLoc);
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

   <div class="toast-container position-absolute p-3 indexToasts">
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="save_toast">
         <div class="toast-header">
            <img src="../utility/logo.png" height="30">
            <strong class="me-auto" id="save_status"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
         </div>
      </div>
   </div>
   <div>
      <h2 class="rainbow" id="form_name" style="cursor: pointer;"></h2>
   </div>
   <div class="container">
      <div class="row">
         <div class="col-4">
            <h6>Elemek</h6>
            <div class="dropdown" id="tools">
               <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  Új hozzáadása
               </button>
               <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" onclick="addFormElement('email')"><i class="fas fa-at fa-2x"></i>
                        E-Mail</a>
                  </li>
                  <li><a class="dropdown-item" href="#" onclick="addFormElement('date')"><i
                           class="fas fa-calendar-alt fa-2x"></i> Dátum</a></li>
                  <!--
                  <li><a class="dropdown-item" href="#"><span draggable="false" ondragstart="drag(event)"
                           class="date_time toolIcon clickableIcon" name="Idő"><i
                              class="fas fa-clock fa-2x"></i></span></a></li>
                  <li><a class="dropdown-item" href="#"> <span draggable="false" ondragstart="drag(event)"
                           class="heading toolIcon clickableIcon" name="Szakaszcím"><i
                              class="fas fa-heading fa-2x"></i></span></a></li>
                  <li><a class="dropdown-item" href="#"><span draggable="false" ondragstart="drag(event)"
                           class="paragraph toolIcon clickableIcon" name="Szakasz bekezdés"><i
                              class="fas fa-paragraph fa-2x"></i></span></a></li> -->
                  <li><a class="dropdown-item" href="#" onclick="addFormElement('shortText')"><i
                           class="fas fa-grip-lines fa-2x"></i> Rövid szöveg</a></li>
                  <li><a class="dropdown-item" href="#" onclick="addFormElement('longText')"><i
                           class="fas fa-align-justify fa-2x"></i> Hosszú szöveg</a></li>
                  <li><a class="dropdown-item" href="#" onclick="addFormElement('')"><span draggable="false"
                           ondragstart="drag(event)" class="radio toolIcon clickableIcon" name="Feleletválasztós"><i
                              class="fas fa-circle fa-2x"></i></span> Feleletválasztós</a></li>
                  <li><a class="dropdown-item" href="#" onclick="addFormElement('checkbox')"><i
                           class="far fa-check-square fa-2x"></i> Jelölőnégyzet</a>
                  </li>
                  <li><a class="dropdown-item" href="#"><span draggable="false" ondragstart="drag(event)"
                           class="dropdown toolIcon clickableIcon" name="Legördülő lista"><i
                              class="fas fa-arrow-circle-down fa-2x"></i></span> Legördülő lista</a></li>
                  <li><a class="dropdown-item" href="#"><span draggable="false" ondragstart="drag(event)"
                           class="scale toolIcon clickableIcon" name="Lineáris skála"><i
                              class="fas fa-sort-numeric-up fa-2x"></i></span> Lineáris skála</a></li>
               </ul>
            </div>
            <h6>Form - ID:
               <?php echo $_GET['formId'] ?>
            </h6>
            <button class="btn btn-primary" onclick="saveForm()">Mentés</button>
            <button class="btn btn-danger" onclick="showDeleteModal()">Törlés</button>
            </br>
            <label for="cars">Form állapota:</label>
            <select class="form-select form-select-sm" id="formState" name="formState">
               <option value="2">Szerkesztés alatt</option>
               <option value="1">Fogad válaszokat</option>
               <option value="0">Nem fogad válaszokat</option>
            </select>
            </br>
            Csak nem szerkesztés alatt levő form esetén:
            <select class="form-select form-select-sm" id="accessRestrict" name="accessRestrict">
               <option value="private">Privát</option>
               <option value="public">Publikus</option>
            </select>

         </div>
         <div class="col-8" id="editorZone">

         </div>
      </div>
   </div>

   <?php
} else {
   echo "<h3 id='titleBar'>Ehhez a tartalomhoz nincs hozzáférésed.</h3>";
}
?>



<script>

   function showDeleteModal() {
      $('#delete_Modal').modal('show');
   }

   var i = 0;
   document.getElementById("form_name").addEventListener("click", function () {
      $('#Title_Modal').modal('show');
   });

   function save_title() {
      var title = document.getElementById("formTitle").value;
      document.getElementById("form_name").innerHTML = title + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>';
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
            var formName = form.Name;

            //Set form state
            document.getElementById("formState").value = formStatus;

            //Set form access
            document.getElementById("accessRestrict").value = formAccess;

            //Set form Name
            document.getElementById("form_name").innerHTML = formName + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>'

            formContainer = document.getElementById("editorZone");
            //Load form elements
            if (formElements == null) {
               return;
            }
            i = formElements.length;
            for (var pos = 1; pos <= formElements.length; pos++) {
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
               formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings));

            }
         }
      })
   });

   function generateElement(type, id, place, settings) {
      var div = document.createElement("div");
      div.classList.add("form-member");
      div.id = type + "-" + id;
      div.setAttribute('data-position', place);
      div.classList.add("mb-3");

      var uidiv = document.createElement("div");
      uidiv.classList.add("form-control");
      uidiv.id = "e-settings";
      div.appendChild(uidiv);

      var question = document.createElement("input");
      question.type = "text";
      question.placeholder = "Kérdés...";
      question.classList.add("form-control");
      question.for = id;
      if (settings != "" && type != "checkbox") {
         question.value = settings;
      }
      uidiv.appendChild(question);

      console.log("Generating element: " + type);

      switch (type) {
         case "email":
            var input = document.createElement("input");
            input.type = "email";
            input.classList.add("form-control");
            input.id = id;
            input.placeholder = "Írja be az email címét";
            input.disabled = true;
            uidiv.appendChild(input);
            break;
         case "date":
            var input = document.createElement("input");
            input.type = "date";
            input.classList.add("form-control");
            input.id = id;
            input.disabled = true;
            uidiv.appendChild(input);
            break;
         case "shortText":
            var input = document.createElement("input");
            input.type = "text";
            input.classList.add("form-control");
            input.id = id;
            input.disabled = true;
            input.placeholder = "Rövid szöveg";
            uidiv.appendChild(input);
            break;

         case "longText":
            var input = document.createElement("textarea");
            input.classList.add("form-control");
            input.id = id;
            input.disabled = true;
            input.placeholder = "Hosszú szöveg";
            uidiv.appendChild(input);
            break;

         case "checkbox":
            var checkboxHolder = document.createElement("div");
            checkboxHolder.classList.add("form-check");
            for (var i = 0; i < settings.length; i++) {
               checkboxHolder.prepend(listCheckboxOptions(id, settings));
            }
            if (settings == "") {
               checkboxHolder.prepend(listCheckboxOptions(id, ""));
            }
            var addCheckbox = document.createElement("button");
            addCheckbox.classList.add("btn", "btn-success", "btn-sm");
            addCheckbox.innerHTML = "Új opció";
            addCheckbox.onclick = function () {
                  checkboxHolder.prepend(listCheckboxOptions(id, ""));
            };
            checkboxHolder.appendChild(addCheckbox);
            uidiv.appendChild(checkboxHolder);
            break;

      }

      var navdiv = document.createElement("div");
      navdiv.classList.add("element-nav");
      div.appendChild(navdiv);

      var moveUpButton = document.createElement("button");
      moveUpButton.classList.add("btn", "btn-secondary", "btn-sm");
      moveUpButton.innerHTML = "↑";
      moveUpButton.onclick = function () {
         moveUp(type, id);
      };
      navdiv.appendChild(moveUpButton);

      var deleteButton = document.createElement("button");
      deleteButton.classList.add("btn", "btn-danger", "btn-sm");
      deleteButton.innerHTML = "X";
      deleteButton.onclick = function () {
         removeElement(type, id);
      };
      navdiv.appendChild(deleteButton);

      var moveDownButton = document.createElement("button");
      moveDownButton.classList.add("btn", "btn-secondary", "btn-sm");
      moveDownButton.innerHTML = "↓";
      moveDownButton.onclick = function () {
         moveDown(type, id);
      };
      navdiv.appendChild(moveDownButton);


      return div;
   }


   function addFormElement(type) {
      i++;
      console.log("Adding form element: " + type);
      var place = document.getElementById("editorZone").getElementsByClassName("form-member").length + 1;
      console.log("Place: " + place);
      document.getElementById("editorZone").appendChild(generateElement(type, i, place, ""));
   };


   function listCheckboxOptions(id, settings) {
      var div = document.createElement("div");
      div.classList.add("form-check");

      var input = document.createElement("input");
      input.type = "checkbox";
      input.classList.add("form-check-input");
      input.disabled = true;
      input.id = id;
      div.appendChild(input);

      var label = document.createElement("input");
      label.type = "text";
      label.classList.add("form-control");
      label.placeholder = "Opció";
      label.value = settings;
      div.appendChild(label);
      return div;
   }

   function getCheckboxSettings(id) {
      var maindiv = document.getElementById("checkbox-" + id);
      var elementSettings = [];
      var question = maindiv.querySelector("#e-settings").getElementsByTagName("input")[0].value;
      elementSettings.push(question);

      for (var i = 0; i < maindiv.querySelector("#e-settings").getElementsByTagName("div")[0].getElementsByTagName("input").length; i++) {
         elementSettings.push(maindiv.querySelector("#e-settings"));
      }

      console.log("Element settings: " + elementSettings);
      //jsonSettings = JSON.stringify(elementSettings);
      return elementSettings;
   }


   function getElementSettings(type, id) {
      if (type == "checkbox") {
         return getCheckboxSettings(id);
      }
      var maindiv = document.getElementById(type + "-" + id);
      var elementSettings = maindiv.querySelector("#e-settings").getElementsByTagName("input")[0].value;
      console.log("Element settings: " + elementSettings);
      return elementSettings;
   }


   function removeElement(type, id) {
      var element = document.getElementById(type + "-" + id);
      element.remove();
   }

   function moveUp(type, id) {
      var element = document.getElementById(type + "-" + id);
      element.setAttribute('data-position', parseInt(element.getAttribute('data-position')) - 1);
      var prevElement = element.previousElementSibling;
      prevElement.setAttribute('data-position', parseInt(prevElement.getAttribute('data-position')) + 1);
      if (prevElement != null) {
         element.parentNode.insertBefore(element, prevElement);
      }
   }

   function moveDown(type, id) {
      var element = document.getElementById(type + "-" + id);
      element.setAttribute('data-position', parseInt(element.getAttribute('data-position')) + 1);
      var nextElement = element.nextElementSibling;
      nextElement.setAttribute('data-position', parseInt(nextElement.getAttribute('data-position')) - 1);
      if (nextElement != null) {
         element.parentNode.insertBefore(nextElement, element);
      }
   }



   // //Every 10 seconds, save the form
   // setInterval(function(){
   //     //Add growing spinner to the first h6
   //     document.getElementById("time").innerHTML="<div class='spinner-grow text-success' role='status'></div>"
   //     saveForm();
   // }, 10000);


   function saveForm() {
      //Get all elements
      var formEditor = document.getElementById("editorZone");
      var elements = formEditor.getElementsByClassName("form-member");
      var formName = document.getElementById("form_name").innerHTML.split("&nbsp")[0];

      if (formName == "") {
         formName = "Névtelen";
      }

      console.log(elements);
      var formElements = [];
      for (var k = 0; k < elements.length; k++) {
         var elementType = elements[k].id.split("-")[0];
         var elementId = elements[k].id.split("-")[1];
         var elementPlace = elements[k].getAttribute('data-position');
         var elementSettings = getElementSettings(elementType, elementId);


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
      console.log(JSON.parse(formJson));

      var formState = document.getElementById("formState").value;
      var accessRestrict = document.getElementById("accessRestrict").value;
      //Send form to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { form: formJson, formState: formState, accessRestrict: accessRestrict, mode: "save", id: <?php echo $_GET['formId'] ?> },
         success: function (data) {
            console.log(data);

            const toastLiveExample = document.getElementById('save_toast');
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample, { delay: 3000 });

            //If data is 200, append a span with a message after time
            if (data == 200) {
               document.getElementById("save_status").innerHTML = "Sikeres mentés";
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