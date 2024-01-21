<?php
error_reporting(E_ERROR | E_PARSE);
use Mediaio\Database;

require_once "../Database.php";
session_start();
include("header.php");
if (in_array("admin", $_SESSION["groups"]) or in_array("teacher", $_SESSION["groups"])) { ?>

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
          <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
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

  $conn = Database::runQuery_mysqli();
  $sql = ("SELECT * FROM `leltar`");
  $result = $conn->query($sql) or die($conn->error);
  $row_cnt3 = $result->num_rows; //Max_tárgy a raktárban

  $sql = ("SELECT * FROM `leltar` WHERE Status = 1");
  $result = $conn->query($sql) or die($conn->error);
  $row_cnt4 = $result->num_rows; //Jelenleg benn tárgyak

  $sql = ("SELECT userNameUsers FROM `users`");
  $result = $conn->query($sql) or die($conn->error);
  $row_cnt5 = $result->num_rows; //regiszztrált felhasználók
  $conn->close();
  ?>

  <body>
    <h1 class="rainbow">Statisztika</h1>
    <div class="container text-center">
      <div class="row justify-content-center">
        <div class="col">
          <h2>
            <?php echo $row_cnt3 ?>/<span class="text text-success">
              <?php echo $row_cnt4 ?>
            </span> tárgy van benn.
          </h2>
          <h6>
            (
            <?php echo number_format((float) (($row_cnt4 / $row_cnt3) * 100), 2, '.', ''); ?>%)
          </h6>

        </div>
      </div>
      <div class="row justify-content-center">
        <h6>
          <?php echo $row_cnt5 ?> felhasználó regisztrálva.
        </h6>
      </div>
    </div>

    <h3 class="panel-title">A hét eseményei</h1>

      <table class="table table-bordered" id="stat-table">
        <?php

        $connectionObject = Database::runQuery_mysqli();
        $query = "SELECT * FROM takelog WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) ORDER BY Date DESC;";
        // echo $query;
        $result = mysqli_query($connectionObject, $query);
        echo '<tr><th>Dátum</th><th>Felhasználó</th><th>Tárgy</th><th>Esemény</th><th>Ellenőrizte</th></tr>';
        foreach ($result as $row) {
          $ackcolby = "";
          if ($row['Acknowledged'] == "1") {
            $ackcolby = $row['ACKBY'];
          } else {
            $ackcolby = "<b>Jóváhagyásra vár</b>";
          }

          $event = "?";
          if ($row['Event'] == "SERVICE") {
            $event = "🔧";
          } else {
            $event = $row['Event'];
          }
          //Make row['Items'] JSON's name field unordered list
          $items = json_decode($row['Items'], true);
          $items = array_column($items, 'name');
          //TODO: concatenate UID to the name
          $items2 = json_decode($row['Items'], true);
          $items2 = array_column($items2, 'UID');



          $items = "<ul><li>" . implode("</li><li>", $items) . "</li></ul>";
          $row['Items'] = $items;
          //If event is OUT, TR is red, else its green
          if ($row['Event'] == "OUT") {
            echo '<tr class="table-danger"><td>' . $row['Date'] . '</td><td>' . $row['User'] . '</td><td>' . $row['Items'] . '</td><td>' . $event . '</td><td>' . $ackcolby . '</td></tr>';
          } else if ($row['Event'] == "IN") {
            echo '<tr class="table-success"><td>' . $row['Date'] . '</td><td>' . $row['User'] . '</td><td>' . $row['Items'] . '</td><td>' . $event . '</td><td>' . $ackcolby . '</td></tr>';
          } else {
            echo '<tr><td>' . $row['Date'] . '</td><td>' . $row['User'] . '</td><td>' . $row['Items'] . '</td><td>' . $event . '</td><td>' . $ackcolby . '</td></tr>';
          }


        }
        ?>
      </table>
  </body>

<?php } else {
  echo "<h2 class='text text-danger'>Nincs jogosultságod az oldal megtekintéséhez.</h2>";
} ?>