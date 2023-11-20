<?php
use Mediaio\Core;
use Mediaio\Database;
use Mediaio\MailService;
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../Core.php';
require_once __DIR__.'/../Database.php';
require_once __DIR__.'/../Mailer.php';
setcookie("Cookie_eventId", $_GET['eventId'], time() + (86400 * 30), "/");
?>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
</head>
<?php

if(isset($_POST["uId"]) && isset($_SESSION['UserUserName']))
{
 if(!isset($_POST['uComment'])){$uComment="";}else{$uComment=$_POST['uComment'];}
  $connect=Database::runQuery_mysqli();
 
  $query = "
  UPDATE worksheet
  SET Comment=?, Location=?, Worktype=?
  WHERE ID=?";

  //bind params to query
  $stmt = $connect->prepare($query);
  $stmt->bind_param("ssss", $uComment, $_POST['uLoc'], $_POST['uType'], $_POST['uId']);
  $result = $stmt->execute();


 if($result){
     echo "1";
 }
 else{
     echo "2";
 }
 exit();
}

if(isset($_POST["deleteId"])){
  $WorkId=$_POST["deleteId"];
  $query = "DELETE from worksheet WHERE ID='$WorkId'";
  $connect=Database::runQuery($query);
  // $statement = $connect->prepare($query);
  // $statement->execute();
  // $result = $statement->fetchAll();
}

if(isset( $_SESSION['UserUserName'])){

if(isset($_GET['eventId'])){
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
    $eventId= ($_GET['eventId']);
    // Retrieve the event using the events->get() method of the Google Calendar API
    try {
      $event = $service->events->get($calendarId, $eventId);
    } catch (Google_Service_Exception $e) {
      try {
        $event = $service->events->get('hq37buvra0ju1sci457sk66pfk@group.calendar.google.com', $eventId);
      } catch (Google_Service_Exception $e) {
        echo '<h2 class="mb-2 mr-sm-2" id="titleString">Nem található esemény ezzel az azonosítóval.</h2>';
        //Add a button that returns to calendar page
        echo '<a href="index.php"><button class="btn btn-info noprint mb-2 mr-sm-2" data-toggle="modal" data-target="#addWorkSheetData">Vissza a naptárra</button></a>';
        exit();
      }
    }
    
    // Catch if event does not exist

    // Display the event details
    // echo "Event summary: " . $event->getSummary() . "<br>";
    // echo "Event start time: " . $event->getStart()->getDateTime() . "<br>";
    // echo "Event end time: " . $event->getEnd()->getDateTime() . "<br>"; 
    
    //MŰKÖDŐ ÁG
    $connect=Database::runQuery_mysqli();
    //Esemény címénak, egyéb adatainak megtalálása és eltárolása
    $query = "SELECT * from events WHERE id = '$eventId'";
    // //

    // $statement = $connect->prepare($query);
    // $statement->execute();
    // $result = $statement->fetchAll();

    // foreach($result as $row){
    //     $eventName=$row[1];
    //     $eventStart=$row[2];
    //     $eventEnd=$row[3];

    //execute query and return the result in a for loop
    $result = $connect->query($query);
    foreach($result as $row){
        $eventName=$row[1];
        $eventStart=$row[2];
        $eventEnd=$row[3];

    }    
echo'


<title>Munkalap - '.$event->getSummary().'</title>

<body>
<h2 class="mb-2 mr-sm-2" id="titleString">Munkalap - <strong>'.$event->getSummary().'</strong>  <button class="btn btn-success noprint mb-2 mr-sm-2" data-toggle="modal" data-target="#addWorkSheetData"><i style="font-size: 30px;" class="fas fa-plus fa-2x"></i></button></h2><h6>Kezdés: '.$event->getStart()->getDateTime()." | Befejezés: ".$event->getEnd()->getDateTime().'</h6>
<table id="worksheetData" class="table">
<tr><th>Név</th><th>Cselekvés</th><th>Hely</th><th>Megjegyzés</th><th class="noprint">Műveletek</th><tr>';
 
$query = "SELECT * from worksheet WHERE EventId = '$eventId' ORDER BY RecordDate ASC";
$result = $connect->query($query);

    foreach($result as $row){
        $workID=$row["ID"];
        $userName=$row["FullName"];
        $workType=$row["Worktype"];
        $Location=$row["Location"];
        $userComment=$row["Comment"];
        $recordDate=$row["RecordDate"];

        if ($userName==$_SESSION['fullName']){
          echo '<tr><td>'.$userName.'</td><td>'.$workType.'</td><td>'.$Location.'</td><td>'.$userComment.'</td><td class="noprint"> ';
          printf('<a id="editLink" href= "#" onClick="showDetails(\'%s\',\'%s\',\'%s\',\'%s\');">%s</a> ', $workID, $workType, $Location, $userComment, '<i class="far fa-lg fa-edit"></i></a>');
          printf('<a id="deleteLink" href= "#" onClick="deleteSheet(\'%s\');">%s</a> ', $workID, '<font color="red"><i class="far fa-lg fa-times-circle"></i></font>');
          echo '</td></a></tr>';}
        else{echo '<tr><td>'.$userName.'</td><td>'.$workType.'</td><td>'.$Location.'</td><td>'.$userComment.'</td></tr>';}
}echo'
</table>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="worksheetDataLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="worksheetDataLabel">Adatpont megváltoztatása</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editWorkData">
        <div class="form-group"></div>
        <div class="form-group">
          <input class="form-control" type="text" autocomplete="off" id="Edit_WorkType" value="helyErtek" placeholder="A fájlok helye a szerveren" required>
        </input></div>
        <div class="form-group">
          <label for="Edit_fileLocation">Fájl helye a szerveren</label>
          <input class="form-control" type="text" autocomplete="off" id="Edit_fileLocation" value="helyErtek" placeholder="A fájlok helye a szerveren" required></input></div>
        <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="Edit_Comment" value="CommentErtek" placeholder="Egyéb megjegyzés (nem kötelező)"></input></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" class="btn btn-primary" value="Hozzáadás"></button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>


<div class="modal fade" id="addWorkSheetData" tabindex="-1" role="dialog" aria-labelledby="worksheetDataLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="worksheetDataLabel">Adatpont hozzáadása a munkalaphoz</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="sendAddWorkData">
        <div class="form-group">
        <div class="form-group">
        <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="workTypeSelect" placeholder="Elvégzett feladat" required></input></div>
        <div class="form-group">
          <label for="fileLocation">Fájl helye a szerveren</label>
          <input class="form-control" type="text" autocomplete="off" id="fileLocation" placeholder="A fájlok helye a szerveren" required></input></div>
        <input id="folderSelectFieldInput" list="folderSelectField" oninput="onFolderSelectFieldInput()">
<datalist id="folderSelectField">
</datalist>  
<button type="button" onclick="updateFolderData()"><i class="fas fa-folder-open"></i></button>
        <span id="loadingData">Fájlok lekérése folyamatban...</span>
        <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="userComment" placeholder="Egyéb megjegyzés (nem kötelező)"></input></div>
        <input class="form-control" type="hidden" id="upDateId" value="NUL"></input>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" class="btn btn-primary" value="Hozzáadás"></button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>


</body>
</html>';}}
else{
    header("Location: ../index.php?error=AccessViolation");
} ?>

<script>

$('#sendAddWorkData').on('submit', function (e) {
e.preventDefault();
$('#addWorkSheetData').modal('hide');
var wType = $( "#workTypeSelect").val();
var wLoc = document.getElementById('fileLocation').value;
var wComment = document.getElementById('userComment').value;
var wEvent = getCookie("Cookie_eventId");
document.getElementById("titleString").innerHTML = "Adatpont hozzáadása folyamatban...";
$.ajax({
       type:"POST",
       url: 'wHandler.php',
       data:{wType:wType, wLoc:wLoc, wComment:wComment, wEvent:wEvent},
       success:function(successNum){
            //alert(successNum);
            window.location.href = "./worksheet.php?eventId="+wEvent;
       },
       error: function(jqXHR, textStatus, errorThrown){
        window.location.href = "./worksheet.php?eventId="+wEvent;
      } 
      })
});

function showDetails(id,type,loc,comment){
  console.log(id,type,loc,comment);
  document.getElementById('upDateId').value = id;
  document.getElementById('Edit_WorkType').value = type;
  document.getElementById('Edit_fileLocation').value = loc;
  document.getElementById('Edit_Comment').value = comment;

  $('#editModal').modal('show');
}

function deleteSheet(id){
  var wEvent = getCookie("Cookie_eventId");
  document.getElementById("titleString").innerHTML = "Adatpont törlése folyamatban...";
  $.ajax({
       type:"POST",
       data:{deleteId:id},
       success:function(successNum){
            // alert(successNum);
            window.location.href = "./worksheet.php?eventId="+wEvent;
            
       },
       error: function(jqXHR, textStatus, errorThrown){
        window.location.href = "./worksheet.php?eventId="+wEvent;
      } 
      })
}

function updateFolderData(){
  document.getElementById("loadingData").innerHTML = "Mappák lekérése folyamatban...";
  folder="Munka"
  if(document.getElementById("fileLocation").value == ""){
    folder="Munka"
  }else{
    folder=document.getElementById("fileLocation").value 
  }
  console.log(folder);
  getFolderData(folder)
  .then(data => {
    data;
    document.getElementById("loadingData").innerHTML = "";
    console.log(data.body)
  
    //Clear datalist
    document.getElementById("folderSelectField").innerHTML = "";

    //handle directory error.
    if(data.data.files == undefined){
      document.getElementById("loadingData").innerHTML = "Nem létező útvonal!";
      return;
    }
    data.data.files.forEach(element => {
      console.log(element.name);
      //Add every element to folderSelectField
      var option = document.createElement("option");
      option.value = element.name;
      document.getElementById("folderSelectField").appendChild(option);
    });

    document.getElementById("loadingData").innerHTML = "";
  })
}

$('#addWorkSheetData').on('show.bs.modal', function (event) {
  document.getElementById("fileLocation").value = "Munka"
  updateFolderData();
})

$('#editWorkData').on('submit', function (e) {
console.log("LOG-8");
e.preventDefault();
$('#editModal').modal('hide');
var uType = $( "#Edit_WorkType").val();
var uLoc = document.getElementById('Edit_fileLocation').value;
var uComment = document.getElementById('Edit_Comment').value;
var uId = document.getElementById('upDateId').value
var wEvent = getCookie("Cookie_eventId");
document.getElementById("titleString").innerHTML = "Adatpont módosítása folyamatban...";
$.ajax({
       type:"POST",
       data:{uType:uType, uLoc:uLoc, uComment:uComment, uId:uId},
       success:function(successNum){
            // alert(successNum);
            window.location.href = "./worksheet.php?eventId="+wEvent;
       },
       error: function(jqXHR, textStatus, errorThrown){
        window.location.href = "./worksheet.php?eventId="+wEvent;
      } 
      })
});

function getCookie(cname) {
     var name = cname + "=";
     var ca = document.cookie.split(';');
     for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if(c.indexOf(name) == 0)
           return c.substring(name.length,c.length);
     }
     return "";
}

//When a datalist item is selected.

function onFolderSelectFieldInput(){
  var val = document.getElementById("folderSelectFieldInput").value;
    var opts = document.getElementById('folderSelectField').children;
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].value === val) {
        // An item was selected from the list!
        // yourCallbackHere()
        //alert(opts[i].value);
        document.getElementById('fileLocation').value += "/"+opts[i].value;
        document.getElementById("folderSelectFieldInput").value = '';
        break;
      }
    }
}


</script>
<script src="./apiCommunications.js"></script>
<style>

body{
    padding: 20px;
}

@media print
{    
    .noprint, .noprint *
    {
        display: none !important;
    }
}
</style>