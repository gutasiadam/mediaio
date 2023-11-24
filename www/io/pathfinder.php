<?php

namespace Mediaio;

use Mediaio\Database;
session_start();

if(isset($_SESSION['UserUserName'])){ //If user is logged in
  require_once "Database.php";
  include "translation.php";
  include "header.php";

?>

<html>  
  <head>
    <link rel="stylesheet" href="utility/pathfinder.css" />
  <title>PathFinder</title>
</head>
<script src="utility/jquery.js"></script>

  <!-- If user is logged in -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php"><img src="./utility/logo2.png" height="50"></a>

      <!-- Load Menu and Index table Icons and links -->
  <script type="text/javascript">
        window.onload = function () {

          menuItems = importItem("./utility/menuitems.json");
          drawMenuItemsLeft('pathfinder', menuItems);

          drawMenuItemsRight('pathfinder', menuItems);
          drawIndexTable(menuItems, 0);

          display = document.querySelector('#time');
          var timeUpLoc="utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
  </script>
  
    <!-- Mobile Navigation - Additional toggle button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Main Navigation -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
    </ul>
    <ul class="navbar-nav ms-auto navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
    </form>
  </div>
</nav>

<body>  
  <div class="container">
  <br />
  <h1 align="center" class="rainbow">Tárgy kölcsönzési története</h1>

  <!-- Item search table -->
  <table id="itemSearch" align="left">
    <tr><form action="./pathfinder.php" method="GET" autocomplete="off">
      <td><input id="id_itemNameAdd" type="text" name="pfItem" class="form-control mb-2 mr-sm-2" placeholder='<?php echo $applicationSearchField;?>'></div></td>
      <td><button type="submit" name="add" id="add" class="btn mediaBlue mb-2 mr-sm-2" ><?php echo $button_Find;?></button><span id='sendQueryButtonLoc'></span></td>
    </form></tr>
  </table>  
	<div class="table-responsive">
		<table class="table table-bordered" class="dynamic_marked" id="dynamic_field">
  </div>
</div>
			
<!-- Timeline panel code -->
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">

          <?php
          if (isset($_GET['pfItem'])) {
            $connectionObject = Database::runQuery_mysqli();
            $TKI = $_GET['pfItem'];
            //find all occurences of '-' and split by the last occurence using regex
            $TKI = preg_split('/ -/', $TKI);

            //Get the Name of the item
            $TKI = $TKI[0];
            $query = "SELECT * FROM `takelog` WHERE JSON_CONTAINS(Items, " . "'" . "{" . '"name" : "' . $TKI . '"}' . "'" . ") ORDER BY `Date` DESC";
            $result = mysqli_query($connectionObject, $query);
            echo '<h3 class="panel-title">Tárgy útvonala - ' . $TKI . '</h3>
        </div>
        <div class="panel-body">
         <div class="timeline">
          <div class="timeline__wrap">';
            foreach ($result as $row) {
              if ($row["Acknowledged"] == 0) {
                echo '<div class="timeline__item left">
              <div class="timeline__content service">
               <h2>' . $row["Date"] . ' (' . $row["User"] . ')</h2>
               <h6>Jóváhagyásra vár.</h6></div></div>';
              } else {
                if ($row["Event"] == "OUT") {
                  echo '<div class="timeline__item right">
              <div class="timeline__content out">
               <h2>' . $row["Date"] . ' (' . $row["User"] . ')</h2>';
                }
                if ($row["Event"] == "IN") {
                  echo '<div class="timeline__item left">
              <div class="timeline__content in">
               <h2>' . $row["Date"] . ' (' . $row["User"] . ')</h2>';
                }
                if ($row["Event"] == "SERVICE") {
                  echo '<div class="timeline__item right">
              <div class="timeline__content service">
               <h2>' . $row["Date"] . ' (' . $row["User"] . ')</h2>
               <h6>Szervizelés</h6>';
            }
            if($row["ACKBY"]!=NULL)
            echo '<h6 style="color: grey;">✔: '. $row["ACKBY"]. '</h6>';
            echo '</div></div>';
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
var dbItems=[]; //For search by Name
var dbUidItems=[];//For search by UID
var ItemNames=[]; //For search by Name
var d = {};
function loadJSON(callback) {   
console.log("[loadJSON] - called.")
var jqxhr = $.getJSON( "./data/takeOutItems.json", function() {
  console.log( "[loadJSON] - OK" );
})
  .done(function(data) {
    console.log('load complete');
    d=jqxhr.responseJSON;
    $.each( data, function( i, item ) {
      //console.log(i+item);
      var itemData={};
      itemData['Nev']=item['Nev'];
      itemData['UID']=item['UID'];
      dbItems.push(itemData);
      ItemNames.push(item['Nev']+" - "+item['UID']);
      // dbItems.push(item['Nev']);
      // dbUidItems.push(item['UID']);
    })
  })
  .fail(function() {
    console.log( "hiba" );
  })
  .always(function() {
    console.log( "Adatok betöltése kész" );
  });
 

}
loadJSON();

    //Process takeout


    // dbItem remover tool - Prevents an item to be added twice to the list
    function arrayRemove(arr, value) {

      return arr.filter(function (ele) {
        return ele != value;
      });

    }
    function autocomplete(inp, arr) {
      /*the autocomplete function takes two arguments,
      the text field element and an array of possible autocompleted values:*/
      var currentFocus;
      /*execute a function when someone writes in the text field:*/
      inp.addEventListener("input", function (e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false; }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item contains the searched term:*/
          if (arr[i].toUpperCase().includes(val.toUpperCase())) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            var boldStartIndex = arr[i].toUpperCase().indexOf(val.toUpperCase());
            b.innerHTML = arr[i].substr(0, boldStartIndex);
            b.innerHTML += "<strong>" + arr[i].substr(boldStartIndex, val.length) + "</strong>";
            b.innerHTML += arr[i].substr(boldStartIndex + val.length);
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
            /*execute a function when someone clicks on the item value (DIV element):*/
            b.addEventListener("click", function (e) {
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
      inp.addEventListener("keydown", function (e) {
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

    autocomplete(document.getElementById("id_itemNameAdd"), ItemNames);
    // autocomplete(document.getElementById("id_itemUIDAdd"), dbUidItems);

    //Search bz inputted UID value
    function searchByUID() {
      var uidName = document.getElementById('id_itemUIDAdd').value;
      d.forEach(element => {
        if (element['UID'] == uidName) {
          console.log("pfItem=" + element['Nev']);
          window.location.href = window.location.href.split("?")[0] + "?pfItem=" + element['Nev'].replace(/ /g, '+');
        }
      });
    }

  </script>

<?php
}
else{
  //User is not logged in.
    header("Location: ./index.php?error=AccessViolation");
}?>