<?php
session_start();
if (!isset($_SESSION["userId"])) {
  header("Location: ../index.php?error=AccessViolation");
  exit();
}
error_reporting(E_ALL | E_WARNING | E_NOTICE);
require_once("./header.php");
?>
<html>
<script src="../utility/_initMenu.js" crossorigin="anonymous"></script>
<script>
  $(document).ready(function() {
    menuItems = importItem("../utility/menuitems.json");
    drawMenuItemsLeft("maintenance", menuItems, 2);
    drawMenuItemsRight('maintenance', menuItems, 2);
  });
</script>
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
      <img src="../utility/logo2.png" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
      </ul>
      <ul class="navbar-nav ms-auto navbarPhP">
        <li>
          <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' ' . $_SESSION['UserUserName']; ?>
          </a>
        </li>
      </ul>
      <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
        <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      </form>
    </div>
  </nav> <?php  } ?>
<br>
<h1 align=center class="rainbow">Takarítási rend, feladatok: </h1>

  <div class="tableParent">
    <div class="form-check">
      <!--<input class="form-check-input noprint" type="checkbox" value="" id="showOnlyMyTasks_checkBox" data-toggle="toggle">
  <label class="form-check-label noprint" for="defaultCheck1"> Csak a saját feladataimat mutasd</label>-->
    </div>
    <?php
    if ((in_array("admin", $_SESSION["groups"]))) {
      echo '<table class="maintanence-admin">
              <tr><td><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add_Work_Modal">Új feladat</button> 
              <input type="checkbox" id="showOldTasks" name="showOldTasks" value="true"><label id="old_tasks" for="vehicle1">Régebbi feladatok</label></td>
              </table>';

    } ?>

    <ul style="margin-left:5%; padding-right: 5px;">
      <li>Szemét kiürítése</li>
      <li>Felsöprés</li>
      <li>Elmosogatás</li>
      <li>Porszívózás a stúdióban</li>
      <li>Felmosás (a tárgyalóban minimális vízzel)</li>
      <li>Rendrakás</li>
    </ul>

    <table class="takaritasirend" id="takaritasirend">



    </table>
</body>

</html>


<div class="modal" tabindex="-1" role="dialog" id="add_Work_Modal" data-backdrop="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Új feladat hozzáadása</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="add_Work_Form">
          <div class="form-group">
            <label for="work_Date">Dátum</label>
            <input type="date" class="form-control" id="work_Date" aria-describedby="emailHelp"
              placeholder="Dátum. ÉV/HÓ/NAP formátumban">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <div id="processing">Feldolgozás..</div>
        <button type="button" class="btn btn-success send_Work_update" onclick=addWork()>Mentés</button>
        <button type="button" class="btn btn-danger clear_Update" data-dismiss="modal">Mégsem</button>
      </div>
    </div>
  </div>
</div>


<script>
  //Ha nincs feladat, ne is jelenjen meg a táblázat:
  window.onload = function () {
    display = document.querySelector('#time');
    var timeUpLoc = "../utility/userLogging.php?logout-submit=y"
    startTimer(display, timeUpLoc);
    renderWork();
    $('#processing').hide();
  };

  function applyToWork(ID) {
    $.ajax({
      url: "maintenanceManager.php",
      type: "POST",
      async: true,
      data: { method: "apply", workID: ID },
      success: function (result) {
        if (result == 200) {
          location.reload();
        }
        if (result == 201) {
          $('#tr' + ID).css('color', 'red');
          $('#tr' + ID).find("td:eq(0)").html("Már jelentkeztél!");
        }
        if (result == 201) {
          $('#tr' + ID).css('color', 'red');
          $('#tr' + ID).find("td:eq(0)").html("Már jelentkeztél!");
        }
        if (result == 202) {
          $('#tr' + ID).css('color', 'red');
          $('#tr' + ID).find("td:eq(0)").html("Nincs szabad hely!");
        }
        if (result == 202) {
          $('#tr' + ID).css('color', 'red');
          $('#tr' + ID).find("td:eq(0)").html("Nincs szabad hely!");
        }
      }
    });
  }
  function addWork() {
    var Date = $('#work_Date').val();
    $.ajax({
      url: "maintenanceManager.php",
      type: "POST",
      async: true,
      data: { method: "add", Date: Date },
      success: function (result) {
        if (result == 200) {
          location.reload();
        }
      }
    });
  }
  function deleteWork(ID) {
    var Date = $('#work_Date').val();
    $.ajax({
      url: "maintenanceManager.php",
      type: "POST",
      async: true,
      data: { method: "delete", workID: ID },
      success: function (result) {
        if (result == 200) {
          location.reload();
        }
      }
    });
  }
  function deleteUserFromWork(ID, userN) {
    console.log(ID + userN);
    var Date = $('#work_Date').val();
    $.ajax({
      url: "maintenanceManager.php",
      type: "POST",
      async: true,
      data: { method: "deleteUser", workID: ID, user: userN },
      success: function (result) {
        if (result == 200) {
          location.reload();
        } else {
          console.log(result)
        }
      }
    });
  }
  //if showOldTasks is changed, rerender the table
  $('#showOldTasks').change(function () {
    renderWork();
  });


  function modifyStatus(ID) {
    var s1 = $('#tr' + ID).find("td:eq(2)").find(":selected").val();
    var s2 = $('#tr' + ID).find("td:eq(4)").find(":selected").val();
    $.ajax({
      url: "maintenanceManager.php",
      type: "POST",
      async: true,
      data: { method: "modify", workID: ID, status1: s1, status2: s2 },
      success: function (result) {
        if (result == 200) {
          location.reload();
        } else {
          console.log(result)
        }
      }
    });
  }

  function renderWork() {
    var getOldTasks = false;
    //if showOldTasks is checked, set getOldTasks to true
    if ($('#showOldTasks').is(":checked")) {
      getOldTasks = true;
    }
    $.ajax({
      url: "maintenanceManager.php",
      type: "POST",
      async: true,
      data: { method: "get", getOldTasks: getOldTasks },
      success: function (result) {
        //clear .takaritasirend table
        $('.takaritasirend').empty();
        result = JSON.parse(result);
        if (result[0] == "Admin") {
          $('.takaritasirend').append('<tr><th>Dátum</th><th>1. Személy</th><th>Státusz</th><th>2. Személy</th><th>Státusz</th><th>Eszközök</th></tr>');
          result[1].forEach(element => {
            //console.log(element);

            switch (element['szemely1_Status']) {
              case 'Y':
                SZ1Status = '<select name="szemelyStatus"><option value="Y" selected>OK</option><option value="N">Nem végezte el</option><option value="B">Beteg</option><option value="E">Egyéb</option></select>'
                break;
              case 'N':
                SZ1Status = '<select name="szemelyStatus"><option value="Y">OK</option><option value="N" selected>Nem végezte el</option><option value="B">Beteg</option><option value="E">Egyéb</option></select>'
                break;
              case 'B':
                SZ1Status = '<select name="szemelyStatus"><option value="Y">OK</option><option value="N">Nem végezte el</option><option value="B" selected>Beteg</option><option value="E">Egyéb</option></select>'
                break;
              case 'E':
                SZ1Status = '<select name="szemelyStatus"><option value="Y">OK</option><option value="N">Nem végezte el</option><option value="B">Beteg</option><option value="E" selected>Egyéb</option></select>'
                break;
              default:
                SZ1Status = '❓';
                break;
            }
            switch (element['szemely2_Status']) {
              case 'Y':
                SZ2Status = '<select name="szemelyStatus"><option value="Y" selected>OK</option><option value="N">Nem végezte el</option><option value="B">Beteg</option><option value="E">Egyéb</option></select>'
                break;
              case 'N':
                SZ2Status = '<select name="szemelyStatus"><option value="Y">OK</option><option value="N" selected>Nem végezte el</option><option value="B">Beteg</option><option value="E">Egyéb</option></select>'
                break;
              case 'B':
                SZ2Status = '<select name="szemelyStatus"><option value="Y">OK</option><option value="N">Nem végezte el</option><option value="B" selected>Beteg</option><option value="E">Egyéb</option></select>'
                break;
              case 'E':
                SZ2Status = '<select name="szemelyStatus"><option value="Y">OK</option><option value="N">Nem végezte el</option><option value="B">Beteg</option><option value="E" selected>Egyéb</option></select>'
                break;
              default:
                SZ2Status = '❓';
                break;
            }


            $('.takaritasirend').append('<tr id=tr' + element['id'] + '><td>' + element['datum'] + '</td><td>' + element['szemely1'] + '</td><td>' +
              SZ1Status + '</td><td>' + element['szemely2'] + '</td><td>' + SZ2Status + '</td><td><button class="btn btn-warning" onclick=modifyStatus(' + element['id'] + ')>Módosít</button> <button class="btn btn-success" onclick=applyToWork(' + element['id'] + ')>Jelentkezem</button> <button class="btn btn-danger" onclick=deleteWork(' + element['id'] + ')>Törlés</button></td></tr>');
            if (element['szemely1'] != null) { $('#tr' + element['id']).find("td:eq(1)").append(' <button class="btn btn-danger" style="margin-left: 10px; margin-right: 5px;" onclick=deleteUserFromWork(' + element['id'] + ',1)>X</button>') }
            if (element['szemely2'] != null) { $('#tr' + element['id']).find("td:eq(3)").append(' <button class="btn btn-danger" style="margin-left: 10px; margin-right: 5px;" onclick=deleteUserFromWork(' + element['id'] + ',2)>X</button>') }
          });
        } else {
          $('.takaritasirend').append('<tr><th>Dátum</th><th>1. Személy</th><th>2. Személy</th></tr>');

          result[0].forEach(element => {
            console.log(element);
          result[0].forEach(element => {
            console.log(element);

            if (element['szemely1'] == null) {
              element['szemely1'] = "<button style='display: block; margin: auto;' class='btn btn-success' onclick=applyToWork(" + element['id'] + ")>Jelentkezés</button>"
            } else {

            }
            if (element['szemely2'] == null) {
              element['szemely2'] = "<button style='display: block; margin: auto;' class='btn btn-success' onclick=applyToWork(" + element['id'] + ")>Jelentkezés</button>"
            } else {

            }

            $('.takaritasirend').append('<tr id=tr' + element['id'] + '><td>' + element['datum'] + '</td><td>' + element['szemely1'] + '</td><td>' + element['szemely2'] + '</td></tr>');
          });
            if (element['szemely1'] == null) {
              element['szemely1'] = "<button style='display: block; margin: auto;' class='btn btn-success' onclick=applyToWork(" + element['id'] + ")>Jelentkezés</button>"
            } else {

            }
            if (element['szemely2'] == null) {
              element['szemely2'] = "<button style='display: block; margin: auto;' class='btn btn-success' onclick=applyToWork(" + element['id'] + ")>Jelentkezés</button>"
            } else {

            }

            $('.takaritasirend').append('<tr id=tr' + element['id'] + '><td>' + element['datum'] + '</td><td>' + element['szemely1'] + '</td><td>' + element['szemely2'] + '</td></tr>');
          });
        }
      }
    });
  }


</script>