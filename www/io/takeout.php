<?php
namespace Mediaio;
require_once __DIR__.'/./ItemManager.php';
use Mediaio\itemDataManager;
setcookie("user_roleLevel",$_SESSION['role'],0,);
/* takeOut szabályok

Status!=1 Nem vehető ki!
TakeRestrict='' bárki kiveheti
TakeRestrict='s' stúdiós és afeletti veheti ki.
TakeRestrict='*' sysadmin veheti ki.
*/
include "header.php";



if(!isset($_SESSION['userId'])){
  header("Location: index.php?error=AccessViolation");
  exit();
}

//Update takeoutItems.json
itemDataManager::generateTakeoutJSON();

$SESSuserName = $_SESSION['UserUserName'];

error_reporting(E_ALL ^ E_NOTICE);
?>

<script src="utility/jstree.js"></script>
  <link href='main.css' rel='stylesheet' />
  <link href="utility/themes/default/style.min.css" rel="stylesheet"/>
<html >
      <title>MediaIo - takeout</title>
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
          drawMenuItemsLeft('takeout', menuItems);
        });
      </script>
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav> <?php  } ?>


  <body ><!--style="background-color:#DCDCDC"-->


</select>

		<div class="container">
    
			<br /><br />
			<h2 class="rainbow" align="center" id="doTitle">Tárgy kivétel</h2><br />
			<div class="form-group">
        <table id="itemSearch" align="left"><tr>
        <td class="selectedItemsDisplay" rowspan="2" style="text-align:left;vertical-align:top;padding:0;min-width:300px;">
          <h3><u>Kiválasztva:</u></h3>
          <ul id="output"></ul>
        </td>
    				<td><div class="col-md-9">
      Keresés: <input type="text" id="search"  style='margin-bottom: 10px' placeholder="Kezdd el ide írni, mit vinnél el.." autocomplete="off" /></br><button class="btn btn-warning" id="clear">Keresés törlése</button> <button class="btn btn-success" id="takeout2BTN">Mehet</button>
<div id="jstree">
</div>

      </div></td>
  			</tr></table>
			<form autocomplete="off" action="/index.php">
			</form>
					<div class="table-responsive">
						<table class="table table-bordered table-dark" id="dynamic_field">
				<form name="sendRequest" method="POST" action='/index.php'>
						</table>
				</form>
        <table class="table table-bordered livearray" id="liveSelArrayResult"><td></td></table>
        
			</div>
		</div>
	</body>
</html>
<script>


//Load takeOutItems.json
d=({})

function displayMessageInTitle(selector,message){
  baseText=$(selector).text();
  $(selector).animate({'opacity': 0}, 400, function(){
        $(this).html('<h2 class="text text-success" role="alert">'+message+'</h2>').animate({'opacity': 1}, 400);
        $(this).html('<h2 class="text text-success" role="alert">'+message+'</h2>').animate({'opacity': 1}, 3000);
        $(this).html('<h2 class="text text-success" role="alert">'+message+'</h2>').animate({'opacity': 0}, 400);
    setTimeout(function() { $(selector).text(baseText).animate({'opacity': 1}, 400); }, 3800);;});
}



function loadJSON(callback) {   
console.log("[loadJSON] - called.")
var xobj = new XMLHttpRequest();
    xobj.overrideMimeType("application/json");
xobj.open('GET', './data/takeOutItems.json', false); // Replace 'my_data' with the path to your file
xobj.onreadystatechange = function () {
      if (xobj.readyState == 4 && xobj.status == "200") {
        // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
        callback(xobj.responseText);
        //console.log(xobj.responseText);
        d=JSON.parse(xobj.responseText);
        //setTimeout(function(){ //console.log(JSON.parse(xobj.responseText));; }, 500);
      }
};
xobj.send(null);  
}

function getCookie(cName) {
  const name = cName + "=";
  const cDecoded = decodeURIComponent(document.cookie); //to be careful
  const cArr = cDecoded.split('; ');
  let res;
  cArr.forEach(val => {
    if (val.indexOf(name) === 0) res = val.substring(name.length);
  })
  return res
}

function renameKey ( obj, oldKey, newKey ) {
  obj[newKey] = obj[oldKey];
  delete obj[oldKey];
}

loadJSON(function(response) {
  // Parse JSON string into object
  console.log("[loadJSON] - done");
 });

//megjelenítés felhasználó roleLevel-je alapján:
var roleNum=getCookie("user_roleLevel");
  for (let i = 0; i < d.length; i++) {
  renameKey(d[i],'Nev','text');
  renameKey(d[i],'ID','id');
  renameKey(d[i],'UID','uid');
  //alert(d[i].uid);
  if(d[i].TakeRestrict=='s' && roleNum<2){// nem stúdiós, vagy afölötti
    d[i].state.disabled=true;
  }
  else if(d[i].TakeRestrict=='*' && roleNum<5){// nem sysadmin
    d[i].state.disabled=true;
  }
  d[i].originalName=d[i].text;
  d[i].restrict=d[i].TakeRestrict;
  if(d[i].restrict!=''){
    d[i].text=d[i].text+' - '+d[i].uid+'('+ d[i].restrict+')';
  }else{
    d[i].text=d[i].text+' - '+d[i].uid;
  } 
}

$('#jstree').jstree({
  "plugins": ["search", "checkbox", "wholerow"],
  "core": {
    "data": d,
    "animation": true,
    "expand_selected_onload": true,
    "themes": {
      "icons": false,
    }},
  "search": {
    "show_only_matches": true,
    "show_only_matches_children": true
  }
});

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
 
function deselect_node(ID){
  $("#jstree").jstree("deselect_node", ID);
  //Elem törlése a kijelölt elemek közül.
  var tmp_filtered = $.grep(takeOutPrepJSON['items'], function(e){ 
     return e.id != ID; 
});
takeOutPrepJSON['items']=tmp_filtered;
}

$('#jstree').on("changed.jstree", function (e, data) {

  len=$('#jstree').jstree().get_selected(true).length
  for (i=0; i < len; i++){
    itemName=$('#jstree').jstree().get_selected(true)[i].original.originalName;
    //alert(itemName);
    itemId=$('#jstree').jstree().get_selected(true)[i].id;
    //itemUid=$('#jstree').jstree().get_selected(true)[i].uid;
    //var item = takeOutPrepJSON[i];   
    itemArr={};
    itemArr.name=itemName;
    itemArr.id=itemId;
    //itemArr.uid=itemUid;
    takeOutPrepJSON.items[i]=itemArr;
    //takeOutPrepJSON.items[i].name=$('#jstree').jstree().get_selected(true)[i].text
    //takeOutPrepJSON.items[i].id=$('#jstree').jstree().get_selected(true)[i].id
    console.log("takeOutPrepJSON:"+takeOutPrepJSON.items);
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
    iName=o.text;
    console.log(o);
    //$('<li/>').appendTo(list);
    toAdd=o.text+'<button class="btn btn-danger removeSelection" onclick="deselect_node('+o.id+')" id="deselectBtn_'+i+'">X</button>';
    //console.log(toAdd);
    $('<li/>').html(toAdd).appendTo(list);
  })
})



$('#jstree').jstree().refresh();


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
  document.getElementById("takeout2BTN").addEventListener("click", function() {
    if (takeOutPrepJSON.items.length==0){
      displayMessageInTitle("#doTitle","Nem választottál ki semmit!");
      return;
    }
    console.log("Kimenet:"+JSON.stringify(takeOutPrepJSON));
    //alert("Kimenet:"+JSON.stringify(takeOutPrepJSON));
      $.ajax({
      url:"./utility/takeout_administrator.php",
      //url:"./utility/dummy.php",
			method:"POST",
			data:{takeoutData: takeOutPrepJSON},
			success:function(response)
			{
        console.log(response);
        if(response=='200'){
          displayMessageInTitle("#doTitle","Sikeres kivétel! \nAz oldal hamarosan újratölt");
          $('#jstree').jstree(true).settings.core.data = d;
          //Fa újratöltése
          setTimeout(() => {  $('#jstree').jstree().refresh(); }, 2000);
          setTimeout(() => {  window.location.href = window.location.href }, 1000);
        }else{
          displayMessageInTitle("#doTitle","Hiba történt.");
        }

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
				//alert(data);
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

</script>

<style>
  * {
    box-sizing: border-box;
  }

  .btn-info2{color:white;background-color:#000658;border-color:#000658;border-width:2px}.btn-info2:hover{color:black;background-color:#ffffff;border-color:#000658;border-width:2px}

  body {
    font: 16px Arial;
    background-color: #ffffff; /*#363636 */
    background: transparent;
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

  .selectedItemsDisplay ul{
    list-style: none;
    background-color: #777777;
    color: white;
    font-size: 20px;
    
  }

  .selectedItemsDisplay li{
    list-style-type:none;
    position: relative;
    left: -30px;
    /*background-color: #D3D3D3;*/
    margin: 5px 0;
  }

</style>