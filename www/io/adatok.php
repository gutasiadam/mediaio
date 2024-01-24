<?php
namespace Mediao;

include "translation.php";
include "header.php";

namespace Mediaio;

use Mediaio\itemDataManager;

require "./ItemManager.php";

?>
<!DOCTYPE html>
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
        <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
          type="submit">Kijelentkezés</button>
        <script type="text/javascript">
          window.onload = function () {
            display = document.querySelector('#time');
            var timeUpLoc = "utility/userLogging.php?logout-submit=y"
            startTimer(display, timeUpLoc);
          };
        </script>
      </form>
    </div>
  </nav>
<?php } ?>
<br>
<form>
  <!-- Selection -->
  <span>Mutasd a</span>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" name="rentable" id="inline_a" value="1" <?php if (isset($_GET['rentable']) && $_GET['rentable'] == '1')
      echo 'checked'; ?>>
    <label class="form-check-label" for="inline_a">Médiás,</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" name="studio" id="inline_b" value="2" <?php if (isset($_GET['studio']) && $_GET['studio'] == '2')
      echo 'checked'; ?>>
    <label class="form-check-label" for="inline_b">Stúdiós,</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" name="Event" id="inline_d" value="5" <?php if (isset($_GET['Event']) && $_GET['Event'] == '5')
      echo 'checked'; ?>>
    <label class="form-check-label" for="inline_d">Event</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" name="nonRentable" id="inline_c" value="3" <?php if (isset($_GET['nonRentable']) && $_GET['nonRentable'] == '3')
      echo 'checked'; ?>>
    <label class="form-check-label" for="inline_c">Nem Kölcsönözhető,</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" name="Out" id="inline_e" value="4" <?php if (isset($_GET['Out']) && $_GET['Out'] == '4')
      echo 'checked'; ?>>
    <label class="form-check-label" for="inline_e">Kinnlevő,</label>
  </div>
  <span>tárgyakat,</span>
  <select id="orderByField" name="orderByField">
    <option value="UID" <?php if (isset($_GET['orderByField']) && $_GET['orderByField'] == 'UID')
      echo 'selected'; ?>>UID
    </option>
    <option value="Nev" <?php if (isset($_GET['orderByField']) && $_GET['orderByField'] == 'Nev')
      echo 'selected'; ?>>Név
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
  <button class="btn btn-success my-2 my-sm-0" type="submit" id="submit">Mehet</button>
</form>

<?php
$countOfRec = 0;
$displayData = array("Event" => $_GET['Event'], "rentable" => $_GET['rentable'], "studio" => $_GET['studio'], "nonRentable" => $_GET['nonRentable'], "Out" => $_GET['Out'], "orderByField" => $_GET['orderByField'], "order" => $_GET['order']);
$result = itemDataManager::getItemData($displayData);
if ($result != NULL && $result->num_rows > 0) {
  echo "<table class=" . "table" . " id=" . "dataTable" . ">
  <th id='UID' onclick=sortClick('UID')><a>UID</a></th>
  <th id='Nev' onclick=sortClick('Nev')><a>Név</a></th>
  <th id='Tipus' onclick=sortClick('Tipus')><a>Típus</a></th>
  <th id='RentBy' onclick=sortClick('RentBy')><a>Kivette</a></th>";
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
  echo '<h3 class="nothing_here">Nem található a keresési feltételeknek megfelelő tárgy a rendszerben.</h3>';
}
echo "</table>";
?>

</body>

<script>
  let currentSort = document.getElementById('orderByField');

  function sortClick(sortParam) {

    if (sortParam === currentSort.value && document.getElementById('order').value === 'ASC') {
      document.getElementById('order').value = 'DESC';
    } else if (sortParam === currentSort.value && document.getElementById('order').value === 'DESC') {
      document.getElementById('order').value = 'ASC';
    }

    if (sortParam === 'UID') {
      currentSort.value = 'UID';
    } else if (sortParam === 'Nev') {
      currentSort.value = 'Nev';
    } else if (sortParam === 'Tipus') {
      currentSort.value = 'Tipus';
    } else if (sortParam === 'RentBy') {
      currentSort.value = 'RentBy';
    }
    document.getElementById('submit').click();
  }

  if (document.getElementById('order').value === 'ASC') {
    switch (currentSort.value) {
      case 'UID':
        document.getElementById('UID').innerHTML += "<i id='ord1' class='fas fa-chevron-up ms-1'></i>";
        break;
      case 'Nev':
        document.getElementById('Nev').innerHTML += "<i id='ord1' class='fas fa-chevron-up ms-1'></i>";
        break;
      case 'Tipus':
        document.getElementById('Tipus').innerHTML += "<i id='ord1' class='fas fa-chevron-up ms-1'></i>";
        break;
      case 'RentBy':
        document.getElementById('RentBy').innerHTML += "<i id='ord1' class='fas fa-chevron-up ms-1'></i>";
        break;
    }
  }
  else {
    switch (currentSort.value) {
      case 'UID':
        document.getElementById('UID').innerHTML += "<i id='ord1' class='fas fa-chevron-down ms-1'></i>";
        break;
      case 'Nev':
        document.getElementById('Nev').innerHTML += "<i id='ord1' class='fas fa-chevron-down ms-1'></i>";
        break;
      case 'Tipus':
        document.getElementById('Tipus').innerHTML += "<i id='ord1' class='fas fa-chevron-down ms-1'></i>";
        break;
      case 'RentBy':
        document.getElementById('RentBy').innerHTML += "<i id='ord1' class='fas fa-chevron-down ms-1'></i>";
        break;
    }
  }

</script>