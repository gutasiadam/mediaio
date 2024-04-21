<?php
session_start();
if (!isset($_SESSION['userId'])) {
  header("Location: ../index.php?error=AccessViolation");
}
#echo $_SESSION['color'];
?>
<html lang='en'>

<head>
  <title>Arpad Media IO</title>
  <link rel="icon" type="image/x-icon" href="../logo.ico">
  <link href='../style/common.scss' rel='stylesheet' />
  <div class="UI_loading"><img class="loadingAnimation" src="../utility/mediaIO_loading_logo.gif"></div>
  <meta charset='utf-8' />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
    integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
  <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>
  <link href='./core/main.css' rel='stylesheet' />
  <link href='./daygrid/main.css' rel='stylesheet' />
  <link href='./timegrid/main.css' rel='stylesheet' />
  <script src='./interaction/main.css'></script>

  <script src='./core/main.js'></script>
  <script src='./daygrid/main.js'></script>
  <script src='./timegrid/main.js'></script>
  <script src='./interaction/main.js'></script>
  <script src="./moment/main.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="./defaultCalendarRender.js"></script>
</head>
<script>
  $(window).on('load', function () {
    $(".UI_loading").fadeOut("slow");
  });
</script>
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
            drawMenuItemsLeft('events', menuItems, 2);
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
            startTimer(display, timeUpLoc, 30);
          };
        </script>
      </form>
    </div>
  </nav>
<?php } ?>

<body>

  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Esemény hozzáadása</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h6>Esemény hozzáadása <span id="addEventInterval"></span> időben</h6>
          <form id="sendAddEvent" class="form-group">
            <select class="form-control" id="eventTypeSelect" required>
              <option value="" selected disabled hidden>Típus</option>
              <?php if (in_array("admin", $_SESSION["groups"])) {
                echo '<option value="#ff6363">Délelőtti iskolai esemény</option>
        <option value="#db4040">Délutáni iskolai esemény</option>
        <option value="#bd7966">Hétvégi iskolai esemény</option>
        <option value="#59ffba">Workshop</option>
        <option value="#fffd6b">Szünet</option>
        <option value="#81c773">Gyűlés</option>';
              } ?>
              <option value="#ffb145">Külsős esemény</option>
              <option value="#917fe3">Otthoni munka</option>
              <option value="#787878">Egyéb</option>
            </select>
            </br>
            <input class="form-control" id="addEventName" type="text" placeholder="esemény címe"></input></br>
            <h6 class="mailSend"><i class="fas fa-exclamation-circle"></i> Hozzáadás után az e-mail címedre (
              <?php echo $_SESSION['email']; ?>) érkezni fog egy levél. Kérlek ellenőrizd az adatokat, és az
              <strong>esemény hozzáadása</strong> linkkel erősítsd meg
              szándékodat. <u>(megerősítés után már nem tudod törölni az eseményt.)</u>
            </h6>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
          <input type="submit" class="btn btn-primary"></button>
          <input type="hidden" id="addEventStartVal"></input>
          <input type="hidden" id="addEventEndVal"></input>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- OPCIÓK MODAL -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="optionsLabel">Opcíók</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if (in_array("admin", $_SESSION["groups"])) {
            echo ' <form id="sendDelEvent">
        <input type="hidden" id="delEventId"></input>
        <input type="hidden" id="delEventTitle"></input>
        </form>';
          } ?>
          <form id="worksheetShow" name="worksheetShow" onsubmit="workSheetPrepare(this);">
            <input type="submit" class="btn btn-dark" value="Munkalap megtekintése"></button>
            <input type="hidden" id="delEventId"></input>
            <input type="hidden" id="delEventTitle"></input>
          </form>
        </div>
        <div class="modal-footer">
          <span id="deleteEventName"></span>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
        </div>
      </div>
    </div>
  </div>


  <table class="table table-bordered" style="height: 85dvh">
    <tr>
      <td style="height: 85dvh">
        <div id='calendar'>
      </td>
      </div>
  </table>

</body>

</html>

<script>
  function openNav() {
    document.getElementById("sideHelp1").style.width = "250px";
  }

  /* Set the width of the side navigation to 0 */
  function closeNav() {
    document.getElementById("sideHelp1").style.width = "0";
  }

  window.onload = function () {
    //$('#WIPModal').modal()
    display = document.querySelector('#time');
    var timeUpLoc = "../utility/userLogging.php?logout-submit=y"
    startTimer(display, timeUpLoc);
  };

  $(document).ready(function () {
    $(".mailSend").hide();
  });
</script>