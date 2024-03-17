<?php
session_start();
include ("header.php");
include ("../translation.php"); ?>
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

<script src="backend/elementGenerator.js" type="text/javascript"></script>
<script src="backend/formSubmission.js" type="text/javascript"></script>
<script>
   let isAnonim = 0;
   <?php if (isset ($_GET['success'])) { ?>
      $(document).ready(function () {
         //Check if form is closed

         //Set form Name and header if form is closed
         document.getElementById("form_name").innerHTML = "Sikeres leadás!";
         document.getElementById("form_header").innerHTML = "Köszönjük, hogy kitöltötte a kérdőívet!";

         var formContainer = document.getElementById("form-body");

         $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "getForm", id: <?php echo $_GET['formId'] ?> },
            success: function (data) {

               //if data is 404, redirect to index.php
               if (data == 404) {
                  window.location.href = "index.php?invalidID";
               }
               var form = JSON.parse(data);

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


   <?php } else if (isset ($_GET['formId']) || isset ($_GET['form'])) { ?>

         var formId = <?php if (isset ($_GET['formId'])) {
            echo $_GET['formId'];
         } ?>;
         $(document).ready(function () {

            //Load form from server
            $.ajax({
               type: "POST",
               url: "../formManager.php",
               data: { mode: "getForm", id: <?php echo $_GET['formId'] ?>, formHash: "<?php echo $_GET['form'] ?>" },
               success: function (data) {

                  //if data is 404, redirect to index.php
                  if (data == 404) {
                     window.location.href = "index.php?invalidID";
                  }

                  var form = JSON.parse(data);
                  var formElements = JSON.parse(form.Data);
                  var formName = form.Name;
                  isAnonim = form.Anonim;
                  //Set form Name and header
                  document.getElementById("form_name").innerHTML = formName;
                  document.getElementById("form_header").innerHTML = form.Header.replace(/\n/g, "<br>");

               <?php if (isset ($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
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
                     formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, "fill"));

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



   <?php if (!isset ($_GET['success'])) { ?>

      var form = document.getElementById("form-body");

      form.addEventListener("submit", function (event) {
         event.preventDefault();
         submitAnswer(formId, isAnonim);
      });

      function getUid() {
         return new Promise((resolve, reject) => {
            var uid = "<?php echo $_SESSION['userId'] ?>";
            if (uid == 0 || uid == null) {
               resolve(0);
            } else {
               resolve(uid);
            }
         });
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