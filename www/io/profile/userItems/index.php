<?php

namespace Mediaio;


use Mediaio\Database;


session_start();
require_once ('../../Database.php');
include "header.php";

error_reporting(E_ALL ^ E_NOTICE);
if (!isset($_SESSION['userId'])) {
  echo "<script>window.location.href = '../../index.php?error=AccessViolation';</script>";
  exit();
}
?>


<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../index.php">
    <img src="../../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function () {
          menuItems = importItem("../../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems, 3);
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
    <form method='post' class="form-inline my-2 my-lg-0" action=../../utility/userLogging.php>
      <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
        type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc = "../../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
  </div>
</nav>

<body>
  <h1 class="rainbow" id="title">Nálad levő tárgyak&nbsp;</h1>
  <div class="container">
    <div class="itemsToRetrieve" id="itemsHolder">
    </div>
    <button class="btn btn-success w-50 mb-3" id="submission" onclick="openRetrieve()">Visszahozás</button>
  </div>

</body>

</html>

<script>
  $(document).ready(function () {
    loadItems();
  });

  function openRetrieve() {
    window.location.href = "../../retrieve/";
  }

  async function loadItems() {

    const response = JSON.parse(await $.ajax({
      url: "../../ItemManager.php",
      method: "POST",
      data: {
        mode: "listUserItems"
      }
    }));

    const itemHolder = document.getElementById("itemsHolder");
    itemHolder.innerHTML = "";

    if (response.length == 0) {
      const noItems = document.createElement("div");
      noItems.classList.add("alert", "alert-info", "mt-3", "text-center");
      noItems.style.width = "400px";
      noItems.innerHTML = "Nincsen nálad egy tárgy sem!";
      itemHolder.appendChild(noItems);

      itemHolder.style.gridTemplateColumns = "none";
      itemHolder.style.justifyContent = "center";
      itemHolder.style.alignItems = "center";

      document.getElementById("submission").style.display = "none";
      return;
    }

    response.forEach(element => {
      createItemCard(element);
    });

    const badge = document.createElement("span");
    badge.classList.add("badge", "bg-primary");
    badge.innerHTML = `${response.length} db`;
    badge.style.fontSize = "20px";
    document.getElementById("title").appendChild(badge);

  }

  function createItemCard(item) {

    const itemHolder = document.getElementById("itemsHolder");

    const itemCard = document.createElement("div");
    itemCard.classList.add("card", "itemCard");
    itemCard.id = item.UID;


    const cardBody = document.createElement("div");
    cardBody.classList.add("card-body");

    const cardTitle = document.createElement("h5");
    cardTitle.classList.add("card-title");
    cardTitle.innerHTML = item.Nev;

    const cardText = document.createElement("p");
    cardText.classList.add("card-text");
    cardText.innerHTML = item.UID;



    cardBody.appendChild(cardTitle);
    cardBody.appendChild(cardText);
    itemCard.appendChild(cardBody);

    itemHolder.appendChild(itemCard);

  }
</script>