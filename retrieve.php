<?php
include "translation.php";
include "header.php";
include('./utility/refetchdata.php');

if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}

$SESSuserName = $_SESSION['UserUserName'];
error_reporting(E_ALL ^ E_NOTICE);
// Cookie for ITEM SELECTION (JS --> PHP)
setcookie('Cookie_currentItemSel', 0, time() + (36000), "/");
setcookie("currentItemRentByMatch", 0, time() + (1000), "/");
setcookie("currentUser", $SESSuserName, time() + (1000), "/");
setcookie("currentRentby", 0, time() + (1000), "/");


function PhparrayCookie(){
  array_push($selItems, $_COOKIE['id_itemNameAdd']);
  foreach ($selItems as $x){
    echo $x . " ";
  }
}
?>
<script>
var goStatus = 0;

</script>

<html >
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php"><img src="./utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
						<script>
            $( document ).ready(function() {
              menuItems = importItem("./utility/menuitems.json");
              drawMenuItemsLeft('retrieve',menuItems);
            });
            </script>
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>
            <?php if ($_SESSION['role']>=3){
              echo '<li><a class="nav-link disabled" href="#">Admin jogok</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit"><?php echo $nav_logOut;?></button>
                      </form>
            <a class="nav-link my-2 my-sm-0" href="./help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
		</nav>


	<body >
		<div class="container">
			<br /><br />
      <h2 class="rainbow" align="center" id="doTitle">Visszahozás</h2><br />
      <div class="row">
      <div class="col-md-4">
      <div class="alert alert-info"><?php echo $Welcomemsg_retrieve?></div>
			<div class="form-group">
        <table id="itemSearch" align="left"><tr><td><div class="autocomplete" method="GET">
    				<input id="id_itemNameAdd" type="text" name="add" class="form-control mb-2 mr-sm-2" placeholder='<?php echo $applicationSearchField;?>'></div></td>
            <td><button type="button" name="add" id="add" class="btn btn-info2 add_btn mb-2 mr-sm-2"><?php echo $button_Add;?></button><td><span id='sendQueryButtonLoc'></span></td>
            
  			</tr>
        
        </table>
			<form autocomplete="off" action="/index.php">
			</form>
        </div></div>
        <div class="col-md-4"><div class="form-check intactForm">
  <input class="form-check-input" type="checkbox" value="" id="intactItems">
  <label class="form-check-label" for="intactItems">
 <h6>Igazolom, hogy minden, amit visszahoztam sérülésmentes és kifogástalanul működik. Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
  </label>
</div></div>
      </div>
      <br>
          <div class="row justify-content-md-center">
          <!-- THIS TABLE HOLDS THE TWO CHILDS-->
            <div class="col-md-6"><table class="table table-bordered table-dark" style="line-height: 10px;" id="dynamic_field"><tr><div style="text-align:center;" class="text-primary"><strong></hr></strong></div></tr></table></div>
            <!--<div class="col-md-6"><table class="table table-bordered table-dark" style="line-height: 30px;" id="dynamic_field_2"><tr><div style="text-align:center;" class="text-primary"><strong><?php //echo $retrieve_table2;?></strong></div></tr></table></div>-->
          </div>
          
						
				<form name="sendRequest" method="POST" action='/index.php'>
              
				</form>
        <table class="table table-bordered livearray" id="liveSelArrayResult"><td></td></table>
        
			</div>
		</div>
	</body>
  <!--<footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p>Code: <a href="https://github.com/d3rang3">Adam Gutasi</a></p></div></footer>-->
</html>
<script>

 function getCookie(name)
  {
    var re = new RegExp(name + "=([^;]+)");
    var value = re.exec(document.cookie);
    return (value != null) ? unescape(value[1]) : null;
  }

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

var dbItems=(loadFile("./DB_Elements.txt"));
console.log(dbItems);
//Right at load - start autologout.

  var selectList = [];
  var id=1;
  var needsVerification = [];
  $(document).ready(function(){

    $('.intactForm').hide(); // Csak akkor jelenjen meg a checkbox, ha már van Go gomb is.
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

    // Let the ADD BTN work without Clicking
    var input = document.getElementById("id_itemNameAdd");
      input.addEventListener("keyup", function(event) {
    // Number 13 is the "Enter" key on the keyboard
    var input_checkviolationValue = document.getElementById("id_itemNameAdd").value;
    if (event.keyCode === 13) {
    // Cancel the default action, if needed
      if (input_checkviolationValue == ''){
      }
      else{
        event.preventDefault();
    // Trigger the button element with a click
    document.getElementById("add").click();
      }
    
    }
});
    
	$(document).on('click', '.btn_remove', function(){ // Tárgy törlése
    var button_id = $(this).attr("id");
    dbItems.push(selectList[button_id]);
    dbItems.sort;
    selectList[button_id-2]=null;
    $('#row'+button_id+'').remove();

    document.getElementById("liveSelArrayResult").innerHTML = "";
    $('#liveSelArrayResult').append('<td>'+selectList+'</td>');
	});
//selectList = selectList.filter(Boolean);


  function allowGO(){
    if($('#intactItems').is(":checked")){
      $('.go_btn').removeClass('disabled');
    }
  }
  $(document).on('click', '#intactItems', function(){
    allowGO();
  });
  //kivétel indítása.
  $(document).on('click', '.go_btn', function(){
    if($("#intactItems").prop("checked")){ // ha a felhasználó elfogadta, hogy a tárgyak rendben vannak.
      var filtered = selectList.filter(function (el) {
      return el != null;
    });
      console.log('filterelt:'+filtered);
      retrieveJSON = JSON.stringify(filtered);
      //alert("retrieve ajax:"+retrieveJSON);
      $.ajax({
    method: 'POST',
    url: './utility/Retrieve_Handler.php',
    data: {data : retrieveJSON, mode: "handle"},
    success: function (response){
      //alert(response);
      $('#doTitle').animate({'opacity': 0}, 400, function(){
        $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak 🙂! Az oldal újratölt.</h2>').animate({'opacity': 1}, 400);
        $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak 🙂! Az oldal újratölt.</h2>').animate({'opacity': 1}, 3000);
        $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak 🙂! Az oldal újratölt.</h2>').animate({'opacity': 0}, 400);
        setTimeout(function() { $("#doTitle").text(applicationTitleShort).animate({'opacity': 1}, 400); }, 3800);;   
    });
    setTimeout(function() { location.reload(); }, 2000);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }
});
  }else{
    alert("Ha a tárggyal gond van, jelezd a vezetőségnek!");
  }});
  
  $(document).on('click', '.add_btn', function(){

    //console.log("CLICK!"+id);
    
    //CSAK AKKOR Működhet GOMB, HA AZ INTACTFORM KI LETT PIPÁLVA.
    
     var currentItemSel = document.getElementById("id_itemNameAdd").value;
     document.getElementById("id_itemNameAdd").innerHTML = "";
     $.ajax({
    method: 'POST',
    url: './utility/Retrieve_Handler.php',
    data: {data : currentItemSel, mode: "check"},
    success: function (response){ // A generált ellenőrző karakter.
      /* Lehetséges kimenetek:
      A - A felhasználó visszahoz egy önmaga által kivett tárgyat.
      B - A felhasználó egy bennlévő tárgyra hivatkozott.
      C - A felhasználó egy más által kivett tárgyra mutat.
      X - Hiba.*/
      switch(response) {
        case "A":
          console.log("A: A felhasználó visszahoz egy önmaga által kivett tárgyat.");
          $('.intactForm').fadeIn();
          if (goStatus == 0){
            $('#sendQueryButtonLoc').append('<button type="submit" class="btn btn-success go_btn mb-2 mr-sm-2 disabled" id="goButton" >Mehet</button>');
            goStatus++;
          }
          $('#dynamic_field').append('<tr id="row'+id+'"><td>'+currentItemSel+'</td><td><button type="button" name="remove" id="'+id+'"style="text-align:center;" class="btn btn-danger btn'+id+' btn_remove">X</button></td></tr>');
          selectList[id-2]=currentItemSel; // bekerülhet az előkészítítő listába.
          break;
        case "B":
          console.log("B: A felhasználó egy bennlévő tárgyra hivatkozott.");
          $('#doTitle').animate({'opacity': 0}, 400, function(){
        $(this).html('<h2 class="text text-info" role="alert">A keresett tárgy benn van.</h2>').animate({'opacity': 1}, 400);
        $(this).html('<h2 class="text text-info" role="alert">A keresett tárgy benn van.</h2>').animate({'opacity': 1}, 3000);
        $(this).html('<h2 class="text text-info" role="alert">A keresett tárgy benn van.</h2>').animate({'opacity': 0}, 400);
        setTimeout(function() { $("#doTitle").text(applicationTitleShort).animate({'opacity': 1}, 400); }, 3800);;   
    }); 
          break;
        case "C":
          console.log("C: A felhasználó egy más által kivett tárgyra mutat.");
          $('#doTitle').animate({'opacity': 0}, 400, function(){
        $(this).html('<h2 class="text text-info" role="alert"><strong>Nem nálad van! </strong> Csak a saját tárgyaidat tudod visszahozni.</h2>').animate({'opacity': 1}, 400);
        $(this).html('<h2 class="text text-info" role="alert"><strong>Nem nálad van! </strong> Csak a saját tárgyaidat tudod visszahozni.</h2>').animate({'opacity': 1}, 3000);
        $(this).html('<h2 class="text text-info" role="alert"><strong>Nem nálad van! </strong> Csak a saját tárgyaidat tudod visszahozni.</h2>').animate({'opacity': 0}, 400);
        setTimeout(function() { $("#doTitle").text(applicationTitleShort).animate({'opacity': 1}, 400); }, 3800);;   
    }); 
          /*$('#dynamic_field_2').append('<tr id="row'+id+'"><td>'+currentItemSel+'<br><small>Nem nálad van!</small></td><td>Csak a saját tárgyaidat tudod visszahozni.</td>/tr>');*/
          //Hibás currentRentby..

          //$('#dynamic_field_2').append('<tr id="row'+id+'"><td>'+currentItemSel+'<br><small>'+currentRentby+'</small></td><td><form><div class="form-group"><input type="number" class="form-control" id="authCodeInput'+id+'" placeholder="XXX-XXX"><input type="hidden" id="authCodeItem'+id+'" class="form-control" value='+currentItemSel+'></input></div></form></td><td><button type="button" class="verify_btn btn-success" name="verify" id="'+id+'" class="btn btn-success btnsucc'+id+' btn_auth">+</button></td><td><button type="button" name="remove" id="'+id+'" class="btn btn-danger btn'+id+' btn_remove">X</button></td></tr>');
          break;
        default:
        console.log("X: Egyik sem. Hiba.");
          // code block
}
      $('#id_itemNameAdd').val('');
      //alert("response from check:"+response);
      //location.reload();

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }
    
});
id++;
});

// AUTHCODE (kikerülőben levő funkció)
//On Verify Btn click

//Random fix
  
	$('#submit').click(function(){		
		$.ajax({
			url:"name.php",
			method:"POST",
			data:$('#add_name').serialize(),
			success:function(data)
			{
				alert(data);
				$('#add_name')[0].reset();
			}
		});
	});

  

});

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
  inp.addEventListener("keydown", function(e) {2
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

  #dynamic_field td {
    font-size: xx-large;
    margin: 0 auto;
    vertical-align: middle;
  }
  .btn-info2{color:white;background-color:#000658;border-color:#000658;border-width:2px}.btn-info2:hover{color:black;background-color:#ffffff;border-color:#000658;border-width:2px}

  body {
    font: 16px Arial;  
  }/*the container must be positioned relative:*/
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

<?php //Message handler
if($_GET['state'] == "Success"){
  echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-success"><strong>Retrieve - </strong>Sikeresen bekerültek a tárgyak a raktárba.</div></tr></td></table>';
}
?>