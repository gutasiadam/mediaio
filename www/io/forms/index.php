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



  <h2 class="rainbow">Kérdőívek</h2>
  <?php if (in_array("admin", $_SESSION["groups"])) {

      // A szerkesztés alatt levő formokat is megjelenítjük
//TODO
    } ?>
  <div class="container">
    <div class="row" id="admin_opt">

    </div>
    <div class="row" id="available_forms">

    </div>
  </div>

  <?php

} else {
  echo "<h1 class='rainbow'>Árpád Média - Kitölthető kérdőívek</h1>";
  ?>
  <div class="container">
    <div class="row" id="available_forms">

    </div>
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

    if (<?php echo isset($_GET['invalidID']) ? "true" : "false" ?>) {
      alert("Nem létező kérdőív");
    }

    if (<?php echo isset($_GET['closedForm']) ? "true" : "false" ?>) {
      alert("A kérdőív lezárásra került");
    }
    //If user is admin
    if (<?php echo in_array("admin", $_SESSION["groups"]) ? "true" : "false" ?>) {
      $("#admin_opt").append('<button class="btn btn-success noprint mb-2 mr-sm-2" onclick=createNewForm()><i class="fas fa-plus fa-lg"></i></button>');
      //Everything that happens on load, when user is admin
      listForms(2, 0);

    }

    //If user is logged in
    else if (<?php echo isset($_SESSION["userId"]) ? "true" : "false" ?>) {
      //Everything that happens on load, when user is logged in
      listForms(1, 2);
    } else {
      //Everything that happens on load, when user is not logged in
      listForms(0, 1);
    }
    //Make an ajax call to formManager.php

  });

  function showFormAnswers(formId) {
    window.location.href = "formanswers.php?formId=" + formId;
  }

  function createCard(formId, formName, formStatus, StillEditing, formAccessRestrict, backgroundImg) {
    var card = document.createElement("div");
    card.className = "card";

    var img = document.createElement("img");
    img.className = "card-img-top";
    img.src = backgroundImg;
    img.height = "100";
    if (backgroundImg == "") {
      img.src = "https://via.placeholder.com/100";
    }
    card.appendChild(img);

    var cardBody = document.createElement("div");
    cardBody.className = "card-body";

    var cardTitle = document.createElement("h5");
    cardTitle.className = "card-title";
    cardTitle.innerHTML = formName;
    cardBody.appendChild(cardTitle);



    var cardText = document.createElement("p");
    cardText.className = "card-text";
    cardText.innerHTML = formStatus;
    cardBody.appendChild(cardText);

    var ButtonHolder = document.createElement("div");
    ButtonHolder.className = "btn-group";
    ButtonHolder.role = "group";


    var cardButton = document.createElement("button");
    cardButton.className = "btn btn-primary";
    cardButton.innerHTML = "Kitöltöm";
    if (formStatus == "Lezárt") {
      cardButton.disabled = true;
    }
    cardButton.onclick = function () { openForm(formId) };


    ButtonHolder.appendChild(cardButton);

    if (<?php echo in_array("admin", $_SESSION["groups"]) ? "true" : "false" ?>) {
      var editButton = document.createElement("button");
      editButton.className = "btn btn-warning noprint";
      editButton.innerHTML = "<i class='fas fa-highlighter fa-lg'></i> Szerkeszt";
      editButton.onclick = function () { editForm(formId) };
      ButtonHolder.appendChild(editButton);

      var showAnswersButton = document.createElement("button");
      showAnswersButton.className = "btn btn-info noprint";
      showAnswersButton.innerHTML = "<i class='fas fa-check fa-lg'></i> Válaszok";
      showAnswersButton.onclick = function () { showFormAnswers(formId) };
      ButtonHolder.appendChild(showAnswersButton);


    }

    cardBody.appendChild(ButtonHolder);
    card.appendChild(cardBody);

    if (<?php echo in_array("admin", $_SESSION["groups"]) ? "true" : "false" ?>) {
      var cardFooter = document.createElement("div");
      cardFooter.className = "card-footer text-body-secondary";
      cardFooter.innerHTML = formAccessRestrict;
      card.appendChild(cardFooter);
    }

    console.log(card);
    document.getElementById("available_forms").appendChild(card);
  }


  //List forms that are in editing phase
  function listForms(formAccessRestrict, formState) {
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "listForms", accessRestrict: formAccessRestrict, formState: formState },
      success: function (response) {

        response = JSON.parse(response);
        console.log("editing: " + response);
        i = 1;
        response.forEach(element => {
          console.log(element);
          if (element.Name == null) { element.Name = "Névtelen" + i; i++; }
          if (element.AccessRestrict == "0") { element.AccessRestrict = "Publikus"; }
          if (element.AccessRestrict == "1") { element.AccessRestrict = "Privát"; }

          if (element.Status == "0") { element.Status = "Szerkesztés alatt"; }
          if (element.Status == "1") { element.Status = "Kitölthető"; }
          if (element.Status == "2") { element.Status = "Lezárt"; }

          var Background = "./backgrounds/" + element.Background;
          createCard(element.ID, element.Name, element.Status, true, element.AccessRestrict, Background);
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
        console.log(response);
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