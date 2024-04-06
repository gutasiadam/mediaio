<?php

use Mediaio\Core;
use Mediaio\Database;
use Mediaio\MailService;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Core.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Mailer.php';
setcookie("Cookie_eventId", $_GET['eventId'], time() + (86400 * 30), "/");
?>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <link href='../style/events.css' rel='stylesheet' />
</head>
<?php
if (isset($_SESSION['UserUserName'])) {

  if (isset($_GET['eventId'])) {
    //Get Event Data
    // Retrieve the event using the events->get() method of the Google Calendar API

    putenv('GOOGLE_APPLICATION_CREDENTIALS=./../utility/credentials.json'); // beállítjuk az elérési útvonalat a credentials.json fájlhoz
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/calendar']); // beállítjuk a szükséges jogosultságokat
    $client->setAccessType('offline');
    // Létrehozunk egy Google_Service_Calendar objektumot a Google Calendar API-hoz való hozzáféréshez
    $service = new Google_Service_Calendar($client);
    $calendarId = 'jjpdv8bd3u2s2hj9ehnbh19src@group.calendar.google.com';
    $eventId = ($_GET['eventId']);
    // Retrieve the event using the events->get() method of the Google Calendar API
    try {
      $event = $service->events->get($calendarId, $eventId);
    } catch (Google_Service_Exception $e) {
      try {
        $event = $service->events->get('hq37buvra0ju1sci457sk66pfk@group.calendar.google.com', $eventId);
      } catch (Google_Service_Exception $e) {
        echo '<h2 class="mb-2 mr-sm-2" id="titleString">Nem található esemény ezzel az azonosítóval.</h2>';
        //Add a button that returns to calendar page
        echo '<a href="index.php"><button class="btn btn-info noprint mb-2 mr-sm-2" data-toggle="modal" data-bs-target="<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>">Vissza a naptárra</button></a>';
        exit();
      }
    }

    $connect = Database::runQuery_mysqli();
    //Esemény címénak, egyéb adatainak megtalálása és eltárolása
    $query = "SELECT * from events WHERE id = '$eventId'";
    //execute query and return the result in a for loop
    $result = $connect->query($query);
    foreach ($result as $row) {
      $eventName = $row[1];
      $eventStart = $row[2];
      $eventEnd = $row[3];
    }
    echo '


<title>Munkalap - ' . $event->getSummary() . '</title>

<body class="px-3 py-3">
<h2 class="mb-2 mr-sm-2" id="titleString">Munkalap - <strong>' . $event->getSummary() . '</strong>  <button type="button" class="btn btn-success noprint mb-2 mr-sm-2" data-bs-toggle="modal" data-bs-target="#addWorkSheetData">
<i style="font-size: 30px;" class="fas fa-plus fa-2x"></i>
</button>
</h2><h6>Kezdés: ' . $event->getStart()->getDateTime() . " | Befejezés: " . $event->getEnd()->getDateTime() . '</h6>
<table id="worksheetData" class="table">
<tr><th>Név</th><th>Cselekvés</th><th>Hely</th><th>Megjegyzés</th><th class="noprint">Műveletek</th><tr>';

    $query = "SELECT * from worksheet WHERE EventId = '$eventId' ORDER BY RecordDate ASC";
    $result = $connect->query($query);

    foreach ($result as $row) {
      $workID = $row["ID"];
      $userName = $row["FullName"];
      $workType = $row["Worktype"];
      $Location = $row["Location"];
      $userComment = $row["Comment"];
      $recordDate = $row["RecordDate"];
      $sharingLink = $row['Link'];

      if ($userName == $_SESSION['fullName']) {
        echo '<tr><td>' . $userName . '</td><td>' . $workType . '</td><td><a href="' . $sharingLink . '">' . $Location . '</td><td></a>' . $userComment . '</td><td class="noprint"> ';
        printf('<a id="editLink" href= "#" onClick="showDetails(\'%s\',\'%s\',\'%s\',\'%s\');">%s</a> ', $workID, $workType, $Location, $userComment, '<i class="far fa-lg fa-edit"></i></a>');
        printf('<a id="deleteLink" href= "#" onClick="deleteSheet(\'%s\');">%s</a> ', $workID, '<font color="red"><i class="far fa-lg fa-times-circle"></i></font>');
        echo '</td></a></tr>';
      } else {
        echo '<tr><td>' . $userName . '</td><td>' . $workType . '</td><td>' . $Location . '</td><td>' . $userComment . '</td></tr>';
      }
    } ?>
    </table>

    <div class="modal fade" id="addWorkSheetData" tabindex="-1" role="dialog" aria-labelledby="worksheetDataLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="worksheetDataLabel">Adatpont hozzáadása a munkalaphoz</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          <form id="sendAddWorkData">
              <div class="form-group">
                <div class="form-group">
                  <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="workTypeSelect" placeholder="Elvégzett feladat" required></input></div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="serverFileSwitch">
                    <label class="form-check-label" for="serverFileSwitch">Fájl tartozik hozzá</label>
                  </div>

                  <div class="input-group mb-3 folderSelectSection">
                    <div class="form-group">
                      <label for="fileLocation">Fájl helye a szerveren</label>
                      <input class="form-control" type="text" autocomplete="off" id="fileLocation" placeholder="A fájlok helye a szerveren"></input>
                    </div>
                      <input id="folderSelectFieldInput" list="folderSelectField" oninput="onFolderSelectFieldInput()"></input>
                        <datalist id="folderSelectField"></datalist>
                          <button type="button" onclick="updateFolderData()"><i class="fas fa-folder-open"></i></button>
                          <span id="loadingData">Fájlok lekérése folyamatban...</span>
                      <input class="form-control" type="hidden" id="upDateId" value="NUL"></input>
                  </div>
                  <div class="form-group">
                    <input class="form-control" type="text" autocomplete="off" id="userComment" placeholder="Egyéb megjegyzés (nem kötelező)"></input>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
                  <input type="submit" class="btn btn-primary" value="Hozzáadás"></button>
                </div>
                <input class="form-control" type="hidden" id="upDateId" value="NUL"></input>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editWorkSheetData" tabindex="-1" role="dialog" aria-labelledby="worksheetDataLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="worksheetDataLabel">Adatpont szerkesztése</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          <form id="sendEditWorkData">
              <div class="form-group">
                <div class="form-group">
                  <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="workTypeSelect_edit" placeholder="Elvégzett feladat" required></input></div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="serverFileSwitch_edit">
                    <label class="form-check-label" for="serverFileSwitch_edit">Fájl tartozik hozzá</label>
                  </div>

                  <div class="input-group mb-3 folderSelectSection_edit">
                    <div class="form-group">
                      <label for="fileLocation_edit">Fájl helye a szerveren</label>
                      <input class="form-control" type="text" autocomplete="off" id="fileLocation_edit" placeholder="A fájlok helye a szerveren"></input>
                    </div>
                      <input id="folderSelectFieldInput_edit" list="folderSelectField_edit" oninput="onFolderSelectFieldInput(true)"></input>
                        <datalist id="folderSelectField_edit"></datalist>
                          <button type="button" onclick="updateFolderData(true)"><i class="fas fa-folder-open"></i></button>
                          <span id="loadingData_edit">Fájlok lekérése folyamatban...</span>
                      <input class="form-control" type="hidden" id="upDateId" value="NUL"></input>
                  </div>
                  <div class="form-group">
                    <input class="form-control" type="text" autocomplete="off" id="userComment_edit" placeholder="Egyéb megjegyzés (nem kötelező)"></input>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
                  <input type="submit" class="btn btn-warning" value="Szerkesztés"></button>
                </div>
                <input class="form-control" type="hidden" id="upDateId" value="NUL"></input>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


    </body>

    </html><?php }
        } else {
          header("Location: ../index.php?error=AccessViolation");
        } ?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Wait for the document to be fully loaded
    var serverFileSwitch = document.getElementById("serverFileSwitch");

    // Add an event listener to detect changes in the checkbox state
    serverFileSwitch.addEventListener("change", function() {
      // Your code to handle the checkbox state change goes here
      if (serverFileSwitch.checked) {
        // Checkbox is checked
        console.log("Server File Switch is checked");
        document.getElementsByClassName("folderSelectSection")[0].style.display = "block";
      } else {
        // Checkbox is unchecked
        console.log("Server File Switch is unchecked");
        document.getElementsByClassName("folderSelectSection")[0].style.display = "none";
      }
    });
  });


    return new Promise((resolve, reject) => {
      generateShareLink(folderName, date)
        .then(sharingLink => {
          // Continue with the rest of your code using the sharingLink
          // For example:
          if (sharingLink !== null) {
            // Do something with the sharingLink
            resolve(sharingLink);
          } else {
            // Handle the case where there was an error generating the link
            reject("Error obtaining sharing link");
          }
        })
        .catch(error => {
          // Handle errors thrown during the async operation
          console.error("Error obtaining sharing link:", error);
          reject(error);
        });
    });
  }



  $('#addWorkSheetData').on('su
  document.addEventListener("DOMContentLoaded", function() {
    // Wait for the document to be fully loaded
    var serverFileSwitch = document.getElementById("serverFileSwitch_edit");

    // Add an event listener to detect changes in the checkbox state
    serverFileSwitch.addEventListener("change", function() {
      // Your code to handle the checkbox state change goes here
      if (serverFileSwitch.checked) {
        // Checkbox is checked
        console.log("Server File Switch is checked");
        document.getElementsByClassName("folderSelectSection_edit")[0].style.display = "block";
      } else {
        // Checkbox is unchecked
        console.log("Server File Switch is unchecked");
        document.getElementsByClassName("folderSelectSection_edit")[0].style.display = "none";
      }
    });
  });

  // Hide folder edit selctions on load.
  $(document).ready(function() {
    document.getElementsByClassName("folderSelectSection_edit")[0].style.display = "none";
    document.getElementsByClassName("folderSelectSection")[0].style.display = "none";
  });

  function obtainSharingLink(folderName, date) {bmit', async function(e) {
    e.preventDefault();
    $('#addWorkSheetData').modal('hide');
    var wType = $("#workTypeSelect").val();
    
    var wLoc = document.getElementById('fileLocation').value;
    if (wLoc == null) {
      wLoc = "";
    }


    var wComment = document.getElementById('userComment').value;
    if (wComment == null) {
      wComment = "";
    }
    

    var wEvent = getCookie("Cookie_eventId");

    var linkDataPromise = null;

    // If fileselect section is visible, generate sharing link
    if (document.getElementsByClassName("folderSelectSection")[0].style.display == "block") {
      var folder = document.getElementById("fileLocation").value;
      var fileName = wType;
      var wEvent = getCookie("Cookie_eventId");

      document.getElementById("titleString").innerHTML = "Megosztási link generálása...";

      try {
        linkDataPromise = obtainSharingLink(folder, "dateString");
        var linkData = await linkDataPromise;

        // Check if linkData has the expected structure
        if (linkData && linkData.data && linkData.data.links && linkData.data.links.length > 0) {
          var linkUrl = linkData.data.links[0].url;
          console.log(linkUrl);
        } else {
          // Handle the case where linkData doesn't have the expected structure or no link is provided
          console.error("Unexpected linkData structure or no link provided:", linkData);
          var linkUrl = ''; // Set an empty string as the link
        }
      } catch (error) {
        // Handle the case where there was an error obtaining the sharing link
        console.error(error);
      }
    } else {
      // If fileselect section is invisible, set an empty string as the link
      var linkUrl = '';
      wLoc = '';
    }

    $.ajax({
      type: "POST",
      url: 'wHandler.php',
      data: {
        wType: wType,
        wLoc: wLoc,
        wComment: wComment,
        wEvent: wEvent,
        link: linkUrl
      },
      success: function(successNum) {
        //alert(successNum);
        window.location.href = "./worksheet.php?eventId=" + wEvent;
      },
      error: function(jqXHR, textStatus, errorThrown) {
        window.location.href = "./worksheet.php?eventId=" + wEvent;
      }
    });
  });




  function showDetails(id, type, loc, comment) {
    console.log(id, type, loc, comment);
    document.getElementById('upDateId').value = id;
    document.getElementById('workTypeSelect_edit').value = type;
    document.getElementById('fileLocation_edit').value = loc;
    document.getElementById('userComment_edit').value = comment;
  

    $('#editWorkSheetData').modal('show');
  }

  function deleteSheet(id) {
    var wEvent = getCookie("Cookie_eventId");
    document.getElementById("titleString").innerHTML = "Adatpont törlése folyamatban...";
    $.ajax({
      type: "POST",
      url: 'wHandler.php',
      data: {
        deleteId: id
      },
      success: function(successNum) {
        // alert(successNum);
        window.location.href = "./worksheet.php?eventId=" + wEvent;

      },
      error: function(jqXHR, textStatus, errorThrown) {
        window.location.href = "./worksheet.php?eventId=" + wEvent;
      }
    })
  }

  function updateFolderData(isEdit = false) {
    var editSuffix="";
    if (isEdit){
      editSuffix = "_edit";
    }
    document.getElementById("loadingData"+editSuffix).innerHTML = "<div class='spinner-border text-primary' role='status'></div>";
    folder = "Munka"
    if (document.getElementById("fileLocation"+editSuffix).value == "") {
      folder = "Munka"
    } else {
      folder = document.getElementById("fileLocation"+editSuffix).value
    }
    console.log(folder);
    getFolderData(folder)
      .then(data => {
        data;
        document.getElementById("loadingData"+editSuffix).innerHTML = "";
        console.log(data.body)

        //Clear datalist
        document.getElementById("folderSelectField"+editSuffix).innerHTML = "";

        //handle directory error.
        if (data.data.files == undefined) {
          document.getElementById("loadingData"+editSuffix).innerHTML = "Nem létező útvonal!";
          return;
        }
        data.data.files.forEach(element => {
          console.log(element.name);
          //Add every element to folderSelectField
          var option = document.createElement("option");
          option.value = element.name;
          document.getElementById("folderSelectField"+editSuffix).appendChild(option);
        });

        document.getElementById("loadingData"+editSuffix).innerHTML = "";
      })
  }



  $('#addWorkSheetData').on('show.bs.modal', function(event) {
    document.getElementById("fileLocation").value = "Munka"
    updateFolderData();
  })

  $('#editWorkSheetData').on('show.bs.modal', function(event) {
    document.getElementById("fileLocation_edit").value = "Munka"
    updateFolderData(true);
  })


  

  $('#sendEditWorkData').on('submit', async function(e) {
    e.preventDefault();
    $('#editModal').modal('hide');
    var uType = $("#workTypeSelect_edit").val();
    var uLoc = document.getElementById('fileLocation_edit').value;
    var uComment = document.getElementById('userComment_edit').value;
    var uId = document.getElementById('upDateId').value
    var wEvent = getCookie("Cookie_eventId");
    document.getElementById("titleString").innerHTML = "Adatpont módosítása folyamatban...";


    var linkDataPromise = null;

    // If fileselect section is visible, generate sharing link
    if (document.getElementsByClassName("folderSelectSection_edit")[0].style.display == "block") {
      var folder = document.getElementById("fileLocation_edit").value;
      var wEvent = getCookie("Cookie_eventId");

      document.getElementById("titleString").innerHTML = "Megosztási link generálása...";

      try {
        linkDataPromise = obtainSharingLink(folder, "dateString");
        var linkData = await linkDataPromise;

        // Check if linkData has the expected structure
        if (linkData && linkData.data && linkData.data.links && linkData.data.links.length > 0) {
          var linkUrl = linkData.data.links[0].url;
          console.log(linkUrl);
        } else {
          // Handle the case where linkData doesn't have the expected structure or no link is provided
          console.error("Unexpected linkData structure or no link provided:", linkData);
          var linkUrl = ''; // Set an empty string as the link
        }
      } catch (error) {
        // Handle the case where there was an error obtaining the sharing link
        console.error(error);
      }
    } else {
      // If fileselect section is invisible, set an empty string as the link
      var linkUrl = '';
      uLoc = '';
    }

    $.ajax({
      type: "POST",
      url: 'wHandler.php',
      data: {
        uType: uType,
        uLoc: uLoc,
        uComment: uComment,
        uId: uId,
        linkUrl: linkUrl
      },
      success: function(successNum) {
        window.location.href = "./worksheet.php?eventId=" + wEvent;
      },
      error: function(jqXHR, textStatus, errorThrown) {
        window.location.href = "./worksheet.php?eventId=" + wEvent;
      }
    })
  });

  function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1);
      if (c.indexOf(name) == 0)
        return c.substring(name.length, c.length);
    }
    return "";
  }

  //When a datalist item is selected.

  function onFolderSelectFieldInput(isEdit = false) {
    var editSuffix="";
    if (isEdit){
      editSuffix = "_edit";
    }
    var val = document.getElementById("folderSelectFieldInput"+editSuffix).value;
    var opts = document.getElementById('folderSelectField'+editSuffix).children;
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].value === val) {
        // An item was selected from the list!
        // yourCallbackHere()
        //alert(opts[i].value);
        document.getElementById('fileLocation'+editSuffix).value += "/" + opts[i].value;
        document.getElementById("folderSelectFieldInput"+editSuffix).value = '';
        break;
      }
    }
  }

  function onEditFolderSelectFieldInput() {
    var val = document.getElementById("folderEditSelectFieldInput"+editSuffix).value;
    var opts = document.getElementById('folderEditSelectField'+editSuffix).children;
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].value === val) {
        // An item was selected from the list!
        // yourCallbackHere()
        //alert(opts[i].value);
        document.getElementById('fileLocation_edit'+editSuffix).value += "/" + opts[i].value;
        document.getElementById("folderEditSelectField"+editSuffix).value = '';
        break;
      }
    }
  }
</script>
<script src="apiCommunications.js"></script>
