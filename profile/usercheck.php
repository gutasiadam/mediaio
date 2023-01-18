<?php
namespace Mediaio;
use Mediaio\Database;
require_once("../Database.php");
include "header.php";
session_start();
if($_SESSION['role']<3){
    exit();
}
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}?>

<head>
<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
</head>
<title>Elérhetőségek</title>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      
            <a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
          
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>
            <?php if ($_SESSION['role']>=3){ ?>
              <li><a class="nav-link disabled" href="#">Admin jogok</a></li> <?php  }?>
            </ul>
						<form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
                      <button class="btn btn-danger my-2 my-sm-0" name="logout-submit" type="submit">Kijelentkezés</button>
                      </form>
                      <div class="menuRight"></div>
					</div>
          <script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    </nav>


<?php 
$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="mediaio";
	$countOfRec=0;

  /*Csoportosított rendezés*/
  //1. lépés: dátum, felhasználó és event szerinti csoportosítások: megadja, melyik sorokat kell majd megkeresnünk.
  $sql ="SELECT
  takelog.Date,
  takelog.User,
  takelog.Event,
  leltar.UID
FROM
  `takelog`,
  `leltar`
WHERE
  takelog.Acknowledged = 'false'
  AND takelog.Event != 'SERVICE'
  AND takelog.Item = leltar.Nev
GROUP BY
  DATE
ORDER BY
  DATE DESC,
  USER,
  EVENT";
  $result=Database::runQuery($sql);
  echo "<table width='50' align=center class="."table"."><th>Dátum</th><th>felhasználónév</th><th>Eszköz</th><th>Esemény</th>";
  $recCount=0;
  while($query1Row = $result->fetch_assoc()) {
    $recCount+=1;
    //echo var_dump($query1Row); //Only for debug reasons.
    $itemString=NULL;
    //2. lépés: minden rekordra végrehajtjuk a keresést, ezzel megkapjuk az összetartozó eseményeket.
    $sql="SELECT Item FROM `takelog` WHERE Acknowledged='false' AND Event!='SERVICE' AND Date='".$query1Row['Date']."' AND User='".$query1Row['User']."' AND
    Event='".$query1Row['Event']."' ORDER BY Item";
    $items=Database::runQuery($sql);
    while($itemsRow = $items->fetch_assoc()) {
      //echo var_dump($itemsRow); //Only for debug reasons.
      $itemString.=$itemsRow['Item']." (".$query1Row['UID'].")"."   -  ";
    }
    //$itemString=substr_replace($itemString, "", -1);
    echo "<tr id=event".$recCount."><td>".$query1Row["Date"]."</td><td>".$query1Row["User"]. "</td><td style='line-height: 200%; font-size: 18px;'>".$itemString."</td><td>".$query1Row["Event"]."</td>
    <td><button class='btn btn-success' onclick='acceptEvent(".$recCount.")'><i class='fas fa-check success'></i></button></br>";
    if($query1Row['Event']=='OUT'){
          echo "<button class='btn btn-danger' style='padding: 7px 15px; margin-top:4px' onclick='declineEvent(".$recCount.")'><i class='fas fa-times danger'></i></button></td>";
    }
    else if($query1Row['Event']=='IN'){
    echo "<button class='btn btn-warning' style='padding: 7px 14.5px; margin-top:4px' onclick='openEventDocument(".$recCount.")'><i class='fas fa-file-alt'></i></button></td>";

    echo "</tr>";
    }
    //echo $outItems;
  }

  //3. lépés: elfogadásnál az össes egybetartozó eseményt el kell fogadtatni.
  if($recCount==0){
    echo '// Jelenleg semmi sem vár elfogadásra.';
  }
echo "</table>";

?>
<script>
(function(){
  setInterval(updateTime, 1000);
});

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
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

window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};



function acceptEvent(n){
  //alert('elfogadas');
  var items=$('#event'+n)[0].cells[2].innerHTML.split('   -  ');
  var i=0;
  items.forEach(element => {
    console.log(element);
    items[i]=element.split('(')[0].trim();
    console.log(items[i]);
    i++;
  });
  items.pop() //removes last '' element.
  var itemsJSON=JSON.stringify(items);
  var eventType=$('#event'+n)[0].cells[3].innerHTML;
  var date=$('#event'+n)[0].cells[0].innerHTML;
  var mode;
  if(eventType=='IN'){
    mode='retrieveApproval';
  }else{
    mode='takeOutApproval';
  }

  console.log(items);
  $.ajax({
    method: 'POST',
    url: '../ItemManager.php',
    data: {data : itemsJSON, mode: mode, value: true, date: date}, //value true means event is approved.
    success: function (response){
      alert(response);
      if(response==200){
        //Remove event from the table.
        $('#event'+n).fadeOut();
      }else{
        console.log("Backend error.");
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }
});
}

function declineEvent(n){
  //alert('elutasitas');
  var items=$('#event'+n)[0].cells[2].innerHTML.split('   - ');
  items.forEach(element => {
    console.log(element);
    element=element.split('(');
  });
  items.pop() //removes last '' element.
  var itemsJSON=JSON.stringify(items);
  var eventType=$('#event'+n)[0].cells[3].innerHTML;
  var date=$('#event'+n)[0].cells[0].innerHTML;
  var mode;
  if(eventType=='IN'){
    mode='retrieveApproval';
  }else{
    mode='takeOutApproval';
  }

  console.log(items);
  $.ajax({
    method: 'POST',
    url: '../ItemManager.php',
    data: {data : itemsJSON, mode: mode,  value: false, date: date}, //event declined.
    success: function (response){
      //alert(response);
      if(response==200){
        //Remove event from the table.
        $('#event'+n).fadeOut();
      }else{
        console.log("Backend error.");
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }
});
}

function openEventDocument(n){
  alert('dokumentum');
}
</script>

