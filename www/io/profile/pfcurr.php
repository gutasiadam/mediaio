<?php
namespace Mediaio;

use Mediaio\Database;

session_start();
require_once('../Database.php');
include "header.php";

if (isset($_SESSION['userId'])) {
  error_reporting(E_ALL ^ E_NOTICE);
  //index.php
  ?>

  <html>

  <head>
    <script src="../utility/jquery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>

  </head>
  <?php if (isset($_SESSION["userId"])) { ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php">
        <img src="../utility/logo2.png" height="50">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
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
        <ul class="navbar-nav navbarPhP">
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
              var timeUpLoc = "/../utility/userLogging.php?logout-submit=y"
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

  <body>
    <div class="container">
      <br />


      <div class="form-group">
        <div class="panel panel-default">
          <div class="panel-heading">
            <?php
            $TKI = $_SESSION['UserUserName'];
            $sql = "SELECT * FROM `leltar` WHERE `RentBy` = '" . $TKI . "'";

            $result = Database::runQuery($sql);
            echo '<h3 class="panel-title">' . $_SESSION['firstName'] . ', ' . $result->num_rows . ' tárgy van most nálad:</h3>
        </div>';
            $imodal = -1;
            $resultArray = [];
            while ($row = $result->fetch_assoc()) {
              array_push($resultArray, $row);
              $rowItem = $row["Nev"];
              echo '
              <div class="row">
              <div class="col-4">
               <h2>' . $row["Nev"] . '</h2>
               <p>' . $row["UID"];
              if ($row['Status'] == '2') {
                echo ' <span class="text-warning">Jóváhagyásra vár.</span>';
              }
              echo '</p></div>';

              if ($row['Status'] != '2') {
                echo '
             <div class="col-2"><button class="btn btn-success " id="bringback' . $imodal . '" data-toggle="modal" data-target="#b' . $imodal . '">Visszahoztam</button></div>
             
             ';
                echo '
            <div class="modal fade" id="b' . $imodal . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">' . $row["Nev"] . ' Visszahozása</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
              <input type="hidden" id="retrieveItem_' . $imodal . '" name="retrieveItem" value="' . $row["Nev"] . '"/> 
              <input type="hidden" name="User" value="' . $TKI . '"/>
              <div class="form-check">
              <input class="form-check-input intactItems" type="checkbox" value="" id="intactItems' . $imodal . '">
              </div>
              <h6></h6>
              <h6 id="emailHelp" class="form-text text-muted">A kipipálással igazolom, hogy amit visszahoztam sérülésmentes, és kifogástalanul működik. Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">❌</button>
                  <button type="submit" id="' . $imodal . '" onClick="reply_click(this.id)" class="btn go_btn btn-success disabled">☑</button>
                  <a href="../utility/damage_report/announce_Damage.php" class="btn go_btn btn-warning">Problémát jelentek be</a>
                  
                  <p class="sysResponse"> </p>
            </div>
                  </div>
                  </div>
                  </div>   
            ';

              }
              $imodal++;
              echo '</div>';
            }
            if ($imodal == -1) {
              echo '// Jelenleg nincs nálad egy tárgy sem ';
            }
            echo '
           </div>
           </div>
          </div>
         </div>
        </div>';
            $connect = null;
            ?>


  </body>

  </html>
  <style>
    .timeline__item {
      background-color: #ededed;
    }
  </style>

  <?php
} else {
  echo ("A tartalom megtekintéséhez először jelentkezz be.");
}
//AUTH Handling

?>
<script>
  //Visszahozás ellenőrzése a handlernél
  function retrieve(i) { // i=> item
    console.log("Begin retrieve by handler");
    retrieveItem_list = [i];
    retrieveJSON = JSON.stringify(retrieveItem_list);
    console.log(retrieveJSON);
    $.ajax({
      method: 'POST',
      url: '../ItemManager.php',
      data: { data: retrieveJSON, mode: "retrieveStaging" },
      success: function (response) {
        if (response == 200) {
          $('.sysResponse').append('Sikeres művelet! Az oldal hamarosan újratölt.');
        }
        setTimeout(function () { location.reload(); }, 1500);

      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log("Status: " + textStatus); console.log("Hiba: " + errorThrown);
      }

    });
  };
  function reply_click(clicked_id) //Begyűjti a tárgy nevét, amit vissza akar a felhasználó hozni.
  {
    retrieveItem = document.getElementById('retrieveItem_' + (clicked_id)).value
    //alert(retrieveItem);
    if ($('.intactItems').is(":checked")) {
      retrieve(retrieveItem);// AJAXos visszahozás megkezdése
    } else {
      alert('Ha probléma akad a tárggyal, jelezd azt a vezetőségnek!');
      //$( ".intactItems" ).effect( "shake" );
    }

  }
  $(document).on('click', '.intactItems', function () { // Submit gomb engedélyezése, ha az Intact form ki lett pipálva.
    if ($('.intactItems').is(":checked")) {
      $('.go_btn').removeClass('disabled');
    }
  });

  function copyText(item) {
    /* Get the text field */
    var copyText = document.getElementById('$code'.item);
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/
    /* Copy the text inside the text field */
    document.execCommand("copy");
    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);
  }
  /*$(document).on('click', '.authToggle', function(){
    setTimeout(function(){ window.location.href = "./utility/logout.ut.php"; }, 3000);});*/
</script>