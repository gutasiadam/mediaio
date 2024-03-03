<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>

<body>

   <div class="container" id="form-container">
      <form class="row form-control" id="form-body">
         <h2 class="rainbow" id="form_name"></h2>
         <h5 id="form_header"></h5>
      </form>

      <div class="row">
      </div>
   </div>

</body>
<script>
   let isAnonim = 0;
   <?php if (isset($_GET['success'])) { ?>
      $(document).ready(function () {
         //Check if form is closed

         //Set form Name and header if form is closed
         document.getElementById("form_name").innerHTML = "Sikeres leadás!";
         document.getElementById("form_header").innerHTML = "Köszönjük, hogy kitöltötte a kérdőívet!";

         var formContainer = document.getElementById("form-body");

         <?php if ($_SESSION['userId'] != null && in_array("admin", $_SESSION["groups"])) { ?>
            //Add view answers button
            var viewAnswers = document.createElement("button");
            viewAnswers.classList.add("btn", "btn-lg", "btn-success");
            viewAnswers.innerHTML = "Válaszok megtekintése";
            viewAnswers.onclick = function () {
               event.preventDefault();
               window.location.href = "formanswers.php?formId=<?php echo $_GET['formId'] ?>";
            }
            formContainer.appendChild(viewAnswers);
         <?php } ?>
      });


   <?php } else { ?>
      $(document).ready(function () {

         //Load form from server
         //console.log(<?php echo $_GET['formId'] ?>);
         $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "viewForm", id: <?php echo $_GET['formId'] ?>},
            success: function (data) {
               
               //if data is 404, redirect to index.php
               if (data == 404) {
                  window.location.href = "index.php?invalidID";
               }
               else if (data == 500) {
                  window.location.href = "index.php?closedForm";
               }
               var form = JSON.parse(data);
               var formElements = JSON.parse(form.Data);
               var formName = form.Name;
               isAnonim = form.Anonim;
               //Set form Name and header
               document.getElementById("form_name").innerHTML = formName;
               document.getElementById("form_header").innerHTML = form.Header.replace(/\n/g, "<br>");

               <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
                  var editForm = document.createElement("button");
                  editForm.classList.add("btn");
                  editForm.innerHTML = '<i class="fas fa-edit fa-2x" style="color: #747b86"></i>';
                  editForm.onclick = function () {
                     window.location.href = "formeditor.php?formId=<?php echo $_GET['formId'] ?>";
                  }
                  document.getElementById("form_name").appendChild(editForm);

               <?php } ?>

               //Where form items are stored
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


                  //Add settings, where possible
                  //console.log("Id: " + elementId + " Place:" + elementPlace + " Type: " + elementType + " Settings: " + elementSettings);
                  formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings));

               }

               //Add submit button
               var submit = document.createElement("button");
               submit.classList.add("btn", "btn-lg", "btn-success");
               submit.type = "submit";
               submit.innerHTML = "Leadás";
               formContainer.appendChild(submit);

            }
         });
      });
   <?php } ?>



   <?php if (!isset($_GET['success'])) { ?>
      //Generate form elements (view mode)
      function generateElement(type, id, place, settings) {
         if (settings != "") {
            //Parse settings
            var questionSetting = JSON.parse(settings).question;
            var isRequired = JSON.parse(settings).required;
            var CheckOptions = JSON.parse(settings).options;
         }
         //Create div, which will hold the form element
         var div = document.createElement("div");
         div.id = type + "-" + id;
         div.setAttribute('data-position', place); //Set position
         if (isRequired) {
            div.setAttribute('data-required', "true");
         } else {
            div.setAttribute('data-required', "false");
         }
         div.classList.add("mb-3", "question");

         //Create question label
         var question = document.createElement("label");
         question.for = id;
         console.log("Required: " + isRequired);
         if (isRequired) {
            question.innerHTML = questionSetting + "<span style='color: red;'> *</span>";
         } else {
            question.innerHTML = questionSetting;
         }
         //question.innerHTML = questionSetting;
         div.appendChild(question);

         console.log("Generating element: " + type);

         //Create form type
         switch (type) {
            case "email":
               //Generate email input
               var input = document.createElement("input");
               input.type = "email";
               input.classList.add("form-control", "userInput");
               input.id = id;
               input.placeholder = "Írja be az email címét";
               if (isRequired) {
                  input.required = true;
               }
               div.appendChild(input);
               break;
            case "date":
               //Generate date input
               var input = document.createElement("input");
               input.type = "date";
               input.classList.add("form-control", "userInput");
               input.id = id;
               if (isRequired) {
                  input.required = true;
               }
               div.appendChild(input);
               break;
            case "time":
               //Generate time input
               var input = document.createElement("input");
               input.type = "time";
               input.classList.add("form-control", "userInput");
               input.id = id;
               if (isRequired) {
                  input.required = true;
               }
               div.appendChild(input);
               break;

            case "shortText":
               //Generate short text input
               var input = document.createElement("input");
               input.type = "text";
               input.classList.add("form-control", "userInput");
               input.id = id;
               if (isRequired) {
                  input.required = true;
               }
               input.placeholder = "Rövid szöveg";
               div.appendChild(input);
               break;

            case "longText":
               //Generate long text input
               var input = document.createElement("textarea");
               input.classList.add("form-control", "userInput");
               input.id = id;
               input.placeholder = "Hosszú szöveg";
               if (isRequired) {
                  input.required = true;
               }
               div.appendChild(input);
               break;

            case "radio":
               //Generate radio buttons
               var radioHolder = document.createElement("div");
               radioHolder.classList.add("radio-holder");
               if (settings == "") {
                  radioHolder.append(listCheckOpt("radio", id, "", 0)); //Add empty radio button
               } else {
                  for (var i = 0; i < CheckOptions.length; i++) {
                     //Add radio buttons
                     radioHolder.append(listCheckOpt("radio", id, CheckOptions[i], i));
                  }
               }
               div.appendChild(radioHolder);
               break;

            case "checkbox":
               //Generate checkboxes
               var checkboxHolder = document.createElement("div");
               checkboxHolder.classList.add("checkbox-holder");
               if (settings == "") {
                  checkboxHolder.append(listCheckOpt("checkbox", id, "", 0));
               } else {
                  for (var i = 0; i < CheckOptions.length; i++) {
                     checkboxHolder.append(listCheckOpt("checkbox", id, CheckOptions[i], i));
                  }
               }
               div.appendChild(checkboxHolder);
               break;

            case "dropdown":
               //Generate dropdown
               //Create dropdown holder
               var dropdownHolder = document.createElement("div");
               dropdownHolder.classList.add("dropdown-holder");

               //Create dropdown
               var select = document.createElement("select");
               select.classList.add("form-select", "userInput");
               select.id = id;

               if (settings == "") {
                  select.append(listDropdown(id, ""));
               } else {
                  for (var i = 0; i < CheckOptions.length; i++) {
                     select.append(listDropdown(id, CheckOptions[i]));
                  }
               }
               dropdownHolder.appendChild(select);
               div.appendChild(dropdownHolder);
               break;

            case "fileUpload":
               //Generate file upload
               var input = document.createElement("input");
               input.type = "file";
               input.classList.add("form-control", "userInput");
               input.id = id;
               div.appendChild(input);
               break;
         }
         return div;
      }

      //Generate dropdown options
      function listDropdown(id, settings) {
         var option = document.createElement("option");
         option.value = settings;
         option.innerHTML = settings;

         return option;
      }

      //Generate radio and checkbox options
      function listCheckOpt(type, id, settings, optionNum) {

         //Create div to hold input and label
         var div = document.createElement("div");
         div.classList.add("form-check");
         div.setAttribute('data-option', optionNum);

         //Create input
         var input = document.createElement("input");
         input.type = type;
         input.classList.add("form-check-input", "userInput");
         if (type == "radio") {
            input.name = "flexRadioDefault";
         }
         input.id = id;
         input.setAttribute('data-name', settings);
         div.appendChild(input);

         //Create label
         var label = document.createElement("label");
         label.classList.add("form-check-label");
         label.for = id;
         label.innerHTML = settings;
         div.appendChild(label);

         return div;
      }

      var form = document.getElementById("form-body");

      form.addEventListener("submit", function (event) {
         event.preventDefault();
         submitAnswer();
      });

      //Submit form
      async function submitAnswer() {
         var form = document.getElementById("form-body"); //Get form container

         var elements = form.getElementsByClassName("question"); //Get all form elements

         //console.log(elements);
         var answers = [];
         for (var i = 0; i < elements.length; i++) {
            //Loop through all form elements
            var element = elements[i];

            //Check if element is required
            var isRequired = element.getAttribute("data-required");
            if (isRequired == "true") {
               var inputs = element.getElementsByClassName("userInput");
               if (inputs[0].value == "") {
                  alert("Kérlek töltsd ki az összes kötelező mezőt!");
                  return;
               }
            }
            var elementType = element.id.split("-")[0];
            var inputs = element.getElementsByClassName("userInput");

            var value = [];
            //Get value of form element
            if (elementType == "radio" || elementType == "checkbox") {
               for (var j = 0; j < inputs.length; j++) {
                  var checked = 0;
                  if (inputs[j].checked) {
                     checked = 1
                  }
                  var input = inputs[j].getAttribute("data-name") + ":" + checked;
                  value.push(input);
               }
            }
            else {
               value = inputs[0].value;
            }

            value = JSON.stringify(value);

            var answer = {
               id: element.id,
               value: value
            }
            answers.push(answer);
            //console.log(answer);
         }

         //Send answers to server
         answers = JSON.stringify(answers).replace(/"/g, '\\"');

         //Set UID to 0 if user is not logged in
         var uid;
         var userIp;
         if (isAnonim == 0) {
            uid = <?php if ($_SESSION['userId'] != null) {
               echo $_SESSION['userId'];
            } else {
               echo "0";
            } ?>;
            userIp = await getIp();
            console.log("User: " + userIp);
         } else {
            console.log("Anonim");
            uid = 0;
            userIp = '0.0.0.0';
         }
         $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "submitAnswer", uid: uid, userIp: userIp, id: <?php echo $_GET['formId'] ?>, answers: answers },
            success: function (data) {
               console.log(data);
               if (data == 500) {
                  alert("Nem megengedett karakterek a válaszban!");
               } else if (data == 200) {
                  window.location.href = "viewform.php?formId=<?php echo $_GET['formId'] ?>&success";
               } else {
                  alert("Sikertelen leadás");
               }
            }
         })
      }



      function getIp() {
         return new Promise((resolve, reject) => {
            $.get('https://api.db-ip.com/v2/free/self', function (data) {
               resolve(data.ipAddress);
            }).fail(function () {
               console.log('Error occurred');
               resolve('<?php echo $_SERVER['REMOTE_ADDR'] ?>');
            });
         });
      }

   <?php } ?>

</script>