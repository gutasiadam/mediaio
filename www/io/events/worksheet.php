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
// if(isset($_POST["wEvent"]) && isset($_SESSION['UserUserName']))
// {
//  if(!isset($_POST['wComment'])){$wComment="";}
//  else{$wComment=$_POST['wComment'];}
//  $connect=Database::runQuery_mysqli();
 
// //change the query above to make it bindable
//  $query = "
//  INSERT INTO worksheet 
//  (FullName, EventID, Worktype, Location, Comment, RecordDate) 
//  VALUES (?, ?, ?, ?, ?, Now())
//  ";


// //execute query with paramter binding
//   $stmt = $connect->prepare($query);
//   $fullName=$_SESSION['lastName']." ".$_SESSION['firstName'];
//   $stmt->bind_param("sssss", $fullName, $_POST['wEvent'], $_POST['wType'], $_POST['wLoc'], $wComment);
//   $result = $stmt->execute();


//  if($result){
//      echo "1";
//  }
//  else{
//      echo "2";
//  }
//  exit();
// }

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
    $event = $service->events->get($calendarId, $eventId);

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
        <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="Edit_WorkType" value="helyErtek" placeholder="A fájlok helye a szerveren" required></input></div>
        <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="Edit_fileLocation" value="helyErtek" placeholder="A fájlok helye a szerveren" required></input></div>
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
        <select class="form-control" id="workTypeSelect" required>
        <option value="" selected disabled hidden>Válassz</option>
        <option value="Fotózás">Fotózás</option>
        <option value="Vágás">Vágás</option>
        <option value="Videózás">Videózás</option></select></div>
        <div class="form-group"><input class="form-control" type="text" autocomplete="off" id="fileLocation" placeholder="A fájlok helye a szerveren" required></input></div>
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
console.log("LOG-1");
e.preventDefault();
$('#addWorkSheetData').modal('hide');
var wType = $( "#workTypeSelect").val();
var wLoc = document.getElementById('fileLocation').value;
var wComment = document.getElementById('userComment').value;
var wEvent = getCookie("Cookie_eventId");
console.log("LOG-2");
document.getElementById("titleString").innerHTML = "Adatpont hozzáadása folyamatban...";
$.ajax({
       type:"POST",
       url: 'wHandler.php',
       data:{wType:wType, wLoc:wLoc, wComment:wComment, wEvent:wEvent},
       success:function(successNum){
            // alert(wEvent);
            window.location.href = "./worksheet.php?eventId="+wEvent;
            console.log("LOG-3");
       },
       error: function(jqXHR, textStatus, errorThrown){
        window.location.href = "./worksheet.php?eventId="+wEvent;
        console.log("LOG-4");
      } 
      })
});

function showDetails(id,type,loc,comment){
  console.log("LOG-5");
  console.log(id,type,loc,comment);
  document.getElementById('upDateId').value = id;
  document.getElementById('Edit_WorkType').value = type;
  document.getElementById('Edit_fileLocation').value = loc;
  document.getElementById('Edit_Comment').value = comment;
  $('#editModal').modal('show');
  console.log("LOG-6");
}

function deleteSheet(id){
  console.log("LOG-7");
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


</script>



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