<?php 
include "translation.php";
if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}
?>
<head>
<script src="JTranslations.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
</head>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php">Arpad Media IO</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item ">
						  <a class="nav-link" href="./index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						  <a class="nav-link" href="./takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item">
						  <a class="nav-link" href="./retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item active">
						  <a class="nav-link" href="#"><i class="fas fa-database fa-lg"></i></a>
						</li>
						<li class="nav-item">
                        	<a class="nav-link" href="./pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
						</li>
            <li class="nav-item">
                        <a class="nav-link" href="./events/"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
						<li class="nav-item">
                        <a class="nav-link" href="./profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            			</li>
						<li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
					  <a class="nav-link my-2 my-sm-0" href="#"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
</nav>
<!--<div class="form-group">
        <table id="itemSearch" align="left"><tr><td><div class="autocomplete" method="GET">
    				<input id="id_itemNameAdd" type="text" name="add" class="form-control mb-2 mr-sm-2" placeholder='<?php echo $applicationSearchField;?>'></div></td>
            <td><button type="button" name="add" id="keres1" class="btn btn-info2 add_btn mb-2 mr-sm-2">Keress</button> </button>     <span id='sendQueryButtonLoc'></span></td>-->
  			<td><h4 id="doTitle">Rendezés név szerint növekvő sorrendben</h4></td></tr></table>
<?php 
//$allowedIP = array("31.46.204.50", "80.99.70.46", "192.168.0.1", "127.0.0.1", "77.234.86.20");
//if(!in_array($_SERVER['REMOTE_ADDR'], $allowedIP) && !in_array($_SERVER["HTTP_X_FORWARDED_FOR"], $allowedIP)) {
  //header("Location: https://www.google.com"); //redirect
  //exit();
//}

$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="leltar_master";
	$countOfRec=0;

	$conn = new mysqli($serverName, $userName, $password, $dbName);

	if ($conn->connect_error) {
		die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
	}
	$sql = "SELECT * FROM leltar ORDER BY Nev ASC";
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
	echo "<table width='50' id="."dataTable"." align=center class="."table"."><th onclick=sortTable(0)>UID</th><th onclick=sortTable(1)>Name</th><th>Type</th><th>Out by</th>";
     //output data of each row
    //Displays amount of records found in leltar_master DB
    while($row = $result->fetch_assoc()) {
		if ($countOfRec == 50){
		}
		if($row["TakeRestrict"]=="*"){
			echo "<tr style='background-color:#fffeab;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}
		else if($row["Status"]==0){
			echo "<tr style='background-color:#F5B8B8;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}else{
			echo "<tr><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td>". "</td></tr>";
		}
		$countOfRec += 1;
	}
} else {
    echo "0 results";
}
echo "</table>";
$conn->close();?>
<script>

$('#keres1').click(function(){
  var keres1Val=  document.getElementById("id_itemNameAdd").value;
  console.log(keres1Val);
/*AJAX CALL EGY PHP Fájlba, onnan már formázva return*/
}); 

function loadFile(filePath) {
  var result = null;
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", filePath, false);
  xmlhttp.send();
  if (xmlhttp.status==200) {
    result = xmlhttp.responseText;
  }
  return result.split("\n");
  
}

var dbItems=(loadFile("./utility/DB_Elements.txt"));
// dbItem remover tool - Prevents an item to be added twice to the list
function arrayRemove(arr, value) {

return arr.filter(function(ele){
    return ele != value;
});

}
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
  }

  autocomplete(document.getElementById("id_itemNameAdd"), dbItems);

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
</script>

<script>
function sortTable(n) {
  if(n==1){
    sMode="név";
  }else{
    sMode="UID";
  }
  
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