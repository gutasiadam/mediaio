<?php

include "translation.php";

if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");}
$SESSuserName = $_SESSION['UserUserName'];
error_reporting(E_ALL ^ E_NOTICE);
// Cookie for ITEM SELECTION (JS --> PHP :3)
setcookie('Cookie_currentItemSel', 0, time() + (36000), "/");


function PhparrayCookie(){
  array_push($selItems, $_COOKIE['id_itemNameAdd']);
  foreach ($selItems as $x){
    echo $x . " ";
  }
}

  // Database initialization - Get's total item number in the database and estabilishes connection.
	$serverName="localhost";
	$userName="root";
	$password=$application_DATABASE_PASS;
	$dbName="leltar_master";
	$countOfRec=0;

	$conn = new mysqli($serverName, $userName, $password, $dbName);

	if ($conn->connect_error) {
		die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
	}
	$sql = "SELECT * FROM leltar";
	$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
		$countOfRec += 1;
	}
} else {
    echo "0 results";
}
$conn->close();

//CHECK WETHER SELECTED ITEM IS OUT OR NAH
if(isset($_POST['takeoutCheck'])){
  $checkitem= json_decode(stripslashes($_POST['takeoutCheck']));
  $conn = new mysqli($serverName, $userName, $password, 'leltar_master');
  $sqlPreCheck = ("SELECT `leltar`.`Nev`, `leltar`.`Status`
  FROM `leltar`
  WHERE (( `Status` = 0) AND ( `Nev` = '$checkitem'))");
  $preResult = $conn->query($sqlPreCheck);
  $rowReturn = $preResult->num_rows;
  if ($rowReturn != 0){ #Tehát 1, ki van véve
    echo "1";
  }if ($rowReturn == 0){
    echo "0";
  }
  $conn->close();
  exit;
}


// IF VERYTHING IS GOOD, WRITE TO DB
if( isset($_POST['data'])){
  $data = json_decode(($_POST['data']), true);
  $dbName="leltar_master";
  foreach ($data as $d){
    $conn = new mysqli($serverName, $userName, $password, $dbName);
    $currDate = date("Y/m/d H:i:s");
	if ($conn->connect_error) {
    die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
  }
  else{  
    $sql = ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '1', '$currDate', '$SESSuserName', '$d', 'OUT')");
    $result = $conn->query($sql);
    $conn->close();
    if ($result === TRUE) {
      $conn = new mysqli($serverName, $userName, $password, 'leltar_master');
      $sql2 = ("UPDATE leltar SET Status = 0, RentBy = '$SESSuserName' WHERE `Nev`='$d'");
      $result2 = $conn->query($sql2);
      $conn->close();
      if ($result2 === TRUE){
        echo "Success.";
      }
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
    }
  echo $d;
  }
  exit;
 }?>
<script>

//WebSocket
/*
if ("WebSocket" in window) {
               console.log("WebSocket is supported by your Browser!");
               var ws = new WebSocket("ws://192.168.0.24:3000/ws");
               // Let us open a web socket
				
               ws.onopen = function() {
                  
                  // Web Socket is connected, send data using send()
                  sender={'method':'probe','user':'gutasiadam'}
                  ws.send(JSON.parse(sender));
                  document.getElementById('webSocketState').style.backgroundColor = ('lime');
                  console.log("Message is sent to the network");
               };
				
               ws.onmessage = function (evt) { 
                var received_msg = evt.data;

                try {
                    let m = JSON.parse(evt.data);
                     handleMessage(m);
                } catch (err) {
                    console.log('[Client] Message is not parseable to JSON.');
                }

                  console.log("Message recieved: " + received_msg);
                  document.getElementById('recMsg').innerHTML = (received_msg);
               };
				
               ws.onclose = function() { 
                  
                  // websocket is closed.
                  console.log("Connection is closed..."); 
                  document.getElementById('webSocketState').style.backgroundColor = ('red');
                  document.getElementById("ServerMsg").style.backgroundColor = ('LightCoral');
                  document.getElementById("ServerMsg").style.color = ('white');
                  document.getElementById('ServerMsg').innerHTML = ('A szerverrel való kommunikáció megszakadt. Próbáld meg újratölteni az oldalt.');
               };

               let handlers = {
                "set-background-color": function(m) {
        // ...
                console.log('[Client] set-background-color handler running.');
                console.log('[Client] Color is ' + m.params.color);
                document.getElementById('webSocketState').style.backgroundColor = (m.params.color);
                }
            };


               function handleMessage(m) {

                if (m.method == undefined) {
                    return;
                }

                let method = m.method;

                if (method) {

                    if (handlers[method]) {
                        let handler = handlers[method];
                        handler(m);
                    } else {
                        console.log('[Client] No handler defined for method ' + method + '.');
                    }

                }
        }
            } else {
              
               // The browser doesn't support WebSocket
               console.log("WebSocket NOT supported by your Browser!");
            }
*/
var goStatus = 0;
function checkGoBtn() {
      $("#add").one('click', function () { 

    console.log(selectList.lenght);
    

    if (selectList.length >= 1){
      console.log("Gombhozzáadás");
      if (goStatus == 0){
        $('#sendQueryButtonLoc').append('<button type="submit" class="btn btn-success go_btn mb-2 mr-sm-2" id="goButton" >Go!</button>');
        goStatus++;
      }
     }

     if (selectList.lenght == 0){
      console.log("GOMBTÖRLÉS");
      $('#goButton').remove();}
    });

      $("#add").on('click', function () { 
    if (selectList.lenght == 0){
      console.log("GOMBTÖRLÉS");
      $('#goButton').remove();}
    });
      }</script>
<html >
<head>
  <script src="JTranslations.js"></script>
  <link rel="stylesheet" href="./main.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="./utility/_initMenu.js" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="utility/jstree.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
      <title><?php echo $applicationTitleFull;?></title>
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
              drawMenuItemsLeft('takeout',menuItems);
            });
            </script>
            <li><a class="nav-link disabled" href="#"><?php echo $nav_timeLockTitle;?> <span id="time"><?php echo $nav_timeLock_StartValue;?></span></a></li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit"><?php echo $nav_logOut;?></button>
                      </form>
            <a class="nav-link my-2 my-sm-0" href="./help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
		</nav>


  <body ><!--style="background-color:#DCDCDC"-->


</select>

		<div class="container">
    <button id="takeout2BTN">Új kivétel teszt</button>
			<br /><br />
			<h2 class="rainbow" align="center" id="doTitle"><?php echo $applicationTitleShort;?></h2><br />
      <div class="row">
      <div class="col-md-3">
      <div class="alert alert-info"><?php echo $Welcomemsg_takeout?></div>
      </div></div>
			<div class="form-group">
        <table id="itemSearch" align="left"><tr><td><div class="autocomplete" method="GET">
    				<input id="id_itemNameAdd" type="text" name="add" class="form-control mb-2 mr-sm-2" placeholder='<?php echo $applicationSearchField;?>'></div></td>
            <td><button type="button" name="add" id="add" class="btn btn-info2 add_btn mb-2 mr-sm-2" onclick="checkGoBtn()"><?php echo $button_Add;?></button>     <span id='sendQueryButtonLoc'></span></td>
            <td><div class="col-md-9">
      Keresés: <input type="text" id="search" autocomplete="off" /><button id="clear">Törlés</button>
<div id="jstree">
</div>
<p>Selected items:</p>
<ul id="output">
</ul>
      </div></td>
  			</tr></table>
			<form autocomplete="off" action="/index.php">
			</form>
					<div class="table-responsive">
						<table class="table table-bordered table-dark" id="dynamic_field">
				<form name="sendRequest" method="POST" action='/index.php'>
							<!--<tr>
								<td><button type="button" name="aadd" id="addd" class="btn btn-warning">Add More lines for items</button></td>
							</tr>-->
              
						</table>
						<!--<div id="livesearch"></div>
							<input type="submit" name="subm" value="Take Out!" class="btn btn-primary"/>
					</div>-->
				</form>
        <table class="table table-bordered livearray" id="liveSelArrayResult"><td></td></table>
        
			</div>
		</div>
	</body>
<footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p><?php echo $applicationTitleFull; ?> <strong>ver. <?php echo $application_Version; ?></strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a></p></div></footer>
</html>
<script>


//Load takeOutItems.json
d=({})


function loadJSON(callback) {   
console.log("loadJSON function called")
var xobj = new XMLHttpRequest();
    xobj.overrideMimeType("application/json");
xobj.open('GET', './utility/takeOutItems.json', false); // Replace 'my_data' with the path to your file
xobj.onreadystatechange = function () {
      if (xobj.readyState == 4 && xobj.status == "200") {
        // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
        callback(xobj.responseText);
        d=JSON.parse(xobj.responseText);
        //console.log("SYNC end:"+d)

      }
};
xobj.send(null);  
}

function renameKey ( obj, oldKey, newKey ) {
  obj[newKey] = obj[oldKey];
  delete obj[oldKey];
}

loadJSON(function(response) {
  // Parse JSON string into object
  console.log("loadJSON done");
 });
for (let i = 0; i < d.length; i++) {
  renameKey(d[i],'Nev','text');
  renameKey(d[i],'ID','id');
}


console.log(d)
 

$('#jstree').jstree({
  'plugins': ['search', 'checkbox', 'wholerow'],
  'core': {
    'data': d,
    'animation': true,
    'expand_selected_onload': true,
    'themes': {
      'icons': false,
    }},
  'search': {
    'show_only_matches': true,
    'show_only_matches_children': true
  }
});
/*
$('#jstree').jstree({
    'core' : {
        'data' : d,

        "themes":{
            "icons":false
        }
    },
    "search": {
        "show_only_matches": true,
        "show_only_matches_children": true
    },
    "plugins" : ["checkbox", "search"]

});*/

$('#search').on("keyup change", function () {
  $('#jstree').jstree(true).search($(this).val())
})

$('#clear').click(function (e) {
  $('#search').val('').change().focus()
})
//JSON Object of selectted Items:
takeOutPrepJSON = {
  'items':[]
}
$('#jstree').on("changed.jstree", function (e, data) {
  len=$('#jstree').jstree().get_selected(true).length
  for (i=0; i < len; i++){
    itemName=$('#jstree').jstree().get_selected(true)[i].text
    itemId=$('#jstree').jstree().get_selected(true)[i].id
    //var item = takeOutPrepJSON[i];   
    itemArr={};
    itemArr.name=itemName;
    itemArr.id=itemId;
    takeOutPrepJSON.items[i]=itemArr;
    //takeOutPrepJSON.items[i].name=$('#jstree').jstree().get_selected(true)[i].text
    //takeOutPrepJSON.items[i].id=$('#jstree').jstree().get_selected(true)[i].id
    console.log("takeOutPrepJSON:"+takeOutPrepJSON);
  }
    }).jstree();

$('#jstree').on("changed.jstree", function (e, data) {
  console.log(data.instance.get_selected(true).text);
});

$('#jstree').on('changed.jstree', function (e, data) {
  var objects = data.instance.get_selected(true)
  var leaves = $.grep(objects, function (o) { return data.instance.is_leaf(o) })
  var list = $('#output')
  list.empty()
  $.each(leaves, function (i, o) {
    $('<li/>').text(o.text).appendTo(list)
  })
})

$('#jstree_demo').jstree({
  "core" : {
    "animation" : 0,
    "check_callback" : true,
    "themes" : { "stripes" : true },
    'data' : {
      'url' : function (node) {
        return node.id === '#' ?
          'ajax_demo_roots.json' : 'ajax_demo_children.json';
      },
      'data' : function (node) {
        return { 'id' : node.id };
      }
    }
  },
  "types" : {
    "#" : {
      "max_children" : 1,
      "max_depth" : 4,
      "valid_children" : ["root"]
    },
    "root" : {
      "icon" : "/static/3.3.10/assets/images/tree_icon.png",
      "valid_children" : ["default"]
    },
    "default" : {
      "valid_children" : ["default","file"]
    },
    "file" : {
      "icon" : "glyphicon glyphicon-file",
      "valid_children" : []
    }
  },
  "plugins" : [
    "contextmenu", "dnd", "search",
    "state", "types", "wholerow"
  ]
});
//Right at load - start autologout.

  var selectList = [];
  var i=1;
  $(document).ready(function(){
  
//get items from takeOutItems.json


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

  
	  $('#add').click(function(){

      document.getElementById("liveSelArrayResult").innerHTML = "";

      var currentItemSel = document.getElementById("id_itemNameAdd").value;
      if (currentItemSel == ''){}
      if (currentItemSel != ''){
        document.cookie = "Cookie_currentItemSel = "+currentItemSel;
        var PATTERN = currentItemSel,
        dbTempDelIndex = dbItems.indexOf(currentItemSel);
        console.log(dbTempDelIndex);
        if (dbTempDelIndex==-1){
          alert("Specified item NOT in list!");
        }
        else {
          i++;
          dbItems.splice(dbTempDelIndex,1);
          //console.log(dbItems);
          document.getElementById('id_itemNameAdd').value = '';
          selectList.push(currentItemSel);
          checkGoBtn();
          $('#liveSelArrayResult').append('<td>'+selectList+'</td>');
          $('#dynamic_field').append('<tr id="row'+i+'"><td>'+currentItemSel+'</td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn'+i+' btn_remove">X</button></td></tr>');
         console.log(i + "id with "+ currentItemSel + " created and occupied.");
        }
      }  
   });
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
    
    
  
  

	$(document).on('click', '.btn_remove', function(){
    var button_id = $(this).attr("id");
    dbItems.push(selectList[button_id-2]);
    dbItems.sort;
    //selectList[button_id-2]=null;
    selectList.splice(selectList[button_id-2], 1);
    $('#row'+button_id+'').remove();
    console.log(selectList.length);
    checkGoBtn();
    document.getElementById("liveSelArrayResult").innerHTML = "";
    $('#liveSelArrayResult').append('<td>'+selectList+'</td>');

    // clear Empty items in selectList - WILL BE USED LATER TO NOT MIX UP ID CONSTRUCTION!

    
    //selectList = selectList.filter(Boolean);
    
	});

  $(document).on('click', '.go_btn', function(){
      var filtered = selectList.filter(function (el) {
      return el != null;
    });
      console.log(filtered);
      takeOutJSON = JSON.stringify(filtered);
      console.log(takeOutJSON);
      $.ajax({
    type: 'POST',
    data: {data : takeOutJSON},
    success: function (response) {
      $('#doTitle').animate({'opacity': 0}, 400, function(){
        $(this).html('<h2 class="text text-success" role="alert">'+takeout_Success+'</h2>').animate({'opacity': 1}, 400);
        $(this).html('<h2 class="text text-success" role="alert">'+takeout_Success+'</h2>').animate({'opacity': 1}, 3000);
        $(this).html('<h2 class="text text-success" role="alert">'+takeout_Success+'</h2>').animate({'opacity': 0}, 400);
    setTimeout(function() { $("#doTitle").text("Arpad Media IO").animate({'opacity': 1}, 400); }, 3800);;});
    $('#dynamic_field').empty();
    var selectList = [];
    console.log("selectList:"+selectList);
    $('#goButton').fadeOut();
    },//window.location.href = './takeout.php?state=Success';;
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }
})
    });
  
  $(document).on('click', '.add_btn', function(){
    itemCheckJSON = JSON.stringify(selectList[selectList.length-1]);
    console.log(itemCheckJSON)
    $.ajax({
    //url: 'utility/Takeout_Handler.php',
    type: 'POST',
    data: {takeoutCheck : itemCheckJSON},
    //dataType: 'json',
    success: function (res) {
          var tempAddCheck = res;
          console.log("Tempcheck: " + tempAddCheck);
          if (tempAddCheck == 0){console.log("Nincs hiba, folytatás");}
          if (tempAddCheck == 1){
            console.log("Err1 - Adatbázishiba, vagy a tárgy már ki van véve")
            $('.btn'+(selectList.length+1)).click();
            checkGoBtn();
           //ERROR 001
            $('#doTitle').animate({'opacity': 0}, 400, function(){
        $(this).html('<h2 class="text text-warning" role="alert">'+takeout_Unavailible+'</h2>').animate({'opacity': 1}, 400);
        $(this).html('<h2 class="text text-warning" role="alert">'+takeout_Unavailible+'</h2>').animate({'opacity': 1}, 3000);
        $(this).html('<h2 class="text text-warning" role="alert">'+takeout_Unavailible+'</h2>').animate({'opacity': 0}, 400);
    setTimeout(function() { $("#doTitle").text("Arpad Media IO").animate({'opacity': 1}, 400); }, 3800);;});
          }
          
          },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }
})
    });


  document.getElementById("takeout2BTN").addEventListener("click", function() {
    console.log("Kimenet:"+JSON.stringify(takeOutPrepJSON));
    $.ajax({
      url:"./utility/takeout_administrator.php",
      //url:"./utility/dummy.php",
			method:"POST",
			data:{takeoutData: takeOutPrepJSON},
			success:function(response)
			{
        console.log(response);
        location.reload();
			}
		});
});
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
    background-color: #ffffff; /*#363636 */
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

  .livearray{
    display:none;
  }

  .jstree-hidden{
    display: none;
  }
</style>