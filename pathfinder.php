<?php
namespace Mediaio;
use Mediaio\Database;
require_once "Database.php";
include "translation.php";
include "header.php";
session_start();
if(isset($_SESSION['UserUserName'])){
?>

<html>  
    <head>
        
        <link rel="stylesheet" href="utility/pathfinder.css" />
        <script src="utility/jquery.js"></script>
  <title>PathFinder</title>
  
    </head>
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
          drawMenuItemsLeft('pathfinder', menuItems);
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
    <body>  
        <div class="container">
   <br />
   <h1 align="center" class="rainbow">Tárgy kölcsönzési története</h1>
   <table id="itemSearch" align="left">
    <tr>
            <form action="./pathfinder.php" method="GET" autocomplete="off">
            
            <td><input id="id_itemNameAdd" type="text" name="pfItem" class="form-control mb-2 mr-sm-2" placeholder='<?php echo $applicationSearchField;?>'></div></td>
            <td><button type="submit" name="add" id="add" class="btn btn-info2 mb-2 mr-sm-2" ><?php echo $button_Find;?></button><span id='sendQueryButtonLoc'></span></td>
  	</tr>
    </form>
        <tr>

            <td><input id="id_itemUIDAdd" type="text" name="pfItem" class="form-control mb-2 mr-sm-2" placeholder='Kezdd el írni a tárgy UID-jét...'></div></td>
            <td><button name="add" id="add" class="btn btn-info2 mb-2 mr-sm-2" onclick="searchByUID()" ><?php echo $button_Find;?></button><span id='sendQueryButtonLoc'></span></td>
  	</tr>
    </table>  
					<div class="table-responsive">
						<table class="table table-bordered" id="dynamic_field"></div></div>
      </div>
			
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">

    <?php 
    if(isset($_GET['pfItem'])){
        $TKI = $_GET['pfItem'];
        $query = "SELECT * FROM `takelog`, `leltar` WHERE leltar.Nev=takelog.Item AND `Item` = '$TKI' ORDER BY `Date` DESC";
        $result = Database::runQuery($query);
        echo '<h3 class="panel-title">Tárgy útvonala - '.$TKI.'</h3>
        </div>
        <div class="panel-body">
         <div class="timeline">
          <div class="timeline__wrap">
           <div class="timeline__items">';
           foreach($result as $row)
           {
            if($row["Acknowledged"]==0){
              echo '<div class="timeline__item ">
              <div class="timeline__content service">
               <h2>'. $row["Date"]. ' ('. $row["User"] . ')</h2>
               <h6>Jóváhagyásra vár.</h6>
              </div>
             </div>';
            }else{
            if($row["Event"]=="OUT"){
              echo '<div class="timeline__item ">
              <div class="timeline__content out">
               <h2>'. $row["Date"]. ' ('. $row["User"] .')</h2>
              </div>
             </div>';} 
            if($row["Event"]=="IN"){
              echo '<div class="timeline__item ">
              <div class="timeline__content in">
               <h2>'. $row["Date"]. ' ('. $row["User"] . ')</h2>
              </div>
             </div>';
            }
            if($row["Event"]=="SERVICE"){
              echo '<div class="timeline__item ">
              <div class="timeline__content service">
               <h2>'. $row["Date"]. ' ('. $row["User"] . ')</h2>
               <h6>Szervizelés</h6>
              </div>
             </div>';
            }
            }

            /*if($row["Event"]=="INwA"){
              echo '<div class="timeline__item ">
              <div class="timeline__content inwa">
               <h2>'. $row["Date"]. ' by '. $row["User"] . '</h2>
               <p> Authkóddal jött be. </p>
              </div>
             </div>';
            }*/
            
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
var dbItems=[]; //For search by Name
var dbUidItems=[];//For search by UID
var d = {};
function loadJSON(callback) {   
console.log("[loadJSON] - called.")
var jqxhr = $.getJSON( "takeOutItems.json", function() {
  console.log( "[loadJSON] - OK" );
})
  .done(function(data) {
    console.log('load complete');
    d=jqxhr.responseJSON;
    $.each( data, function( i, item ) {
      //console.log(i+item);
      dbItems.push(item['Nev']);
      dbUidItems.push(item['UID']);
    })
  })
  .fail(function() {
    console.log( "hiba" );
  })
  .always(function() {
    console.log( "Adatok betöltése kész" );
  });
 

// Perform other work here ...

// Set another completion function for the request above
/*jqxhr.always(function() {
  console.log( "second complete" );
});*/
}
loadJSON();

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
  autocomplete(document.getElementById("id_itemUIDAdd"), dbUidItems);

//Search bz inputted UID value
function searchByUID(){
 var uidName=document.getElementById('id_itemUIDAdd').value;
 d.forEach(element => {
    if(element['UID']==uidName){
      console.log("pfItem="+element['Nev']);
       window.location.href += "?pfItem="+element['Nev'].replace(/ /g,'+');
    }
 });
}
  // autologout

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


  .in{
    background-color:#B8F5C2;
  }
  .out{
    background-color:#F5B8B8;
  }
  .service{
    background-color:#f1ee8e;
  }

  .autocomplete-items {
    position: relative;
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
    position: relative;
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

<?php
}
else{
    //header("Location: ./index.php?error=AccessViolation");
    echo "SESSION ERROR";
}?>