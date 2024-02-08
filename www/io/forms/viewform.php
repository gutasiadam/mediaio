<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>

<h2 class="rainbow" id="form_name"></h2>
<div class="container" id="form-container">
   <div class="row form-control" id="form-body">

   </div>

   <div class="row">
      <div class="col" id="submit">
         <button class='btn btn-lg btn-success' type='submit' name='submit' onclick="submitAnswer()">Leadás</button>
      </div>
   </div>
</div>


<script>
   $(document).ready(function () {
      //Load form from server
      console.log(<?php echo $_GET['formId'] ?>);
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "viewForm", id: <?php echo $_GET['formId'] ?> },
         success: function (data) {
            console.log(data);
            //if data is 404, redirect to index.php
            if (data == 404) {
               window.location.href = "index.php?invalidID";
            }
            var form = JSON.parse(data);
            var formElements = JSON.parse(form.Data);
            console.log(formElements);
            var formName = form.Name;
            //Set form Name
            document.getElementById("form_name").innerHTML = formName;

            formContainer = document.getElementById("form-body");
            //Load form elements
            for (var j = 0; j < formElements.length; j++) {
               var element = formElements[j];
               console.log(element);
               var elementType = element.type;
               var elementId = element.id;
               var elementSettings = element.settings;



               //Add settings, where possible
               console.log("Id: " + elementId + " Type: " + elementType + " Settings: " + elementSettings);
               formContainer.appendChild(generateElement(elementType, elementId, elementSettings));

            }
         }
      })
   });



   function generateElement(type, id, settings) {
      var div = document.createElement("div");
      div.id = type + "-" + id;
      div.classList.add("mb-3");

      var question = document.createElement("label");
      question.for = id;
      if (settings == "") {
         question.innerHTML = "Kérdés";
      } else {
         question.innerHTML = settings;
      }
      div.appendChild(question);

      console.log("Generating element: " + type);

      switch (type) {
         case "email":
            var input = document.createElement("input");
            input.type = "email";
            input.classList.add("form-control");
            input.id = id;
            input.placeholder = "Írja be az email címét";
            div.appendChild(input);
            break;
         case "date":
            var input = document.createElement("input");
            input.type = "date";
            input.classList.add("form-control");
            input.id = id;
            div.appendChild(input);
            break;
         case "shortText":
            var input = document.createElement("input");
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

         case "checkbox":
            var input = document.createElement("input");
            input.type = "checkbox";
            input.classList.add("form-check-input");
            input.id = id;
            div.appendChild(input);
            break;
      }
      return div;
   }


   function addFormElement(type) {
      i++;
      console.log("Adding form element: " + type);
      document.getElementById("editorZone").appendChild(generateElement(type, i, ""));
   };

   function submitAnswer() {
      var form = document.getElementById("form-body");
      var elements = form.getElementsByTagName("input");
      var answers = [];
      for (var i = 0; i < elements.length; i++) {
         var element = elements[i];
         var answer = {
            id: element.id,
            value: element.value
         }
         answers.push(answer);
      }
      console.log(answers);
      console.log(JSON.stringify(answers));
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "submitAnswer", uid: 0, id: <?php echo $_GET['formId'] ?>, answers: JSON.stringify(answers) },
         success: function (data) {
            /* console.log(data); */
            if (data == 200) {
               window.location.href = "viewform.php";
            } else {
               alert("Sikertelen leadás");
            }
         }
      })
   }

</script>