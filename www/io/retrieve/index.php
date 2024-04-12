<?php

namespace Mediaio;

session_start();

if (!isset($_SESSION['userId'])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
}

include "header.php";
?>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
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
          drawMenuItemsLeft('retrieve', menuItems, 2);
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

<!-- Scanner Modal -->
<div class="modal fade" id="scanner_Modal" tabindex="-1" role="dialog" aria-labelledby="scanner_ModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Szkenner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="pauseCamera()"
          aria-label="Close"></button>
      </div>
      <div class="modal-body" id="scanner_body">
        <div id="reader" width="600px">

        </div>
        <!-- Toasts -->
        <div class="toast align-items-center" id="scan_toast" role="alert" aria-live="assertive" aria-atomic="true"
          style="z-index: 9; display:none;">
          <div class="d-flex">
            <div class="toast-body" id="scan_result">
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="scanner_footer">
        <button type="button" class="btn btn-outline-dark" id="ext_scanner" onclick="ExternalScan()">Külső
          olvasó</button>
        <button type="button" class="btn btn-info" id="zoom_btn" onclick="zoomCamera()">Zoom: 2x</button>
        <button type="button" class="btn btn-info" id="torch_btn" onclick="startTorch()">Vaku</button>
        <div class="dropdown dropup">
          <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="true">
            Kamerák
          </button>
          <ul class="dropdown-menu" id="av_cams"></ul>
        </div>

        <!--         <input type="checkbox" class="btn-check btn-light" id="btncheck1" autocomplete="off" wfd-id="id0"
          onclick="startTorch()">
        <label class="btn btn-outline-primary" for="btncheck1"><i class="fas fa-lightbulb"></i></label> -->
        <button type="button" class="btn btn-success" onclick="pauseCamera()" data-bs-dismiss="modal">Kész</button>
      </div>
    </div>
  </div>
</div>
<!-- End of Scanner Modal -->

<body>
  <h2 class="rainbow" id="doTitle">Visszahozás</h2>
  <div class="container" id="retrieve-container">
    <!-- Announce Damage button -->
    <div class="row" id="retrieve-option-buttons">
      <button class="btn btn-danger" onclick="AnnounceDamage()">Sérülés bejelentése <i
          class="fas fa-file-alt"></i></button>
      <button type="button" class="btn btn-warning" onclick="showScannerModal()">Szkenner <i
          class="fas fa-qrcode"></i></button>
      <input type="checkbox" class="btn-check" autocomplete="off" id="manual_Retrieve">
      <label class="btn btn-outline-secondary" for="manual_Retrieve">Visszahozás kézzel</label>
    </div>

    <div class="row itemsToRetrieve">
      <div class="col">
        Nálad levő eszközök:
      </div>
      <div class="col">
        Kiválasztott eszközök:
      </div>
    </div>

    <div class="row" id="submit-retrieve">
      <!-- Displays a tickable when items are selected, to approve tekaout process -->
      <div class="col" id="intact">
        <div class="form-check intactForm" id="if_intact">
          <input class="form-check-input" type="checkbox" value="" id="intactItems">
          <label class="form-check-label" for="intactItems">
            <h6 class="statement">Igazolom, hogy minden, amit visszahoztam sérülésmentes és kifogástalanul működik.
              Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
          </label>
        </div>
      </div>
      <!-- Send button holder -->
      <div class="" id="send_retrieve">
        <button class="send btn btn-success">
          <i class="fas fa-check-square fa-4x"></i>
        </button>
      </div>
    </div>

    <div id='toTop'><i class="fas fa-chevron-down"></i></div>
</body>

<script>
  //Preventing double click zoom
  document.addEventListener('dblclick', function (event) {
    event.preventDefault();
  }, { passive: false });


</script>

</html>