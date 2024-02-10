<?php
session_start();
if (!isset($_SESSION['userId'])) {
   header("Location: ../index.php?error=AccessViolation");
   exit();
}
include("header.php");
include("../translation.php");

?>
<html>

<?php if (in_array("admin", $_SESSION["groups"])) { ?>

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

   <body>
      <div class="form">
         <h2 class="rainbow" id="form_name"></h2>
         <div class="row" id="form-option-buttons">
            <div class="dropdown" id="tools">
               <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  Válaszok
               </button>
               <ul class="dropdown-menu" id="answers_dropdown">
               </ul>
            </div>
            <button class="btn btn-secondary" onclick="showFormEdit(<?php echo $_GET['formId'] ?>)"><i
                  class='fas fa-highlighter fa-lg'></i> Szerkesztés</button>
            <button class="btn" onclick="showSettingsModal()"><i class="fas fa-sliders-h fa-lg"></i></button>
         </div>
         <div class="container" id="form-body">

         </div>
      </div>
   </body>

<?php } ?>
<script>
   var answers;
   var formElements;

   $(document).ready(function () {
      //Load form from server
      console.log(<?php echo $_GET['formId'] ?>);
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "getFormAnswers", id: <?php echo $_GET['formId'] ?> },
         success: function (data) {
            console.log(data);
            //if data is 404, redirect to index.php
            if (data == 404) {
               window.location.href = "index.php?invalidID";
            }

            answers = JSON.parse(data);
            console.log(answers);
            var dropdown = document.getElementById("answers_dropdown");

            for (var i = 0; i < answers.length; i++) {
               var answer = answers[i];
               var li = document.createElement("li");
               var a = document.createElement("a");
               a.innerHTML = (i + 1) + ". válasz";
               a.onclick = function () { showFormAnswers(answer.ID) };
               li.appendChild(a);
               dropdown.appendChild(li);
            }
         }
      });

      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "getForm", id: <?php echo $_GET['formId'] ?> },
         success: function (data) {
            console.log(data);
            //if data is 404, redirect to index.php
            if (data == 404) {
               window.location.href = "index.php?invalidID";
            }
            var form = JSON.parse(data);
            formElements = JSON.parse(form.Data);
            console.log(formElements);
            var formName = form.Name;
            //Set form Name and header
            document.getElementById("form_name").innerHTML = formName;

            formContainer = document.getElementById("form-body");

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
         }
      });

   });

   function showFormAnswers(id) {

      var formContainer = document.getElementById("form-body");
      //Load form elements
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
         formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, id));

      }
   }

   function showFormEdit(id) {
      window.location.href = "formeditor.php?formId=" + <?php echo $_GET['formId'] ?>;
   }

   function generateElement(type, id, place, settings, answerId) {
      var div = document.createElement("div");
      div.id = type + "-" + id;
      div.setAttribute('data-position', place);
      div.classList.add("mb-3");

      var question = document.createElement("label");
      question.for = id;
      question.innerHTML = "Kérdés";
      if (settings != "") {
         question.innerHTML = settings;
         if (type == "checkbox" || type == "radio") {
            question.innerHTML = JSON.parse(settings).name;
         }
      }
      div.appendChild(question);

      console.log("Generating element: " + type);

      switch (type) {
         case "email":
            var input = document.createElement("label");
            input.type = "email";
            input.classList.add("form-control");
            input.id = id;
            input.innerHTML = answers[id].answer;
            div.appendChild(input);
            break;
         case "date":
            var input = document.createElement("label");
            input.type = "date";
            input.classList.add("form-control");
            input.id = id;
            div.appendChild(input);
            break;
         case "shortText":
            var input = document.createElement("label");
            input.type = "text";
            input.classList.add("form-control");
            input.id = id;
            input.placeholder = "Rövid szöveg";
            div.appendChild(input);
            break;

         case "longText":
            var input = document.createElement("textarea");
            input.classList.add("form-control");
            input.id = id;
            input.placeholder = "Hosszú szöveg";
            div.appendChild(input);
            break;

         case "radio":
            var radioHolder = document.createElement("div");
            radioHolder.classList.add("radio-holder");
            if (settings == "") {
               radioHolder.append(listCheckOpt("radio", id, "", 0));
            } else {
               for (var i = 0; i < JSON.parse(settings).options.length; i++) {
                  radioHolder.append(listCheckOpt("radio", id, JSON.parse(settings).options[i], i));
               }
            }
            div.appendChild(radioHolder);
            break;

         case "checkbox":
            var checkboxHolder = document.createElement("div");
            checkboxHolder.classList.add("checkbox-holder");
            if (settings == "") {
               checkboxHolder.append(listCheckOpt("checkbox", id, "", 0));
            } else {
               for (var i = 0; i < JSON.parse(settings).options.length; i++) {
                  checkboxHolder.append(listCheckOpt("checkbox", id, JSON.parse(settings).options[i], i));
               }
            }
            div.appendChild(checkboxHolder);
            break;

         case "fileUpload":
            var input = document.createElement("input");
            input.type = "file";
            input.classList.add("form-control");
            input.id = id;
            div.appendChild(input);
            break;
      }
      return div;
   }

</script>

</html>