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
      <div class="form" id="doboz">
         <h2 class="rainbow" id="form_name"></h2>
         <div class="row" id="form-option-buttons">
            <div class="dropdown" id="tools">
               <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-align-left"></i>
               </button>
               <ul class="dropdown-menu" id="answers_dropdown">
               </ul>
            </div>
            <button class="btn btn-success" onclick="showTable()"><i class="fas fa-table fa-lg"></i></button>
            <button class="btn" onclick="showFormEdit(<?php echo $_GET['formId'] ?>)"><i
                  class='fas fa-highlighter fa-lg'></i></button>
            <button class="btn" onclick="viewForm(<?php echo $_GET['formId'] ?>)"><i class="fas fa-eye"></i></button>

         </div>
         <div class="justify-content-center">
            <table class="table" id="answersTable" style="display: none">
               <thead>
                  <tr id="headerHolder">

                  </tr>
               </thead>
               <tbody class="table-group-devider" id="answerHolder">

               </tbody>
            </table>
            <form class="row form-control" id="form-body" style="display: none">

            </form>
            <br>
         </div>
      </div>
   </body>

<?php } ?>
<script>
   var UserAnswers = [];
   var formElements;

   $(document).ready(function () {
      //Load form from server
      console.log(<?php echo $_GET['formId'] ?>);
      loadPage();
   });

   async function loadPage() {
      await fetchData();
      await fetchAnswers();
      showTable();
   }


   async function fetchData() {
      console.log("Fetching form data");
      return new Promise(async (resolve, reject) => {
         try {
            const response = await $.ajax({
               type: "POST",
               url: "../formManager.php",
               data: { mode: "getForm", id: <?php echo $_GET['formId'] ?> }
            });

            if (response == 404) {
               window.location.href = "index.php?invalidID";
            }

            var form = JSON.parse(response);
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

            resolve();
         } catch (error) {
            console.error("Error:", error);
            reject(error);
         }
      });
   }

   async function fetchAnswers() {
      console.log("Fetching form answers");
      return new Promise(async (resolve, reject) => {
         try {
            const response = await $.ajax({
               type: "POST",
               url: "../formManager.php",
               data: { mode: "getFormAnswers", id: <?php echo $_GET['formId'] ?> }
            });


            if (response == 404) {
               window.location.href = "index.php?invalidID";
            }

            var answers = JSON.parse(response);
            console.log("Answers:" + answers);
            var dropdown = document.getElementById("answers_dropdown");

            for (var i = 0; i < answers.length; i++) {
               UserAnswers.push(answers[i]);
               var id = answers[i].ID;

               var li = document.createElement("li");
               li.classList.add("dropdown-item");
               li.style.cursor = "pointer";

               li.onclick = (function (id) {
                  return function () {
                     showFormAnswers(id);
                  };
               })(id);

               li.innerHTML = (i + 1) + ". válasz</a>";
               dropdown.appendChild(li);
            }
            resolve();
         } catch (error) {
            console.error("Error:", error);
            reject(error);
         }
      });
   }

   function setButtonClass(name) {
      var buttons = document.getElementById("form-option-buttons").getElementsByTagName("button");
      for (var i = 0; i < buttons.length; i++) {
         buttons[i].classList.remove("btn-success");
      }

      switch (name) {
         case "singleAnswer":
            buttons[0].classList.add("btn-success");
            break;
         case "table":
            buttons[1].classList.add("btn-success");
            break;
      }
   }

   //Function to view form
   function viewForm(formId) {
      window.location.href = "viewform.php?formId=" + formId;
   }

   function showFormAnswers(id) {

      //Set button class
      setButtonClass("singleAnswer");

      //Set doboz max-width
      var doboz = document.getElementById("doboz");
      doboz.style.maxWidth = "800px";

      //Set table invisible
      var table = document.getElementById("answersTable");
      table.style.display = "none";

      console.log("Showing form answers: " + id);
      var UserSubmission;
      for (var i = 0; i < UserAnswers.length; i++) {
         if (UserAnswers[i].ID == id) {
            UserSubmission = UserAnswers[i];
         }
      }

      var AnswerData = JSON.parse(UserSubmission.UserAnswers);
      //console.log(AnswerData);

      var formContainer = document.getElementById("form-body");
      formContainer.innerHTML = "";
      //Load form elements
      for (var pos = 1; pos <= formElements.length; pos++) {
         for (var j = 0; j < formElements.length; j++) {
            if (formElements[j].place == pos) {
               var element = formElements[j];
            }
         }
         //console.log(element);

         var elementType = element.type;
         var elementId = element.id;
         var elementPlace = element.place;
         var elementSettings = element.settings;
         var elementAnswer;

         for (var i = 0; i < AnswerData.length; i++) {
            if (AnswerData[i].id == (elementType + "-" + elementId)) {
               elementAnswer = AnswerData[i].value;
            }
         }

         //console.log("Element answer: " + elementAnswer);
         //Add settings, where possible
         //console.log("Id: " + elementId + " Place:" + elementPlace + " Type: " + elementType + " Settings: " + elementSettings);
         formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, elementAnswer));

      }

      //Set form visible
      formContainer.style.display = "block";
   }

   function showFormEdit(id) {
      window.location.href = "formeditor.php?formId=" + <?php echo $_GET['formId'] ?>;
   }

   function generateElement(type, id, place, settings, answer) {
      answer = JSON.parse(answer);


      if (settings != "") {
         var questionSetting = JSON.parse(settings).question;
         var isRequired = JSON.parse(settings).required;
         var CheckOptions = JSON.parse(settings).options;
      }
      var div = document.createElement("div");
      div.id = type + "-" + id;
      div.setAttribute('data-position', place);
      if (isRequired) {
         div.setAttribute('data-required', "true");
      } else {
         div.setAttribute('data-required', "false");
      }
      div.classList.add("mb-3", "question");

      var question = document.createElement("label");
      question.for = id;

      if (isRequired) {
         question.innerHTML = questionSetting + "<span style='color: red;'> *</span>";
      } else {
         question.innerHTML = questionSetting;
      }
      //question.innerHTML = questionSetting;
      div.appendChild(question);



      switch (type) {
         case "email":
            var input = document.createElement("input");
            input.type = "email";
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.value = answer;
            input.disabled = true;
            div.appendChild(input);
            break;
         case "date":
            var input = document.createElement("input");
            input.type = "date";
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.value = answer;
            input.disabled = true;
            div.appendChild(input);
            break;
         case "time":
            var input = document.createElement("input");
            input.type = "time";
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.value = answer;
            input.disabled = true;
            div.appendChild(input);
            break;

         case "shortText":
            var input = document.createElement("input");
            input.type = "text";
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.value = answer;
            input.disabled = true;
            input.placeholder = "Rövid szöveg";
            div.appendChild(input);
            break;

         case "longText":
            var input = document.createElement("textarea");
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.value = answer;
            input.disabled = true;
            input.placeholder = "Hosszú szöveg";
            div.appendChild(input);
            break;

         case "radio":
            var radioHolder = document.createElement("div");
            radioHolder.classList.add("radio-holder");
            if (settings == "") {
               radioHolder.append(listCheckOpt("radio", id, "", 0, false));
            } else {
               for (var i = 0; i < CheckOptions.length; i++) {
                  radioHolder.append(listCheckOpt("radio", id, CheckOptions[i], i, answer[i]));
               }
            }
            div.appendChild(radioHolder);
            break;

         case "checkbox":
            var checkboxHolder = document.createElement("div");
            checkboxHolder.classList.add("checkbox-holder");
            if (settings == "") {
               checkboxHolder.append(listCheckOpt("checkbox", id, "", 0), false);
            } else {
               for (var i = 0; i < CheckOptions.length; i++) {
                  checkboxHolder.append(listCheckOpt("checkbox", id, CheckOptions[i], i, answer[i]));
               }
            }
            div.appendChild(checkboxHolder);
            break;

         case "dropdown":
            var dropdownHolder = document.createElement("div");
            dropdownHolder.classList.add("dropdown-holder");

            var select = document.createElement("select");
            select.classList.add("form-select", "userInput");
            select.id = id;
            select.disabled = true;

            var option = document.createElement("option");
            option.innerHTML = answer;
            select.appendChild(option);

            dropdownHolder.appendChild(select);
            div.appendChild(dropdownHolder);
            break;

         case "fileUpload":
            var input = document.createElement("input");
            input.type = "file";
            input.classList.add("form-control", "userInput");
            input.id = id;
            div.appendChild(input);
            break;
      }
      return div;
   }


   function listCheckOpt(type, id, settings, optionNum, answer) {
      //console.log("Answer: " + answer);
      var labelname = answer.split(":")[0];
      var checked = Boolean(Number(answer.split(":")[1]));

      //console.log("Labelname: " + labelname + " Checked: " + checked);

      var div = document.createElement("div");
      div.classList.add("form-check");
      div.setAttribute('data-option', optionNum);

      var input = document.createElement("input");
      input.type = type;
      input.disabled = true;
      input.classList.add("form-check-input", "userInput");
      if (type == "radio") {
         input.name = "flexRadioDefault";
      }
      input.id = id;
      input.checked = checked;
      input.setAttribute('data-name', settings);
      div.appendChild(input);

      var label = document.createElement("label");
      label.classList.add("form-check-label");
      label.for = id;
      label.innerHTML = labelname;
      div.appendChild(label);

      return div;
   }

   function showTable() {

      setButtonClass("table");

      //Set doboz max-width
      var doboz = document.getElementById("doboz");
      doboz.style.maxWidth = "1200px";

      //Empty table
      var headerHolder = document.getElementById("headerHolder");
      headerHolder.innerHTML = "";

      var answerHolder = document.getElementById("answerHolder");
      answerHolder.innerHTML = "";

      // Set form invisible
      var formContainer = document.getElementById("form-body");
      formContainer.style.display = "none";

      // Generate table header
      var idTh = document.createElement("th");
      idTh.innerHTML = "ID";
      idTh.scope = "col";
      headerHolder.appendChild(idTh);

      for (var i = 0; i < formElements.length; i++) {
         var th = document.createElement("th");

         //Getting question name
         var question = JSON.parse(formElements[i].settings).question;

         th.innerHTML = question;
         th.scope = "col";
         headerHolder.appendChild(th);
      }

      // Generate table body

      function createRow(UserAnswers, formElements) {
         var tr = document.createElement("tr");
         var AnswerData = JSON.parse(UserAnswers.UserAnswers);

         var idTd = document.createElement("td");
         idTd.innerHTML = UserAnswers.ID;
         tr.appendChild(idTd);

         for (var j = 0; j < formElements.length; j++) {
            var td = document.createElement("td");
            var elementAnswer = getElementAnswer(formElements[j], AnswerData);
            td.innerHTML = elementAnswer;
            tr.appendChild(td);
         }
         return tr;
      }

      function getElementAnswer(element, AnswerData) {
         var elementType = element.type;
         var elementId = element.id;
         var elementAnswer;

         for (var k = 0; k < AnswerData.length; k++) {
            if (AnswerData[k].id == (elementType + "-" + elementId)) {
               if (elementType == "checkbox" || elementType == "radio") {
                  elementAnswer = getCheckedAnswer(AnswerData[k].value);
                  if (elementAnswer == undefined) {
                     elementAnswer = "<i>Nincs kiválasztva</i>";
                  }
               } else {
                  elementAnswer = AnswerData[k].value;
               }
            }
         }
         return elementAnswer;
      }

      function getCheckedAnswer(value) {
         var answer = JSON.parse(value);
         var elementAnswer;

         for (var l = 0; l < answer.length; l++) {
            var answerOption = answer[l].split(":")[0];
            var checked = Boolean(Number(answer[l].split(":")[1]));
            if (checked) {
               if (elementAnswer == undefined) {
                  elementAnswer = answerOption;
               } else {
                  elementAnswer = elementAnswer + ", " + answerOption;
               }
            }
         }
         return elementAnswer;
      }

      for (var i = 0; i < UserAnswers.length; i++) {
         var tr = createRow(UserAnswers[i], formElements);
         answerHolder.appendChild(tr);
      }

      // Set table visible
      var table = document.getElementById("answersTable");
      table.style.display = "table";

   }

</script>

</html>