<?php
namespace Mediaio;
require_once __DIR__.'/./ItemManager.php';
use Mediaio\itemDataManager;

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
        <a class="nav-link disabled timelock" href="#"><span id="time"> 30:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc,30);
        };
      </script>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav> <?php  } 
//Limit GivetoAnotherperson modal to admin users only
if(in_array("system", $_SESSION["groups"]) or in_array("admin", $_SESSION["groups"])){
  ?>
<!-- GivetoAnotherperson Modal -->
            <div class="modal fade" id="givetoAnotherPerson_Modal" tabindex="-1" role="dialog" aria-labelledby="givetoAnotherPerson_ModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Eszköz kivétele más helyett</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <!-- Perform an ajax query to ItemManager.php -->
                    <div id='givetoAnotherPerson_UserName_Field'>

                    <label for="givetoAnotherPerson_UserName">Felhasználó neve:</label>
                    <select id="givetoAnotherPerson_UserName" name="givetoAnotherPerson_UserName" class="form-control" required>
                      <option value="" disabled selected>Válassz felhasználót</option>
                    </select>
                      
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
<!-- End of GivetoAnotherperson Modal -->
<?php  } ?>
<!-- Presets Modal -->
            <div class="modal fade" id="presets_Modal" tabindex="-1" role="dialog" aria-labelledby="presets_ModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Elérhető presetek</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div id="presetsLoading" class="spinner-grow text-info" role="status"></div>
                    <div id="presetsContainer"></div>
                      
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
<!-- End of Presets Modal -->
  <body>
			<br /><br />
			<h2 class="rainbow" align="center" id="doTitle">Tárgy kivétel</h2><br />
  <div class="container">
  <div class="row align-items-start">
    <div class="col-4">
      <h3 style='text-align: center'>Kiválasztva</h3>
      <ul class="selectedItemsDisplay" id="output"></ul>
    </div>
    <div class="col-8">
      Keresés: <input type="text" id="search"  style='margin-bottom: 10px' placeholder="Kezdd el ide írni, mit vinnél el.." autocomplete="off" /></br><button class="btn btn-warning" id="clear" style='margin-bottom: 6px'>Keresés törlése</button> <button class="btn btn-success" id="takeout2BTN" style='margin-bottom: 6px'>Mehet</button> <button class="btn btn-info" onclick="showPresetsModal()" style='margin-bottom:6px'>Presetek</button> <button id="givetoAnotherPerson_Button" type="button" class="btn btn-dark" data-toggle="modal" data-target="#givetoAnotherPerson_Modal" style="visibility: hidden; margin-bottom: 6px">Másnak veszek ki</button>
      <div id="jstree">
      </div>
    </div>
    </div>
  </div>
</div>
  </body>


  <!-- Navigation back to top -->
  <div id='toTop'><i class="fas fa-chevron-up"></i></div>
</html>
<script>
function showPresetsModal(){
  $('#presets_Modal').modal('show');

  //get Preset Items
    $.ajax({
			url:"ItemManager.php",
			method:"POST",
			data:{mode: "getPresets"},
			success:function(response)
			{


        //Convert rerponse to JSON
        var presets = JSON.parse(response);
        takeoutPresets=[];
        //For each user add a select option to givetoAnotherPerson_UserName
        if(presets.length>0){
          $('#presetsLoading').hide();
        }
        $('#presetsContainer').html('');

        for (var i = 0; i < presets.length; i++) {
          console.log(presets[i]);
          takeoutPresets.push(presets[i]);
          $("#presetsContainer").append('<button class="btn btn-info2" onclick="addItems('+i+')">'+presets[i].Name+'</button></br></br>');
        }
			}
		});

}

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
  renameKey(d[i],'ConnectsToItems','relatedItems');
  //alert(d[i].uid);

  if(d[i].Status=='0' || d[i].Status=='2'){ //Taken out or waiting for UserCheck
    d[i].state.disabled=true;
  }else{
    //Sysadmin bypass
   if(<?php echo in_array('system',$_SESSION['groups'])?'true':'false' ?>){//stúdiós restrict
    d[i].state.disabled=false;
  }else{
    if(d[i].TakeRestrict=='s' && <?php echo (in_array('studio',$_SESSION['groups']) || in_array('admin',$_SESSION['groups']))?'false':'true' ?>){//stúdiós restrict
      d[i].state.disabled=true;
    }
    if(d[i].TakeRestrict=='*'){
      d[i].state.disabled=true;
    }
    if(d[i].TakeRestrict=='e' && <?php echo (in_array('event',$_SESSION['groups']) || in_array('admin',$_SESSION['groups']))?'false':'true' ?>){// event eszköz restrict
      d[i].state.disabled=true;
    }
  }
  }



  d[i].originalName=d[i].text;
  d[i].childFlag=false;
  d[i].activeRelatedItems=d[i].relatedItems;
  d[i].restrict=d[i].TakeRestrict;
  if(d[i].restrict!=''){
    d[i].text=d[i].text+' - '+d[i].uid+'('+ d[i].restrict+')';
  }else{
    d[i].text=d[i].text+' - '+d[i].uid;
  } 
}

//Invoked after JStree is loaded
$('#jstree').bind('ready.jstree', function(e, data) {
  // console.log("[tree loaded] - running coloring");
  // //wait 100ms
  // for(i=1;i<=d.length;i++){
  //   if($('#jstree').jstree().get_node(i).original.Status=='2' || $('#jstree').jstree().get_node(i).original.Status=='0' ){
  //     $("#jstree ul li:nth-child("+i+") a").attr('takeout', 'true');
  //     $("#jstree ul li:nth-child("+i+") a").css({"background-color":"green","font-size":"20px","color":"red"});
  //     console.log("[jstree] - running - "+i);
  //     console.log($("#jstree ul li:nth-child("+i+") a"));
  //   }
  // }

  // //Update style for items that are taken out.
  // $('*[takeout="true"]').css({"background-color":"green","font-size":"20px","color":"red"});
});

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
    "show_only_matches_children": true,
    "case_sensitive": false
  }
});

$('#search').on("keyup change", function () {
  $('#jstree').jstree(true).search($(this).val())
  colorTakenItems();


})

$('#clear').click(function (e) {
  $('#search').val('').change().focus()
})
//JSON Object of selectted Items:
takeOutPrepJSON = {
  'items':[]
}
 
function deselect_node(ID){
  //Get node UID
  var nodeUid=$('#jstree').jstree().get_node(ID).original.uid;
  //Deselect the node
  $('#jstree').jstree().deselect_node(ID);
  var tmp_filtered = $.grep(takeOutPrepJSON['items'], function(e){ 
     return e.id != ID; 
});
takeOutPrepJSON['items']=tmp_filtered;
}

//Add Preset Items
function addItems(id){

    selectionArray=[];
    addArray=JSON.parse(takeoutPresets[id].Items).items;
    addArray.forEach(element => {
        for (j=1; j <= d.length; j++){
          if($('#jstree').jstree().get_node(j).original.uid==element & $('#jstree').jstree().get_node(j).state.disabled==false){
            selectionArray.push(j);
          }
        }
      $('#jstree').jstree().select_node(selectionArray);
      })
    };

$('#jstree').on("changed.jstree", function (e, data) {
  if(data.action=="select_node"){
    itemArr={};
    itemArr.id=data.node.id;
    itemArr.name=data.node.original.originalName;
    takeOutPrepJSON.items.push(itemArr);
    selectionArray=[];
    objects=JSON.parse($('#jstree').jstree().get_node(data.node.id).original.activeRelatedItems);
      if(objects!=null){
        for (k=0; k < objects.length; k++){
          for (j=1; j <= d.length; j++){
            if($('#jstree').jstree().get_node(j).original.uid==objects[k] & $('#jstree').jstree().get_node(j).state.disabled==false){
              selectionArray.push(j);
              //$('#jstree').jstree().get_selected(true)[i].original.childFlag=true;
            } 
          }
        }
      }
    //Run selection
    $('#jstree').jstree().select_node(selectionArray);
  }else if(data.action=="deselect_node"){
    //Deselecting node should NOT affects the relatedItems.
    deselect_node(data.node.id);
  }else if(data.action=="deselect_all"){
    //
  }
  if(containsOnlyStudioItems() && <?php echo (in_array('system',$_SESSION['groups']) || in_array('admin',$_SESSION['groups']))?'true':'false' ?>){
      $(`#givetoAnotherPerson`).css('visibility','visible')
      $(`#givetoAnotherPerson_Button`).css('visibility','visible')
    }else{
      $(`#givetoAnotherPerson`).css('visibility','hidden')
      $(`#givetoAnotherPerson_Button`).css('visibility','hidden')
  }
    }).jstree();

$('#jstree').on('changed.jstree', function (e, data) {
  var objects = data.instance.get_selected(true)
  var leaves = $.grep(objects, function (o) { return data.instance.is_leaf(o) })
  var list = $('#output')
  list.empty()
  $.each(leaves, function (i, o) {
    iName=o.text;
    //console.log(o);
    toAdd=o.text+'<button class="btn btn-danger removeSelection" onclick="deselect_node('+o.id+')" id="deselectBtn_'+i+'">X</button>';
    //console.log(toAdd);
    $('<li/>').html(toAdd).appendTo(list);
  })
})



$('#jstree').jstree().refresh();
$('*[takeout-info="out"]').css({"font-size":"12px","color":"red"});
//Right at load - start autologout.

  var selectList = [];
  var i=1;

  //Change color of items that are taken out or waiting for usercheck
function colorTakenItems(){
    for(a=1;a<=d.length;a++){
      if($('#jstree').jstree().get_node(a).original.Status=='2' || $('#jstree').jstree().get_node(a).original.Status=='0' ){
        $("#jstree ul li:nth-child("+a+") a").attr('takeout', 'true');
        $("#jstree ul li:nth-child("+a+") a").css({
          "font-size":"17px",
          "color":"#ebcc83",
          "text-decoration": "line-through !important",
          "font-weight": "normal !important"
        });
        $("#jstree ul li:nth-child("+a+") a").removeClass("jstree-search");
      }
      
    }
  }

  function containsOnlyStudioItems(){
    if(takeOutPrepJSON.items.length==0){
      return false;
    }
    for(j=0;j<takeOutPrepJSON.items.length;j++){
      if($('#jstree').jstree().get_node(parseInt(takeOutPrepJSON.items[0].id)).original.TakeRestrict!='s'){
        return false;
      }
    }
    return true;
  }

  $(document).ready(function(){
    //Color taken items
    setTimeout(function (){
      colorTakenItems();
    }, 500);

    //Back to top button
    $(window).scroll(function() {
    if ($(this).scrollTop()) {
          $('#toTop').fadeIn();
      } else {
          $('#toTop').fadeOut();
      }
    });


    //get Users
    $.ajax({
			url:"ItemManager.php",
			method:"POST",
			data:{mode: "getUsers"},
			success:function(response)
			{
        //alert(response);

        //Convert rerponse to JSON
        var users = JSON.parse(response);
        //For each user add a select option to givetoAnotherPerson_UserName
        for (var i = 0; i < users.length; i++) {
          $('#givetoAnotherPerson_UserName').append($('<option>', {
              value: users[i].usernameUsers,
              text: users[i].usernameUsers
          }));
        }
			}
		});

    $("#toTop").click(function() {
        $("html, body").animate({scrollTop: 0}, 1000);
     });


  document.getElementById("takeout2BTN").addEventListener("click", function() {
    if (takeOutPrepJSON.items.length==0){
      displayMessageInTitle("#doTitle","Nem választottál ki semmit!");
      return;
    }

    console.log("Kimenet:"+JSON.stringify(takeOutPrepJSON));
      $.ajax({
      url:"./utility/takeout_administrator.php",
      //url:"./utility/dummy.php",
			method:"POST",
			data:{takeoutData: takeOutPrepJSON, takeoutAsUser: $('#givetoAnotherPerson_UserName').val()},
			success:function(response)
			{
        if(response=='200'){
          displayMessageInTitle("#doTitle","Sikeres kivétel! \nAz oldal hamarosan újratölt");
          $('#jstree').jstree(true).settings.core.data = d;
          //Fa újratöltése
          setTimeout(() => {  $('#jstree').jstree().refresh(); }, 2000);
          setTimeout(() => {  window.location.href = window.location.href }, 1000);
        }else{
          //console.log(response);
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

  .selectedItemsDisplay{
    list-style: none;
    background-color: #777777;
    color: white;
    font-size: 17px;
    padding-bottom: 0.2rem;
    padding-top: 0.2rem;
  }

  .selectedItemsDisplay li{
    list-style-type:none;
    position: relative;
    left: -30px;
    /*background-color: #D3D3D3;*/
    margin: 5px 0;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: center;
  }

  #toTop{
    position: fixed;
    bottom: 20px;
    right: 30px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
    background-color: #000658;
    color: white;
    cursor: pointer;
    padding: 15px;
    display:none;
    border-radius: 50%;
  }

  #toTop:hover {
    background-color: #555;
    animation-name: changefontsize;
    animation-duration: 0.5s;
    font-size: 22px;
  }

  @keyframes changefontsize {
  from {font-size: 18px;}
  to {font-size: 22px;}
}



  

</style>
