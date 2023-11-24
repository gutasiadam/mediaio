<?php

namespace Mediaio;

use Mediaio\Databse;

require_once __DIR__ . '/../Database.php';
include "./header.php";
session_start();
if (in_array("system", $_SESSION["groups"]) || in_array("teacher", $_SESSION["groups"])) {

  error_reporting(E_ALL ^ E_NOTICE);


?>

  <html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="utility/timeline.min.js"></script>
    <link rel="stylesheet" href="../utility/pathfinder.css" />
    <link rel="stylesheet" href="../utility/timeline.min.css" />
    <script src="../utility/jquery.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"> </script>
    <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <title>Pontok</title>

  </head>
  <?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php">
        <img src="../utility/logo2.png" height="50">
      </a>
      <!-- Breadcrumb for mobilne navigation -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto navbarUl">
          <script>
            $(document).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft('profile', menuItems, 2);
            });
          </script>
        </ul>
        <ul class="navbar-nav navbarPhP">
          <li>
            <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' ' . $_SESSION['UserUserName']; ?>
            </a>
          </li>
        </ul>
        <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
          <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
        </form>
        <a class="nav-link my-2 my-sm-0" href="./help.php">
          <i class="fas fa-question-circle fa-lg"></i>
        </a>
      </div>
    </nav> <?php  } ?>

  <body>
    <div class="container">
      <br />
      <h1 id="mainTitle" align="center">Ponttábla</h1><br>

      <!--Temporary development modal-->
      <div class="modal fade" id="WIPModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Fejlesztés alatt</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <h5>
                <p>Kedves felhasználó!</p>
              </h5>
              <p>Az oldal feljesztése folyamatban van, kérlek ne használd ezt a funkciót.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="panel panel-default">
          <div class="panel-heading">
          <?php
          $TKI = $_SESSION['UserUserName'];
          $sql = ("SELECT * FROM `users` ORDER BY `UserPoints` DESC");
          $result = Database::runQuery($sql);
          $imodal = 0;
          $resultArray = [];
          $pointsData = [];

          while ($row = $result->fetch_assoc()) {
            array_push($resultArray, $row);
            $rowItem = $row["firstName"] . $row["lastName"];
            $tempArray = [$row["firstName"], $row["lastName"], $row["UserPoints"]];
            array_push($pointsData, $tempArray);
            echo '
                <div class="row">
                <div class="col-4">
                  <h6 class="nospace">' . $row["lastName"] . '</h6>
                  <h2>' . $row["firstName"] . '</h2>
                </div>
                <div class="col-4 border-left border-right">';

            if ($row["UserPoints"] == 0) {
              echo '<h2 class="text text-warning">' . $row["UserPoints"] . '</h2>';
            } else if ($row["UserPoints"] < 0) {
              echo '<h2 class="text text-danger">' . $row["UserPoints"] . '</h2>';
            } else {
              echo '<h2>' . $row["UserPoints"] . '</h2>';
            }
            echo '<p>BZs</p>
                </div>';
            echo '<div class="col-1"><button class="btn btn-dark " id="auth' . $imodal . '" data-toggle="modal" data-bs-target="#a' . $imodal . '">Módosítás</button></div>
               </div>';

            echo
            '<div class="modal fade" id="a' . $imodal . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pontszámok módosítása ' . $row["usernameUsers"] . ' számára</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="./points.php" class="form-group" method=post>
                <h4>Pontszám</h4> 
                <input type="number" step="0.5" class="form-control" id="PValue' . $imodal . '" name="points" value=""/> 
                <input type="hidden" class="form-control" name="user" id="modalnum' . $imodal . '" value="' . $row["usernameUsers"] . '"/>
                <input type="hidden" class="form-control" name="user" id="user' . $imodal . '" value="' . $row["usernameUsers"] . '"/>
                <input type="hidden" class="form-control" name="currentpoints" id="currentpoints' . $imodal . '" value="' . $row["UserPoints"] . '"/> 
                <h6 id="emailHelp" class="form-text text-muted"> <strong>Negatív és nem egész számot is beírhatsz</strong> ( pl.: -3.5 )<br>A gomb lenyomása után töltsd újra az oldalt, hogy a ponttábla frissüljön!</h6>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
                    <button type="submit" id="authToggle" class="btn btn-success authToggle" value=' . $imodal . ' onclick="submitData(this.value)">Módosítás</button>
                    </form>
              </div>
                    </div>      
                </div>
              </div>';
            $imodal++;
          }
        } else {
          echo '<h1 class="text text-danger">Nincs jogosultságod az oldal megtekintéséhez!</h1>';
        }

          ?>
  </body>

  </html>
  <style>
    .timeline__item {
      background-color: #ededed;
    }

    .nospace {
      padding-bottom: 1px;
      margin: 0px;
    }
  </style>

  <script>
    function submitData(val) {
      //alert(val);
      var pointUpdate = document.getElementById('PValue' + val).value;
      var userUpdate = document.getElementById('user' + val).value;
      var pointsCurrent = document.getElementById('currentpoints' + val).value;
      //alert(pointUpdate+userUpdate+pointsCurrent);
      var newScore = (parseFloat(pointUpdate) + parseFloat(pointsCurrent)).toFixed(2);
      //alert(newScore);
      $.ajax({
        type: 'POST',
        url: "./pointUpdate.php",
        data: {
          newScore: parseFloat(newScore),
          userUpdate: userUpdate
        },
        success: function(response) {
          document.getElementById("mainTitle").innerHTML = "Ponttábla";
        }, //window.location.href = './takeout.php?state=Success';;
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          //alert("Status: " + textStatus); alert("Error: " + errorThrown); 
        }
      });
    }

    window.onload = function() {
      $('#WIPModal').modal();
      display = document.querySelector('#time');
      var timeUpLoc = "../utility/userLogging.php?logout-submit=y"
      startTimer(display, timeUpLoc);
    };

    /*$(document).on('click', '.authToggle', function(){
      setTimeout(function(){ window.location.href = "./utility/logout.ut.php"; }, 3000);});*/
  </script>