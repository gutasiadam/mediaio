<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>

<body>

   <div class="container" id="form-container">
      <div class="row form-control" id="form-body">
         <h2 class="rainbow" id="form_name"></h2>
         <h5 id="form_header"></h5>
      </div>

      <div class="row">
      </div>
   </div>

</body>
<script>
   $(document).ready(function () {

      if (<?php if (isset($_GET['success'])) {
         echo "1";
      } else {
         echo "0";
      } ?>) {
         document.getElementById("form_name").innerHTML = "Sikeres leadás!";
         document.getElementById("form_header").innerHTML = "Köszönjük, hogy kitöltötte a kérdőívet!";
      } else {
         //Load form from server
         console.log(<?php echo $_GET['formId'] ?>);
         $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "viewForm", id: <?php echo $_GET['formId'] ?>, userIp: '<?php echo $_SERVER['REMOTE_ADDR'] ?>' },
            success: function (data) {
               console.log(data);
               //if data is 404, redirect to index.php
               if (data == 404) {
                  window.location.href = "index.php?invalidID";
               }
               else if (data == 500) {
                  window.location.href = "index.php?closedForm";
               }
               var form = JSON.parse(data);
               var formElements = JSON.parse(form.Data);
               console.log(formElements);
               var formName = form.Name;
               //Set form Name and header
               document.getElementById("form_name").innerHTML = formName;
               document.getElementById("form_header").innerHTML = form.Header.replace(/\n/g, "<br>");

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
                  console.log(element);

                  var elementType = element.type;
                  var elementId = element.id;
                  var elementPlace = element.place;
                  var elementSettings = element.settings;


                  //Add settings, where possible
                  console.log("Id: " + elementId + " Place:" + elementPlace + " Type: " + elementType + " Settings: " + elementSettings);
                  formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings));

               }

               var submit = document.createElement("button");
               submit.classList.add("btn", "btn-lg", "btn-success");
               submit.innerHTML = "Leadás";
               submit.onclick = function () { submitAnswer() };
               formContainer.appendChild(submit);

            }
         })
      };
   });



   function generateElement(type, id, place, settings) {
      if (settings != "") {
         var questionSetting = JSON.parse(settings).question;
         var isRequired = JSON.parse(settings).required;
         var CheckOptions = JSON.parse(settings).options;
      }
      var div = document.createElement("div");
      div.id = type + "-" + id;
      div.setAttribute('data-position', place);
      div.classList.add("mb-3", "question");

      var question = document.createElement("label");
      question.for = id;
      question.innerHTML = "Kérdés";
      question.innerHTML = questionSetting;
      div.appendChild(question);

      console.log("Generating element: " + type);

      switch (type) {
         case "email":
            var input = document.createElement("input");
            input.type = "email";
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.placeholder = "Írja be az email címét";
            div.appendChild(input);
            break;
         case "date":
            var input = document.createElement("input");
            input.type = "date";
            input.classList.add("form-control", "userInput");
            input.id = id;
            div.appendChild(input);
            break;
         case "shortText":
            var input = document.createElement("input");
            input.type = "text";
            input.classList.add("form-control", "userInput");
            input.id = id;
            input.placeholder = "Rövid szöveg";
            div.appendChild(input);
            break;

         case "longText":
            var input = document.createElement("textarea");
            input.classList.add("form-control", "userInput");
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
               for (var i = 0; i < CheckOptions.length; i++) {
                  radioHolder.append(listCheckOpt("radio", id, CheckOptions[i], i));
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
               for (var i = 0; i < CheckOptions.length; i++) {
                  checkboxHolder.append(listCheckOpt("checkbox", id, CheckOptions[i], i));
               }
            }
            div.appendChild(checkboxHolder);
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

   function listCheckOpt(type, id, settings, optionNum) {
      var div = document.createElement("div");
      div.classList.add("form-check");
      div.setAttribute('data-option', optionNum);

      var input = document.createElement("input");
      input.type = type;
      input.classList.add("form-check-input", "userInput");
      if (type == "radio") {
         input.name = "flexRadioDefault";
      }
      input.id = id;
      input.setAttribute('data-name', settings);
      div.appendChild(input);

      var label = document.createElement("label");
      label.classList.add("form-check-label");
      label.for = id;
      label.innerHTML = settings;
      div.appendChild(label);

      return div;
   }

   function submitAnswer() {
      var form = document.getElementById("form-body");

      var elements = form.getElementsByClassName("question");

      console.log(elements);
      var answers = [];
      for (var i = 0; i < elements.length; i++) {
         var element = elements[i];

         var elementType = element.id.split("-")[0];
         var inputs = element.getElementsByClassName("userInput");

         var value = [];
         if (elementType == "radio" || elementType == "checkbox") {
            for (var j = 0; j < inputs.length; j++) {
               value.push(inputs[j].checked);
               value.push(inputs[j].getAttribute("data-name"));
            }
         } else {
            value = inputs[0].value;
         }

         value = JSON.stringify(value);

         var answer = {
            id: element.id,
            value: value
         }
         answers.push(answer);
         console.log(answer);
      }
      //console.log(answers);
      //console.log(JSON.stringify(answers));
      var uid = <?php if ($_SESSION['userId'] != null) {
         echo $_SESSION['userId'];
      } else {
         echo "0";
      } ?>;
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "submitAnswer", uid: uid, userIp: '<?php echo $_SERVER['REMOTE_ADDR'] ?>', id: <?php echo $_GET['formId'] ?>, answers: JSON.stringify(answers) },
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

</script>