<?php
session_start();
include "header.php";
include("../translation.php"); ?>
<html>
<link href="utility/themes/default/style.min.css" rel="stylesheet" />


<title>MediaIo - forms</title>
<?php if (isset($_SESSION["userId"])) { ?>
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



  <h2 class="rainbow">Kitölthető kérdőívek</h2>
  <?php if (in_array("admin", $_SESSION["groups"])) {

      // A szerkesztés alatt levő formokat is megjelenítjük
//TODO
    } ?>
  <div class="container" id="available_forms">
    <div class="row" id="admin_opt">

    </div>
    <div class="row">

    </div>
  </div>

  <?php

} else {
  echo "<h1 class='rainbow'>Árpád Média - Kitölthető kérdőívek</h1>";
  ?>
  <div class="container" id="available_forms">

  </div>
  <?php
}
?>

</html>

<body>
</body>


<script>
  //onload
  $(document).ready(function () {
    //If user is admin
    if (<?php echo in_array("admin", $_SESSION["groups"]) ? "true" : "false" ?>) {
      $("#admin_opt").append('<button class="btn btn-success noprint mb-2 mr-sm-2" onclick=createNewForm()><i class="fas fa-plus fa-lg"></i></button>');
      //Everything that happens on load, when user is admin
      listEditingPhaseForms();

    }

    //If user is logged in
    if (<?php echo isset($_SESSION["userId"]) ? "true" : "false" ?>) {
      //Everything that happens on load, when user is logged in
      listRestrictedForms();
      listPublicForms();
    } else {
      //Everything that happens on load, when user is not logged in
      listPublicForms();
    }
    //Make an ajax call to formManager.php

  });

  function showFormAnswers(formId) {
    window.location.href = "formanswers.php?formId=" + formId;
  }

  function createCard(formId, formName, formStatus, formAccessRestrict) {
    var card = document.createElement("div");
    card.style = "width: 18rem;";
    card.className = "card";

    var cardBody = document.createElement("div");
    cardBody.className = "card-body";

    var cardTitle = document.createElement("h5");
    cardTitle.className = "card-title";
    cardTitle.innerHTML = formName;

    var cardText = document.createElement("p");
    cardText.className = "card-text";
    cardText.innerHTML = "Kitöltési státusz: " + formStatus + "<br>" + "Biztonság " + formAccessRestrict;

    var cardButton = document.createElement("button");
    cardButton.className = "btn btn-primary";
    cardButton.innerHTML = "Kitöltöm";
    cardButton.onclick = function () { openForm(formId) };

    cardBody.appendChild(cardTitle);
    cardBody.appendChild(cardText);
    cardBody.appendChild(cardButton);

    if (<?php echo in_array("admin", $_SESSION["groups"]) ? "true" : "false" ?>) {
      var editButton = document.createElement("button");
      editButton.className = "btn btn-warning noprint";
      editButton.innerHTML = "<i class='fas fa-highlighter fa-lg'></i> Szerkeszt";
      editButton.onclick = function () { editForm(formId) };
      cardBody.appendChild(editButton);

      var showAnswersButton = document.createElement("button");
      showAnswersButton.className = "btn btn-info noprint";
      showAnswersButton.innerHTML = "<i class='fas fa-check fa-lg'></i> Válaszok";
      showAnswersButton.onclick = function () { showFormAnswers(formId) };
      cardBody.appendChild(showAnswersButton);
      
      
    }


    card.appendChild(cardBody);
    console.log(card);
    document.getElementById("available_forms").appendChild(card);
  }


  function listRestrictedForms() {
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "getRestrictedForms" }, //In the future, once we have group restrictions, this code needs to be updated.
      success: function (response) {
        //console.log(response);
        response = JSON.parse(response);
        console.log("restriced: " + response);

        response.forEach(element => {
          console.log(element);
          if (element.Name == null) { element.Name = "Névtelen"; }
          if (element.Status == 1) {
            element.Status = "Kitölthető";
          } else {
            element.Status = "Zárolt";
          }

          createCard(element.ID, element.Name, element.Status, "Korlátozott");
        });
      }
    });

  }

  function listPublicForms() {
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "getPublicForms" }, //In the future, once we have group restrictions, this code needs to be updated.
      success: function (response) {
        console.log(response);
        response = JSON.parse(response);
        console.log("public: " + response);
        //Add a tr for each form to formTable
        response.forEach(element => {
          console.log(element);
          if (element.Name == null) { element.Name = "Névtelen"; }
          if (element.Status == 1) {
            element.Status = "Kitölthető";
          } else {
            element.Status = "Zárolt";
          }
          createCard(element.ID, element.Name, element.Status, "Publikus");
        });
      }
    });
  }


  //List forms that are in editing phase
  function listEditingPhaseForms() {
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "getEditingPhaseForms" },
      success: function (response) {

        response = JSON.parse(response);
        console.log("editing: " + response);
        i = 1;
        response.forEach(element => {
          console.log(element);
          if (element.Name == null) { element.Name = "Névtelen" + i; i++; }
          $("#admin_opt").append('<button class="btn btn-warning noprint mb-2 mr-sm-2" onclick=editForm(' + element.ID + ')><i class="fas fa-highlighter fa-lg"></i> ' + element.Name + '</button>');
        });
      }
    });
  }
  function createNewForm() {
    //Make an ajax call to formManager.php
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "createNewForm" },
      success: function (response) {
        //console.log(response);
        window.location.href = "formeditor.php?formId=" + response;
      }
    });
  }

  function openForm(formId) {
    window.location.href = "viewform.php?formId=" + formId;
  }

  function editForm(formId) {
    window.location.href = "formeditor.php?formId=" + formId;
  }
</script>