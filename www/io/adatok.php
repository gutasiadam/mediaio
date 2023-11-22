<?php
namespace Mediao;

include "translation.php";
include "header.php";
//Suppresses error messages
error_reporting(E_ERROR | E_PARSE);

namespace Mediaio;

//require "./Mediaio_autoload.php";

use Mediaio\itemDataManager;

require "./itemManager.php";

?>
<!DOCTYPE html>

<body>
  <?php if (isset($_SESSION["userId"])) { ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php">
        <img src="./utility/logo2.png" height="50">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto navbarUl">
          <script>
            $(document).ready(function () {
              menuItems = importItem("./utility/menuitems.json");
              drawMenuItemsLeft('adatok', menuItems);
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
        <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
          <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
          <script type="text/javascript">
            window.onload = function () {
              display = document.querySelector('#time');
              var timeUpLoc = "utility/userLogging.php?logout-submit=y"
              startTimer(display, timeUpLoc);
            };
          </script>
        </form>
        <a class="nav-link my-2 my-sm-0" href="./help.php">
          <i class="fas fa-question-circle fa-lg"></i>
        </a>
      </div>
    </nav>
  <?php } ?>
  <br>
  <form>
    Mutasd a
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="rentable" id="inlinea" value="1" <?php if (isset($_GET['rentable']) && $_GET['rentable'] == '1')
        echo 'checked'; ?>>
      <label class="form-check-label" for="inlinea">Médiás,</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="studio" id="inlineb" value="2" <?php if (isset($_GET['studio']) && $_GET['studio'] == '2')
        echo 'checked'; ?>>
      <label class="form-check-label" for="inlineb">Stúdiós,</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="Event" id="inlined" value="5" <?php if (isset($_GET['Event']) && $_GET['Event'] == '5')
        echo 'checked'; ?>>
      <label class="form-check-label" for="inlined">Event</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="nonRentable" id="inlinec" value="3" <?php if (isset($_GET['nonRentable']) && $_GET['nonRentable'] == '3')
        echo 'checked'; ?>>
      <label class="form-check-label" for="inlinec">Nem Kölcsönözhető,</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="Out" id="inlined" value="4" <?php if (isset($_GET['Out']) && $_GET['Out'] == '4')
        echo 'checked'; ?>>
      <label class="form-check-label" for="inlined">Kinnlevő,</label>
    </div>


    tárgyakat,
    <select id="orderByField" name="orderByField">
      <option value="UID" <?php if (isset($_GET['orderByField']) && $_GET['orderByField'] == 'UID')
        echo 'selected'; ?>>
        UID
      </option>
      <option value="Nev" <?php if (isset($_GET['orderByField']) && $_GET['orderByField'] == 'Nev')
        echo 'selected'; ?>>
        Név
      </option>
      <option value="Tipus" <?php if (isset($_GET['orderByField']) && $_GET['orderByField'] == 'Tipus')
        echo 'selected'; ?>>
        Típus</option>
      <option value="RentBy" <?php if (isset($_GET['orderByField']) && $_GET['orderByField'] == 'RentBy')
        echo 'selected'; ?>>
        Kivette</option>
    </select>
    <label for="orderByField">szerint rendezve,</label>
    <select id="order" name="order">
      <option value="ASC" <?php if (isset($_GET['order']) && $_GET['order'] == 'ASC')
        echo 'selected'; ?>>növekvő</option>
      <option value="DESC" <?php if (isset($_GET['order']) && $_GET['order'] == 'DESC')
        echo 'selected'; ?>>csökkenő
      </option>
    </select>

    <label for="order">sorrendben.</label>
    <button class="btn btn-success my-2 my-sm-0" type="submit">Mehet</button>
  </form>

  <?php
  $countOfRec = 0;
  $displayData = array("Event" => $_GET['Event'], "rentable" => $_GET['rentable'], "studio" => $_GET['studio'], "nonRentable" => $_GET['nonRentable'], "Out" => $_GET['Out'], "orderByField" => $_GET['orderByField'], "order" => $_GET['order']);
  $result = itemDataManager::getItemData($displayData);
  if ($result != NULL && $result->num_rows > 0) {
    echo "<table id=" . "dataTable" . " align=center class=" . "table" . "><th onclick=sort(0)>UID</th><th onclick=sort(1)>Név</th><th onclick=sort(2)>Típus</th><th onclick=sort(3)>Kivette</th>";
    //output data of each row
    //Displays amount of records found in leltar_master DB
    while ($row = $result->fetch_assoc()) {
      if ($row["Status"] == 0) {
        echo "<tr style='background-color:#fffeab;' ><td><a id=#" . $row["UID"] . "></a>" . $row["UID"] . "</td><td>" . $row["Nev"] . "</td><td>" . $row["Tipus"] . "</td><td><strong>" . $row["RentBy"] . "</strong></td></tr>";
      } else if ($row["TakeRestrict"] == "s") {
        echo "<tr style='background-color:#7db3e8;' ><td><a id=#" . $row["UID"] . "></a>" . $row["UID"] . "</td><td>" . $row["Nev"] . "</td><td>" . $row["Tipus"] . "</td><td><strong>" . $row["RentBy"] . "</strong></td></tr>";
      } else if ($row["TakeRestrict"] == "*") {
        echo "<tr style='background-color:#F5B8B8;' ><td><a id=#" . $row["UID"] . "></a>" . $row["UID"] . "</td><td>" . $row["Nev"] . "</td><td>" . $row["Tipus"] . "</td><td><strong>" . $row["RentBy"] . "</strong></td></tr>";
      } else if ($row["TakeRestrict"] == "e") {
        echo "<tr style='background-color:#4ca864;' ><td><a id=#" . $row["UID"] . "></a>" . $row["UID"] . "</td><td>" . $row["Nev"] . "</td><td>" . $row["Tipus"] . "</td><td><strong>" . $row["RentBy"] . "</strong></td></tr>";
      } else {
        echo "<tr><td><a id=#" . $row["UID"] . "></a>" . $row["UID"] . "</td><td>" . $row["Nev"] . "</td><td>" . $row["Tipus"] . "</td><td>" . "</td></tr>";
      }
      $countOfRec += 1;
    }
  } else {
    echo "// Nem található a keresési feltételeknek megfelelő tárgy a rendszerben. //";
  }
  echo "</table>";
  ?>

</body>