<?php 
include "translation.php";
if(isset($_SESSION['userId'])){
    error_reporting(E_ALL ^ E_NOTICE);
?>

<html>  
    <head>
        <script src="utility/timeline.min.js"></script>
        <link rel="stylesheet" href="utility/pathfinder.css" />
        <!--<link rel="stylesheet" href="utility/timeline.min.css" />-->
        <script src="utility/jquery.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <title>PathFinder 0.6</title>
  
    </head>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
					<a class="navbar-brand" href="index.php"><?php echo $applicationTitleShort;?></a>
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
            <li class="nav-item">
						  <a class="nav-link" href="./adatok.php"><i class="fas fa-database fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        	<a class="nav-link active" href="#"><i class="fas fa-project-diagram fa-lg"></i></a>
            			</li>
            <li class="nav-item">
                        <a class="nav-link" href="./profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#"><?php echo $nav_timeLockTitle;?> <span id="time"><?php echo $nav_timeLock_StartValue;?></span></a>
            </li>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit"><?php echo $nav_logOut;?></button>
                      </form>
                      <a class="nav-link my-2 my-sm-0" href="./help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
</nav>
    <body>  
        <div class="container">
   <br />
   <h3 align="center">PathFinder<i class="fas fa-project-diagram fa-lg"></i></h3>
   <table id="itemSearch" align="left"><tr><td>
            <form action="./pathfinder.php" method="GET" autocomplete="off">
            <div class="autocomplete" method="GET">
    		<input id="id_itemNameAdd" type="text" name="pfItem" class="form-control" placeholder='<?php echo $applicationSearchField;?>'></div></td>
            <td><button type="submit" name="add" id="add" class="btn btn-info2 mb-2 mr-sm-2"><?php echo $button_Find;?></button><span id='sendQueryButtonLoc'></span></td>
  			</tr></table>
			<form autocomplete="off" action="/index.php">
			</form>
					<div class="table-responsive">
						<table class="table table-bordered" id="dynamic_field">
			
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">

    <?php 
    if(isset($_GET['pfItem'])){
        $TKI = $_GET['pfItem'];      
        $connect = new PDO("mysql:host=localhost;dbname=leltar_master", "root", "umvHVAZ%");
        $query = "SELECT * FROM `takelog` WHERE `Item` = '$TKI' ORDER BY `Date` ASC";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        echo '<h3 class="panel-title">Item Journey - '.$TKI.'</h3>
        </div>
        <div class="panel-body">
         <div class="timeline">
          <div class="timeline__wrap">
           <div class="timeline__items">';
           foreach($result as $row)
           {
            if($row["Event"]=="OUT"){
              echo '<div class="timeline__item ">
              <div class="timeline__content out">
               <h2>'. $row["Date"]. ' by '. $row["User"] . '</h2>
               <p>'. $row["Event"]. '</p>
              </div>
             </div>';} 
            if($row["Event"]=="IN"){
              echo '<div class="timeline__item ">
              <div class="timeline__content in">
               <h2>'. $row["Date"]. ' by '. $row["User"] . '</h2>
               <p>'. $row["Event"]. '</p>
              </div>
             </div>';
            }
            if($row["Event"]=="INwA"){
              echo '<div class="timeline__item ">
              <div class="timeline__content inwa">
               <h2>'. $row["Date"]. ' by '. $row["User"] . '</h2>
               <p> In w\ AuthCode </p>
              </div>
             </div>';
            }
            
           }
           echo '
           </div>
          </div>
         </div>
        </div>';
    }
    $connect = null;
    ?>
    </body>  
</html>

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
/*$(document).ready(function(){
 jQuery('.timeline').timeline({
  mode: 'horizontal',
  //visibleItems: 4
  //Remove this comment for see Timeline in Horizontal Format otherwise it will display in Vertical Direction Timeline
 });
});*/
var dbItems = ["Fresnel 1000W", "Fresnel 650W", "Fresnel 300W", "Softbox allo 1", "Softbox allo 2", "Softbox fekvo",
 "Fresnel allvany A", "Fresnel allvany B", "Fresnel allvany C", "Softbox allvany 1", "Softbox allvany 2", "Softbox allvany 3", 
 "Hatter allvany 1", "Hatter allvany 2", "Fujitsu monitor 1", "Fujitsu monitor 2", "Dell monitor 1", "Dell monitor 2", "Genius hangfal",
  "Forgo szek 1", "Forgo szek 2", "Forgo szek 3", "Nagy szurke fuggony 1", "Nagy szurke fuggony 2", "Nagy szurke fuggony 3", "Nagy szurke fuggony 4",
  "HP laptop", "Szurke kanape", "Kek kanape", "Fekete fa szek 1", "Fekete fa szek 2", "Fekete fem szek", "Spanyolfal", "Kisasztal 1", "Kisasztal 2", "Nagyasztal 1",
  "Nagyasztal 2", "Nagyasztal 3", "TP-Link switch", "Cisco switch", "D-Link router", "Kuka (kisszoba)", "Kuka (PET)", "Kontroll hangfal 1", "Kontroll hangfal 2", "Szerverasztal 1", "Szerverasztal 2", "Szervergep 1", "Szervergep 2", "Szervergep 3",
  "Dell szervermonitor", "Nagy parafatabla", "Fekvo parafatabla", "Allo parafatabla", "Neon lampa", "Benti kisszekreny 1", "Benti kisszekreny 2",
  "Kinti kisszekreny", "Kinti nagyszekreny", "Asztali LED lampa", "Filces tabla", "Szerver ventillator", "Feher allo ventillator",
  "Hattertarto csipesz 1", "Hattertarto csipesz 2", "Hattertarto csipesz 3", "Negyes kapcsolhato eloszto 1", "Negyes kapcsolhato eloszto 2", "Negyes kapcsolhato eloszto 3", "Negyes kapcsolhato eloszto 4",
  "Negyes kapcsolhato eloszto 5", "Negyes kapcsolhato eloszto 6", "Negyes eloszto (5m)", "Harmas eloszto (1m)"];


//Process takeout


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

// autologout
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

</script>

<style>
  * {
    box-sizing: border-box;
  }

  .btn-info2{color:white;background-color:#000658;border-color:#000658;border-width:2px}.btn-info2:hover{color:black;background-color:#ffffff;border-color:#000658;border-width:2px}

  body {
    font: 16px Arial;  
  }

  /*the container must be positioned relative:*/
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

  .in{
    background-color:#B8F5C2;
  }
  .out{
    background-color:#F5B8B8;
  }
  .inwa{
    background-color:#acfcfc;
  }
</style>

<?php
}
else{
    header("Location: ./index.php?error=AccessViolation");
}?>