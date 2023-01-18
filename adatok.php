<?php
namespace Mediao;
include "translation.php";
include "header.php";

namespace Mediaio;
//require "./Mediaio_autoload.php";

use Mediaio\itemDataManager;
require "./itemManager.php";

?>
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="./utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("./utility/menuitems.json");
          drawMenuItemsLeft('adatok', menuItems);
        });
      </script>
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span><?php if ($_SESSION['role']>=3){echo' Admin jogok';}?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav> <?php  } ?>
<form>
Ezeket a tárgyakat mutasd:
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="toDisplay1" id="inlinea" value="1">
  <label class="form-check-label" for="inlinea">Kölcsönözhető</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="toDisplay2" id="inlineb" value="2">
  <label class="form-check-label" for="inlineb">Stúdiós</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="toDisplay3" id="inlinec" value="3">
  <label class="form-check-label" for="inlinec">Nem Kölcsönözhető</label>
</div>
<button class="btn btn-success my-2 my-sm-0" type="submit">Mehet</button>
</form>

  			<td><h4 id="doTitle">Rendezés név szerint növekvő sorrendben</h4></td></tr></table>
<?php 
	$countOfRec=0;


  $displayData= array("toDisplay1"=>$_GET['toDisplay1'],"toDisplay2"=>$_GET['toDisplay2'],"toDisplay3"=>$_GET['toDisplay3']);
  $result=itemDataManager::getItemData($displayData);
if ($result!=NULL && $result->num_rows > 0) {
	echo "<table width='50' id="."dataTable"." align=center class="."table"."><th onclick=sort(0)>UID</th><th onclick=sort(1)>Név</th><th onclick=sort(2)>Típus</th><th onclick=sort(3)>Kivette</th>";
     //output data of each row
    //Displays amount of records found in leltar_master DB
    while($row = $result->fetch_assoc()) {
		/*if ($countOfRec == 50){
		}*/
		if($row["Status"]==0){
			echo "<tr style='background-color:#fffeab;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}
    else if($row["TakeRestrict"]=="s"){
			echo "<tr style='background-color:#7db3e8;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}
		else if($row["TakeRestrict"]=="*"){
			echo "<tr style='background-color:#F5B8B8;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}else{
			echo "<tr><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td>". "</td></tr>";
		}
		$countOfRec += 1;
	}
} else {
    echo "// Nem található a keresési feltéleknek megfelelő tárgy a rendszerben. //";
}
echo "</table>";
?>
<script>

window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
    display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};;

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

function sort(n){
  console.log('Working..')
  sortTable(n);
}
//UID, Név, Típus, Kivette 
function sortTable(n) {
  switch (n) {
    case 1:
      sMode="tárgynév";
      break;
    case 2:
      sMode="típus";
      break;
    case 3:
      sMode='"Kivette"';
      break;
    default:
      sMode="UID";
      break;
  }
  //s=sMode;
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("dataTable");
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc"; 
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      x = rows[i].getElementsByTagName("td")[n];
      y = rows[i + 1].getElementsByTagName("td")[n];
      
      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
      if (dir == "asc") {
        dMode="növekvő";
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        dMode="csökkenő";
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
  
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;      
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
  $('#doTitle').animate({'opacity': 0}, 400, function(){
        $(this).html('<h4 class="text text-info" role="alert">Rendezés '+sMode+' szerint '+dMode+' sorrendben.</h4>').animate({'opacity': 1}, 400);
        $(this).html('<h4 class="text text-info" role="alert">Rendezés '+sMode+' szerint '+dMode+' sorrendben.').animate({'opacity': 1}, 100);
        $(this).html('<h4 class="text text-info" role="alert">Rendezés '+sMode+' szerint '+dMode+' sorrendben.').animate({'opacity': 0}, 400);
    setTimeout(function() { $("#doTitle").text("Rendezés "+sMode+" szerint "+dMode+" sorrendben.").animate({'opacity': 1}, 400); }, 900);;});
}
</script>

<style>
  .btn-info2{color:white;background-color:#000658;border-color:#000658;border-width:2px}.btn-info2:hover{color:black;background-color:#ffffff;border-color:#000658;border-width:2px}
  .autocomplete {
    position: relative;
    display: inline-block;
  }

  input {
    border: 1px solid transparent;
    background-color: #f1f1f1;
    padding: 10px;
    font-size: 16px;
  }

  input[type=text] {
    background-color: #f1f1f1;
    width: 100%;
  }

  input[type=submit] {
    background-color: DodgerBlue;
   color: #fff;
   cursor: pointer;
  }

  .autocomplete-items {
    position: absolute;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    /*position the autocomplete items to be the same width as the container:*/
    top: 100%;
    left: 0;
    right: 0;
  }

  .autocomplete-items div {
    padding: 10px;
    cursor: pointer;
    background-color: #fff; 
    border-bottom: 1px solid #d4d4d4; 
  }

  /*when hovering an item:*/
  .autocomplete-items div:hover {
    background-color: #e9e9e9; 
  }

  /*when navigating through the items using the arrow keys:*/
  .autocomplete-active {
    background-color: Black !important; 
    color: #ffffff; 
  }

  .livearray{
    display:none;
  }
</style>