<?php

namespace Mediaio;


use Mediaio\Database;


session_start();
require_once ('../Database.php');
include "header.php";

error_reporting(E_ALL ^ E_NOTICE);
if (!isset($_SESSION['userId'])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
}
?>

<html>

<head>
  <script src="../utility/jquery.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
    integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

</head>

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
          if ($result->num_rows == 0) {
            echo '<h3 class="nothing_here">' . $_SESSION['firstName'] . ', nincs nálad tárgy!</h3>';
          } else {
            echo '<h1 class="panel-title" style="margin-bottom: 25px">' . $_SESSION['firstName'] . ', ' . $result->num_rows . ' tárgy van most nálad:</h1>
              </div>';
          }
          echo '<div class="panel-body" id="pf-curr-items">';
          $imodal = 0;
          $resultArray = [];
          while ($row = $result->fetch_assoc()) {
            array_push($resultArray, $row);
            $rowItem = $row["Nev"];
            echo '
              <div class="row">
              <div class="col" id="item-name">
               <h3 class="item-name">' . $row["Nev"] . '</h3>
               <p class="item-uid">' . $row["UID"];
            if ($row['Status'] == '2') {
              echo ' <span class="text-warning">Jóváhagyásra vár.</span>';
            }
            echo '</p></div>';

            if ($row['Status'] != '2') {
              echo '
             <div class="col"><button class="btn btn-success " id="bringback' . $imodal . '" data-bs-toggle="modal" data-bs-target="#b' . $imodal . '">Visszahoztam</button></div>
             
             ';
              echo '
            <div class="modal fade" id="b' . $imodal . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" id="exampleModalLabel">' . $row["Nev"] . ' - ' . $row['UID'] . ' visszahozása</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <div class="modal-body">
              <input type="hidden" id="retrieveItem_' . $imodal . '" name="retrieveItem" value="' . $row["Nev"] . '"/> 
              <input type="hidden" name="User" value="' . $TKI . '"/>
              <div class="form-check">
                <input class="form-check-input intactItems" type="checkbox" value="" id="intactItems' . $imodal . '">
              </div>
              <h6></h6>
              <h6 id="emailHelp" class="form-text text-muted">A kipipálással igazolom, hogy amit visszahoztam sérülésmentes, és kifogástalanul működik. Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
                  <button type="submit" id="' . $imodal . '" onClick="reply_click(' . "'" . $row["Nev"] . "','" . $row["UID"] . "'" . ')" class="btn go_btn btn-success disabled"><i class="fas fa-solid fa-check"></i> Visszahozás</button>
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
          echo '
           </div>
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

<script>

  var single_click = true;
  //Visszahozás ellenőrzése a handlernél

  //I: Item object with UID and name
  function retrieve(i) {
    console.log("Begin retrieve by handler");
    retrieveItem_list = [i]; //required for itemManager's multiple item handling (arrays)
    $.ajax({
      method: 'POST',
      url: '../ItemManager.php',
      data: { data: JSON.stringify(retrieveItem_list), mode: "retrieveStaging" }, //JSON stringify converts the JSON to PHP-readable format.
      success: function (response) {
        if (response == 200) {
          $('.sysResponse').append('Sikeres művelet! Az oldal hamarosan újratölt.');
        }
        setTimeout(function () { location.reload(); }, 100);

      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log("Status: " + textStatus); console.log("Hiba: " + errorThrown);
      }

    });
  };

  function reply_click(clicked_name, clicked_uid) //Begyűjti a tárgy nevét, amit vissza akar a felhasználó hozni.
  {
    item = { 'name': clicked_name, 'uid': clicked_uid };
    //retrieveItem = document.getElementById('retrieveItem_' + (clicked_id)).value
    //alert(retrieveItem);
    if ($('.intactItems').is(":checked") && single_click == true) {
      retrieve(item);// AJAXos visszahozás megkezdése
    } else if (single_click == true) {
      alert('Ha probléma akad a tárggyal, jelezd azt a vezetőségnek!');
      //$( ".intactItems" ).effect( "shake" );
    }
    single_click = false;

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