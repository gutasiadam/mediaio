<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>

<h2 class="rainbow" id="form_name"></h2>
<div class="container" id="form-body">

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

               if (elementType == "email") {
                  console.log("Ez egy email");
                  var emildiv = document.createElement("div");
                  emildiv.classList.add("mb-3");

                  var label = document.createElement("label");
                  if (elementSettings == "") {
                     label.innerHTML = "Email cím:";
                  } else {
                     label.innerHTML = elementSettings;
                  }
                  label.for = elementId;
                  emildiv.appendChild(label);

                  var input = document.createElement("input");
                  input.type = "email";
                  input.classList.add("form-control");
                  input.id = elementId;
                  input.placeholder = "name@example.com";
                  emildiv.appendChild(input);
                  console.log(emildiv);

                  formContainer.appendChild(emildiv);
               }

               if (elementType == "date") {
                  console.log("Ez egy dátum");
                  var dateDiv = document.createElement("div");
                  dateDiv.classList.add("mb-3");

                  var label = document.createElement("label");
                  if (elementSettings == "") {
                     label.innerHTML = "Dátum:";
                  } else {
                     label.innerHTML = elementSettings;
                  }
                  label.for = elementId;
                  dateDiv.appendChild(label);

                  var input = document.createElement("input");
                  input.type = "date";
                  input.classList.add("form-control");
                  input.id = elementId;
                  dateDiv.appendChild(input);
                  console.log(dateDiv);

                  formContainer.appendChild(dateDiv);
               }

               if (elementType == "shortText") {
                  console.log("Ez egy rövid szöveg");
                  var textDiv = document.createElement("div");
                  textDiv.classList.add("mb-3");

                  var label = document.createElement("label");
                  if (elementSettings == "") {
                     label.innerHTML = "Rövid válasz:";
                  } else {
                     label.innerHTML = elementSettings;
                  }
                  label.for = elementId;
                  textDiv.appendChild(label);

                  var input = document.createElement("input");
                  input.type = "text";
                  input.classList.add("form-control");
                  input.id = elementId;
                  input.placeholder = "Írja be a szöveget";
                  textDiv.appendChild(input);
                  console.log(textDiv);

                  formContainer.appendChild(textDiv);
               }

               if (elementType == "Feleletválasztós") {
                  console.log("Ez egy feleletválasztós");
                  var radioGroup = document.createElement("div");
                  radioGroup.classList.add("mb-3");

                  var label = document.createElement("label");
                  if (elementSettings == "") {
                     label.innerHTML = "Válasszon:";
                  } else {
                     label.innerHTML = elementSettings;
                  }
                  radioGroup.appendChild(label);

                  var input = document.createElement("input");
                  input.type = "radio";
                  input.id = elementId + "1";
                  input.name = elementId;
                  radioGroup.appendChild(input);

                  var label = document.createElement("label");
                  label.innerHTML = "Igen";
                  label.for = elementId + "1";
                  radioGroup.appendChild(label);

                  var input = document.createElement("input");
                  input.type = "radio";
                  input.id = elementId + "2";
                  input.name = elementId;
                  radioGroup.appendChild(input);

                  var label = document.createElement("label");
                  label.innerHTML = "Nem";
                  label.for = elementId + "2";
                  radioGroup.appendChild(label);

                  formContainer.appendChild(radioGroup);
               }

               if (elementType == "Hosszú szöveg") {
                  console.log("Ez egy hosszú szöveg");
                  var longText = document.createElement("div");
                  longText.classList.add("mb-3");

                  var label = document.createElement("label");
                  if (elementSettings == "") {
                     label.innerHTML = "Szöveg:";
                  } else {
                     label.innerHTML = elementSettings;
                  }
                  label.for = elementId;
                  longText.appendChild(label);

                  var input = document.createElement("textarea");
                  input.classList.add("form-control");
                  input.id = elementId;
                  input.placeholder = "Írja be a szöveget";
                  longText.appendChild(input);
                  console.log(longText);

                  formContainer.appendChild(longText);
               }


               if (elementType == "Szakaszcím") {
                  console.log("Ez egy szakaszcím");

                  var section = document.createElement("h3");
                  section.innerHTML = elementSettings;
                  section.id = elementId;

                  formContainer.appendChild(section);
                  /*                   var accordion = document.createElement("div");
                                    accordion.classList.add("accordion");
                                    accordion.classList.add("accordion-flush");
                                    accordion.id = "Szakaszcím"; */


                  /*                   formContainer.appendChild(accordion);
                                    console.log(accordion); */

               }
               /*                if (elementType == "Szakasz bekezdés") {
                                 console.log("Ez egy szakasz bekezdés");
                                 var sectionText = document.createElement("p");
               
                                 sectionText.innerHTML = elementSettings;
                                 sectionText.id = elementId;
               
                                 formContainer.appendChild(sectionText);
                              } */
               if (elementType == "Lineáris skála") {
                  console.log("Ez egy lineáris skála");
                  var linearScale = document.createElement("div");
                  linearScale.classList.add("form-group");
                  linearScale.id = elementId;

                  var label = document.createElement("label");
                  label.innerHTML = elementSettings;
                  linearScale.appendChild(label);

                  var input = document.createElement("input");
                  input.type = "range";
                  input.classList.add("form-range");
                  input.min = "0";
                  input.max = "5";
                  input.step = "1";
                  input.id = elementId;
                  linearScale.appendChild(input);

                  formContainer.appendChild(linearScale);
               }
            }
         }
      })
   });
</script>