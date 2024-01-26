<?php

namespace Mediaio;

use Mediaio\Database;

require_once("../Database.php");
session_start();
error_reporting(E_ERROR | E_PARSE);
include "header.php";
if (!(in_array("admin", $_SESSION["groups"]))) {
  exit();
}
if (!isset($_SESSION['userId'])) {
  header("Location: index.php?error=AccessViolation");
} ?>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<title>Elérhetőségek</title>

<?php if (isset($_SESSION["userId"])) { ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
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
            drawMenuItemsLeft('profile', menuItems, 2);
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
<?php } ?>


<?php
/*Csoportosított rendezés*/
$sql = "SELECT takelog.Date, takelog.User, takelog.Event, takelog.Items FROM `takelog` WHERE takelog.Acknowledged = 0 AND 
  takelog.Event != 'SERVICE' ORDER BY DATE DESC, USER, EVENT";
$connection = Database::runQuery_mysqli();
$result = $connection->query($sql);
if ($result->num_rows > 0) {
  echo "<table id="."user-check"." class=" . "table" . "><th>Dátum</th><th>Felhasználónév</th><th>Eszköz</th><th>Esemény</th><th>JSON</th>";
  $recCount = 0;
  
  while ($row = $result->fetch_assoc()) {
    $itemsString = '';
    $recCount += 1;
    //store row[Items] json obejct as php array.
    $items = json_decode($row['Items'], true);

    //for each items in the array, print the name field
    foreach ($items as $item) {
      $itemsString .= $item['name'] . ";  ";
    }

    echo "<tr class="."event"." id=event" . $recCount . "><td>" . $row["Date"] . "</td><td>" . $row["User"] . "</td><td>" . $itemsString . "</td><td>" . $row["Event"] . "</td><td>" . $row["Items"] . "</td>
        <td><button class='btn btn-success' onclick='acceptEvent(" . $recCount . ")'><i class='fas fa-solid fa-check'></i></button></br>";
    if ($row['Event'] == 'OUT') {
      //declineEvent
      echo "<button class='btn btn-danger' style='padding: 7px 15px; margin-top:4px' onclick='declineEvent(" . $recCount . ")'><i class='fas fa-times danger'></i></button></td>";
    } else if ($row['Event'] == 'IN') {
      echo "<button class='btn btn-warning' style='padding: 7px 14.5px; margin-top:4px' onclick='openEventDocument(" . $recCount . ")'><i class='fas fa-file-alt'></i></button></td>";

      echo "</tr>";
    }
  }
} else {
  echo '<h3 class="nothing_here">Jelenleg semmi nem vár elfogadásra!</h3>';
}
echo "</table>";

?>
<script>
  function acceptEvent(n) {
    //alert('elfogadas');
    var items = JSON.parse($('#event' + n)[0].cells[4].innerHTML);
    var itemsJSON = JSON.stringify(items);
    var eventType = $('#event' + n)[0].cells[3].innerHTML;
    var date = $('#event' + n)[0].cells[0].innerHTML;
    var user = $('#event' + n)[0].cells[1].innerHTML;
    var mode;
    if (eventType == 'IN') {
      mode = 'retrieveApproval';
    } else {
      mode = 'takeOutApproval';
    }
    console.log(items);
    $.ajax({
      method: 'POST',
      url: '../ItemManager.php',
      data: {
        data: itemsJSON,
        mode: mode,
        value: true,
        date: date,
        user: user
      }, //value true means event is approved.
      success: function (response) {
        if (response == 200) {
          //Remove event from the table.
          $('#event' + n).fadeOut();
          setTimeout(function () {
            location.reload();
          }, 500);
        } else {
          console.log("Backend error.");
          alert(response);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown);
      }
    });
  }

  function declineEvent(n) {
    var items = JSON.parse($('#event' + n)[0].cells[4].innerHTML);
    // items.forEach(element => {
    //   console.log(element);
    //   element=element.split('(');
    // });
    var itemsJSON = JSON.stringify(items);
    var eventType = $('#event' + n)[0].cells[3].innerHTML;
    var date = $('#event' + n)[0].cells[0].innerHTML;
    var mode;
    var user = $('#event' + n)[0].cells[1].innerHTML;
    if (eventType == 'IN') {
      mode = 'retrieveApproval';
    } else {
      mode = 'takeOutApproval';
    }
    //alert(items.stringify());
    $.ajax({
      method: 'POST',
      url: '../ItemManager.php',
      data: {
        data: itemsJSON,
        mode: mode,
        value: false,
        date: date,
        user: user
      }, //event declined.
      success: function (response) {
        //alert(response)
        if (response == 200) {
          //Remove event from the table.
          $('#event' + n).fadeOut();
          location.reload();
        } else {
          console.log("Backend error.");
          alert(response);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown);
      }
    });
  }

  function openEventDocument(n) {
    window.location.replace("../utility/damage_report/announce_Damage.php");
  }
</script>