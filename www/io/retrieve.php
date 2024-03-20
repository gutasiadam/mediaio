<?php

namespace Mediaio;


use Mediaio\Database;


require_once ('./Database.php');
session_start();

if (!isset ($_SESSION['userId'])) {
  header("Location: ./index.php?error=AccessViolation");
  exit();
}

include "header.php";
$SESSuserName = $_SESSION['UserUserName'];


function PhparrayCookie()
{
  array_push($selItems, $_COOKIE['id_itemNameAdd']);
  foreach ($selItems as $x) {
    echo $x . " ";
  }
}
?>
<script>
  var goStatus = 0;

</script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<html>
<?php if (isset ($_SESSION["userId"])) { ?>
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
            drawMenuItemsLeft('retrieve', menuItems);
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
        <div id="reader" width="600px"></div>
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
  <div class="container" id="retrieve-container">
    <h2 class="rainbow" id="doTitle">Visszahozás</h2>

    <!-- Announce Damage button -->
    <div class="row" id="retrieve-option-buttons">
      <button class="btn btn-danger" onclick="AnnounceDamage()">Sérülés bejelentése <i
          class="fas fa-file-alt"></i></button>
      <button type="button" class="btn btn-warning" onclick="showScannerModal()">Szkenner <i
          class="fas fa-qrcode"></i></button>
      <button type="button" class="btn btn-outline-secondary" id="manual_Retrieve"
        onclick="manualRetrieve()">Visszahozás kézzel</button>
    </div>


    <!-- THIS TABLE HOLDS THE TWO CHILDS - selectable and selected-->
    <!-- Selectable items -->
    <?php
    echo '<div class="row" id="retrieve-row">
      
      <div class="col-6" id="items-to-retrieve">
        <table class="table table-bordered table-dark dynamic-table" id="retrieve_items">';

    //Get the items that are currently by the user
    //Todo: Moves this function to the itemManager.php
    $TKI = $_SESSION['UserUserName'];
    $conn = Database::runQuery_mysqli();
    $sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI' AND Status=0");
    $result = mysqli_query($conn, $sql);
    $conn->close();
    $n = 0;
    while ($row = $result->fetch_assoc()) {
      //var_dump($row);
      $n++;
      echo '<tr id="' . $row['UID'] . '"><td class="result dynamic-field"><button disabled="true" id="' . $row['UID'] . '" class="btn btn-dark" onclick="' . "prepare(this.id,'" . $row['UID'] . "'" . ",'" . $row['Nev'] . "');" . '"' . '>' . $row['Nev'] . ' [' . $row['UID'] . ']' . ' <i class="fas fa-angle-double-right"></i></button></td></tr>';
      //echo '<div class="result dynamic-field"><button id="' . $row['UID'] . '" class="btn btn-dark" onclick="' . "prepare(this.id,'" . $row['Nev'] . "'" . ');' . '"' . '>' . $row['Nev'] . ' [' . $row['UID'] . ']' . ' <i class="fas fa-angle-double-right"></i></button></div>';
    }
    echo '</table>';
    echo '</div>';
    if ($n == 0) {
      echo '<h3 class="nothing_here">Jelenleg nincs nálad egy tárgy sem!</h3>';
    }
    ?>
    <div class="col-6">
      <table class="table table-bordered dynamic-table " style="line-height: 10px;" id="dynamic_field">
        <tr>
          <div style="text-align:center;" class="text-primary"><strong></hr></strong></div>
        </tr>
      </table>
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

</html>

<script>

  $("#toTop").click(function () {
    $("#retrieve-container").animate({
      scrollTop: $("#retrieve-container")[0].scrollHeight
    }, 700);
    $('#toTop').fadeOut();
  });

  //Preventing double click zoom
  document.addEventListener('dblclick', function (event) {
    event.preventDefault();
  }, { passive: false });

  function AnnounceDamage() {
    window.location.href = "../utility/damage_report/announce_Damage.php";
  }


  function prepare(id, uid, name, fromCookie = false) {
    $('#dynamic_field').append('<tr class="bg-success" id="prep-' + id + '"><td class="dynamic-field"><button id="prep-' + id + '" class="btn btn-succes" onclick="unstage(this.id);"><i class="fas fa-angle-double-left"></i> ' + name + ' [' + uid + ']' + '</button></td></tr>');
    $('#' + id).hide();
    $('.intactForm').css('display', 'block');
    $('#toTop').fadeIn();
    if (fromCookie == false) {
      updateSelectionCookie(id, uid, name);
    }
  }

  function unstage(id) {
    $('#' + id).remove();
    id = id.replace("prep-", "");
    $('#' + id).show();
    if ($('#dynamic_field tr').length == 1) {
      $('.intactForm').css('display', 'none');
      $('.send').hide();
      $("#intactItems").prop("checked", false);
      $('#toTop').fadeOut();
    }
    updateSelectionCookie(id, "", "", false);
  }

  // Cookie management

  function getCookie(cName) {
    const name = cName + "=";
    const cDecoded = decodeURIComponent(document.cookie); //to be careful
    const cArr = cDecoded.split('; ');
    let res;
    cArr.forEach(val => {
      if (val.indexOf(name) === 0) res = val.substring(name.length);
    })
    return res
  }

  var itemsToRetrieve = [];

  function updateSelectionCookie(id, uid, name, push = true) {
    console.log("Updated cookie");

    //Set cookie expire date to 1 day
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    //get IDs of selected items
    let item = {
      'id': id,
      'uid': uid,
      'name': name
    }
    if (push) {
      itemsToRetrieve.push(item);
    } else {
      itemsToRetrieve = itemsToRetrieve.filter(function (obj) {
        return obj.id !== id;
      });
    }
    jsonItems = JSON.stringify(itemsToRetrieve);
    document.cookie = "itemsToRetrieve=" + jsonItems + ";" + expires + ";path=/";
  }


  function reloadSavedSelections() {
    console.log("Reloading items from cookies");

    var cookie = getCookie("itemsToRetrieve");
    if (cookie == "" || cookie == undefined) {
      return;
    }
    itemsToRetrieve = JSON.parse(cookie);

    if (itemsToRetrieve != "") {
      itemsToRetrieve.forEach(item => {
        prepare(item.id, item.uid, item.name, true);
      });
    }
  }

  function clearSelectionCookie() {
    console.log("Cleared cookie");
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = "itemsToRetrieve=;" + expires + ";path=/";
  }


  // END OF cooke management
  $(document).ready(function () {
    $('.intactForm').css('display', 'none');
    // Csak akkor jelenjen meg a checkbox, ha már van Go gomb is.
    $('.send').hide();

    function startTimer(duration, display) {
      var timer = duration,
        minutes, seconds;
      setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
          timer = duration;
          window.location.href = "./utility/logout.ut.php"
        }
      }, 1000);
    }

    reloadSavedSelections();



    /*     $(document).on('click', '.result', function () {
          $('.intactForm').css('display', 'flex');
        }); */

    function allowGO() {
      if ($('#intactItems').is(":checked")) {
        $('.send').show();

      }
    }
    $(document).on('click', '#intactItems', function () {
      allowGO();
    });
    //Initiate Takeout process
    $(document).on('click', '.send', function () {
      $("#retrieve-container").animate({
        scrollTop: 0
      }, 500);
      if ($("#intactItems").prop("checked")) { // ha a felhasználó elfogadta, hogy a tárgyak rendben vannak.
        var items = []; //Items that will be retreievd.
        $('#dynamic_field > tbody  > tr > td > button ').each(function (index, tr) {
          console.log(this.innerText);

          newItem = {
            'uid': this.innerText.split('[')[1].slice(0, -1),
            'name': this.innerText.split('[')[0].trim()
          }

          //push only if items are not already in the list

          items.indexOf(newItem) === -1 ? items.push(newItem) : console.log("This item already exists");


        });
        //console.log(items);
        retrieveJSON = JSON.stringify(items);
        //console.log(retrieveJSON);
        $.ajax({
          method: 'POST',
          url: './ItemManager.php',
          data: {
            data: retrieveJSON,
            mode: "retrieveStaging"
          },
          success: function (response) {
            if (response == '200') {
              clearSelectionCookie();
              $('#doTitle').animate({
                'opacity': 0
              }, 400, function () {
                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                  'opacity': 1
                }, 400);
                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                  'opacity': 1
                }, 3000);
                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                  'opacity': 0
                }, 400);
                setTimeout(function () {
                  $("#doTitle").text(applicationTitleShort).animate({
                    'opacity': 1
                  }, 400);
                }, 3800);;
              });
              setTimeout(function () {
                location.reload();
              }, 2000);
            } else {
              $('#doTitle').animate({
                'opacity': 0
              }, 400, function () {
                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                  'opacity': 1
                }, 400);
                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                  'opacity': 1
                }, 3000);
                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                  'opacity': 0
                }, 400);
                setTimeout(function () {
                  $("#doTitle").text(applicationTitleShort).animate({
                    'opacity': 1
                  }, 400);
                  location.reload();
                }, 3800);;
              });
            }

          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus);
            alert("Error: " + errorThrown);
          }
        });
      } else {
        alert("Ha a tárggyal gond van, jelezd a vezetőségnek!");
        return;
      }
    });



    $('#submit').click(function () {
      $.ajax({
        url: "name.php",
        method: "POST",
        data: $('#add_name').serialize(),
        success: function (data) {
          alert(data);
          $('#add_name')[0].reset();
        }
      });
    });

  });


  function manualRetrieve() {
    $(".UI_loading").fadeIn("fast");
    let useritems = <?php echo $n ?>;


    if ($('#manual_Retrieve').hasClass('btn-outline-secondary')) {
      console.log("Checked");
      for (j = 0; j < useritems; j++) {
        $('#retrieve_items').find('tr').eq(j).find('button').prop('disabled', false);
      }
      $('#manual_Retrieve').removeClass('btn-outline-secondary');
      $('#manual_Retrieve').addClass('btn-secondary');

    } else {
      console.log("Unchecked");
      for (j = 0; j < useritems; j++) {
        $('#retrieve_items').find('tr').eq(j).find('button').prop('disabled', true);
      }
      $('#manual_Retrieve').removeClass('btn-secondary');
      $('#manual_Retrieve').addClass('btn-outline-secondary');
    }
  }


  //Scanner

  const qrOnSuccess = (decodedText, decodedResult) => {
    console.log(`Code matched = ${decodedText}`, decodedResult);

    //Check if the scanned item is in the list
    let useritems = <?php echo $n ?>;
    let itemFound = false
    for (j = 0; j < useritems; j++) {
      if (decodedText == $('#retrieve_items').find('tr').eq(j).attr('id')) {
        //Check if the item is already in the list
        if ($('#retrieve_items').find('tr').eq(j).css('display') == 'none') {
          showToast(decodedText + " - már visszaadtad!", "red");
          console.log("Not available!");
          scan_fail_sfx.play();
          itemFound = true;
          return;
        } else {
          showToast(decodedText, "green");
          prepare(decodedText, decodedText, $('#retrieve_items').find('tr').eq(j).find('button').text().split('[')[0].trim());
          console.log("Prepared!");
          showToast(decodedText, "green");
          scan_succes_sfx.play();
          itemFound = true;
          return;
        }
      }
    }
    if (itemFound == false) {
      showToast("Ez az eszköz nincs nálad!", "red");
      scan_fail_sfx.play();
      console.log("Not available!");
    }
  };


</script>
<script src="utility/qr_scanner/io_qr_scanner.js" type="text/javascript"></script>

<?php //Message handler
if (isset ($_GET['state'])) {
  if ($_GET['state'] == "Success") {
    echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-success"><strong>Retrieve - </strong>Sikeresen bekerültek a tárgyak a raktárba.</div></tr></td></table>';
  }
}
?>