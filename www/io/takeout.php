<?php

namespace Mediaio;

require_once __DIR__ . '/./ItemManager.php';

use Mediaio\itemDataManager;

include "header.php";



if (!isset($_SESSION['userId'])) {
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
<link href="utility/themes/default/style.min.css" rel="stylesheet" />
<html>
<title>MediaIo - takeout</title>
<?php if (isset($_SESSION["userId"])) { ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
      <img src="./utility/logo2.png" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
        <script>
          $(document).ready(function () {
            menuItems = importItem("./utility/menuitems.json");
            drawMenuItemsLeft('takeout', menuItems);
          });
        </script>
      </ul>
      <ul class="navbar-nav ms-auto navbarPhP">
        <li>
          <a class="nav-link disabled timelock" href="#"><span id="time"> 30:00 </span>
            <?php echo ' ' . $_SESSION['UserUserName']; ?>
          </a>
        </li>
      </ul>
      <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
        <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
          type="submit">Kijelentkezés</button>
        <script type="text/javascript">
          window.onload = function () {
            display = document.querySelector('#time');
            var timeUpLoc = "utility/userLogging.php?logout-submit=y"
            startTimer(display, timeUpLoc, 30);
          };
        </script>
      </form>
    </div>
  </nav>
<?php }
//Limit GivetoAnotherperson modal to admin users only
if (in_array("system", $_SESSION["groups"]) or in_array("admin", $_SESSION["groups"])) {
  ?>
  <!-- GivetoAnotherperson Modal -->
  <div class="modal fade" id="givetoAnotherPerson_Modal" tabindex="-1" role="dialog"
    aria-labelledby="givetoAnotherPerson_Modal_Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="givetoAnotherPerson_Modal_Label">Eszköz kivétele más helyett</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
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
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- End of GivetoAnotherperson Modal -->
<?php } ?>
<!-- Presets Modal -->
<div class="modal fade" id="presets_Modal" tabindex="-1" role="dialog" aria-labelledby="presets_ModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Elérhető presetek</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="presetsLoading" class="spinner-grow text-info" role="status"></div>
        <div id="presetsContainer"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End of Presets Modal -->



<body>
  <h2 class="rainbow" id="doTitle">Tárgy kivétel</h2>
  <div class="container">
    <div class="row align-items-start" id="takeout-container">
      <div class="col-4" id="selected-desktop">
        <h3>Kiválasztva:</h3>
        <ul class="selectedItemsDisplay" id="output-desktop"></ul>
      </div>
      <div class="col">
        Keresés: <input type="text" id="search" style='margin-bottom: 10px'
          placeholder="Kezdd el ide írni, mit vinnél el.." autocomplete="off" />
        <div class="row optionsButtons" id="takeout-option-buttons">
          <!-- <button class="btn btn-warning col-2 mx-1" id="clear" style='margin-bottom: 6px'>Törlés</button> -->
          <button class="btn btn-sm btn-danger col-lg-auto mb-1 text-nowrap" id="clear" style='margin-bottom: 6px'
            onclick="deselect_all()">Összes törlése</button>
          <button class="btn btn-sm btn-success col-lg-auto mb-1" id="takeout2BTN"
            style='margin-bottom: 6px'>Mehet</button>
          <button class="btn btn-sm btn-info col-lg-auto mb-1" onclick="showPresetsModal()"
            style='margin-bottom:6px'>Presetek</button>
          <button class="btn btn-sm btn-dark col-lg-auto mb-1 text-nowrap" id="givetoAnotherPerson_Button" type="button"
            data-bs-toggle="modal" data-bs-target="#givetoAnotherPerson_Modal" style="margin-bottom: 6px">Másnak veszek
            ki</button>
          <button href="#sidebar" class="btn btn-sm btn-success mb-1" id="show_selected" data-bs-toggle="offcanvas"
            role="button" aria-controls="sidebar">Kiválasztva
            <span id="selectedCount" class="badge bg-danger">0</span>
          </button>

          <!-- Belső használatra kivétel - későbbi release -->
          <!-- <div class="form-check form-switch col-2">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
            <label class="form-check-label text-nowrap" for="flexSwitchCheckDefault">Csak használatra</label>
          </div> -->
        </div>
        <div id="jstree">
        </div>
      </div>


      <!-- Offcanvas -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebar-label">
        <div class="offcanvas-header">
          <h4 class="offcanvas-title" id="sidebar-label">Kiválasztva</h4>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body" id="sidebar-body">
          <div class="row">
            <div class="col-12">
              <ul class="selectedItemsDisplay" id="output-mobile"></ul>
            </div>
            <button class="btn btn-sm btn-success col-lg-auto mb-1" data-bs-dismiss="offcanvas" id="takeout2BTN-mobile">Mehet</button>
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
  //Selected items badge counter
  var badge = document.getElementById("selectedCount");

  //Preventing double click zoom
  document.addEventListener('dblclick', function (event) {
    event.preventDefault();
  }, { passive: false });

  function reloadSavedSelections() {
    //Try re-selectiong items that are saved in the takeOutItems cookie.
    var selecteditems = getCookie("selectedItems").split(',');
    console.log("items that are reloading are :" + selecteditems)
    selecteditems.forEach(element => {
      $('#jstree').jstree().select_node(element);
    });
  }

  function showPresetsModal() {
    $('#presets_Modal').modal('show');

    //get Preset Items
    $.ajax({
      url: "ItemManager.php",
      method: "POST",
      data: {
        mode: "getPresets"
      },
      success: function (response) {


        //Convert rerponse to JSON
        var presets = JSON.parse(response);
        takeoutPresets = [];
        //For each user add a select option to givetoAnotherPerson_UserName
        if (presets.length > 0) {
          $('#presetsLoading').hide();
        }
        $('#presetsContainer').html('');

        for (var i = 0; i < presets.length; i++) {
          console.log(presets[i]);
          takeoutPresets.push(presets[i]);
          $("#presetsContainer").append('<button class="btn mediaBlue position-relative" id="presetButton' + i + '" onclick="addItems(' + i + ')">' + presets[i].Name +
            '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">99+<span class="visually-hidden">unread messages</span></span></button></br></br>');
          //Hide preset badges
        }

        for (var i = 0; i < takeoutPresets.length; i++) {
          $('#presetButton' + i + ' span')[0].innerHTML = '';
        }
      }
    });

  }

  //Load takeOutItems.json
  d = ({})

  function displayMessageInTitle(selector, message) {
    baseText = $(selector).text();
    $(selector).animate({
      'opacity': 0
    }, 400, function () {
      $(this).html('<h2 class="text text-success" role="alert">' + message + '</h2>').animate({
        'opacity': 1
      }, 400);
      $(this).html('<h2 class="text text-success" role="alert">' + message + '</h2>').animate({
        'opacity': 1
      }, 3000);
      $(this).html('<h2 class="text text-success" role="alert">' + message + '</h2>').animate({
        'opacity': 0
      }, 400);
      setTimeout(function () {
        $(selector).text(baseText).animate({
          'opacity': 1
        }, 400);
      }, 3800);;
    });
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
        d = JSON.parse(xobj.responseText);
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

  function renameKey(obj, oldKey, newKey) {
    obj[newKey] = obj[oldKey];
    delete obj[oldKey];
  }

  loadJSON(function (response) {
    // Parse JSON string into object
    console.log("[loadJSON] - done");
  });

  //megjelenítés felhasználó roleLevel-je alapján:
  var roleNum = getCookie("user_roleLevel");
  for (let i = 0; i < d.length; i++) {
    renameKey(d[i], 'Nev', 'text');
    renameKey(d[i], 'ID', 'id');
    renameKey(d[i], 'UID', 'uid');
    renameKey(d[i], 'ConnectsToItems', 'relatedItems');
    //alert(d[i].uid);

    if (d[i].Status == '0' || d[i].Status == '2') { //Taken out or waiting for UserCheck
      d[i].state.disabled = true;
    } else {
      //Sysadmin bypass
      if (<?php echo in_array('system', $_SESSION['groups']) ? 'true' : 'false' ?>) { //stúdiós restrict
        d[i].state.disabled = false;
      } else {
        if (d[i].TakeRestrict == 's' && <?php echo (in_array('studio', $_SESSION['groups']) || in_array('admin', $_SESSION['groups'])) ? 'false' : 'true' ?>) { //stúdiós restrict
          d[i].state.disabled = true;
        }
        if (d[i].TakeRestrict == '*') {
          d[i].state.disabled = true;
        }
        if (d[i].TakeRestrict == 'e' && <?php echo (in_array('event', $_SESSION['groups']) || in_array('admin', $_SESSION['groups'])) ? 'false' : 'true' ?>) { // event eszköz restrict
          d[i].state.disabled = true;
        }
      }
    }

    setTimeout(function() {
      reloadSavedSelections()
    }, 300);



    d[i].originalName = d[i].text;
    d[i].childFlag = false;
    d[i].activeRelatedItems = d[i].relatedItems;
    d[i].restrict = d[i].TakeRestrict;
    if (d[i].restrict != '') {
      d[i].text = d[i].text + ' - ' + d[i].uid + '(' + d[i].restrict + ')';
    } else {
      d[i].text = d[i].text + ' - ' + d[i].uid;
    }
  }

  //Invoked when JSTree is ready
  $('#jstree').bind('ready.jstree', function (e, data) { });


  //Invoked after JStree is loaded
  $('#jstree').bind('loaded.jstree', function (e, data) {
    console.log("Loaded!")

    setTimeout(function () {
      reloadSavedSelections()
    }, 300);
  });


  //Invoked after JStree is loaded
  $('#jstree').bind('loaded.jstree', function(e, data) {
    console.log("Loaded!")


  });

  $('#jstree').jstree({
    "plugins": ["search", "checkbox", "wholerow"],
    "core": {
      "data": d,
      "animation": true,
      "expand_selected_onload": true,
      "themes": {
        "icons": false,
      }
    },
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

  //JSON Object of selectted Items:
  takeOutPrepJSON = {
    'items': []
  }

  function deselect_all() {
    $('#jstree').jstree().deselect_all();
    takeOutPrepJSON['items'] = [];

    decideGiveToAnotherPerson_visibility();
    parseInt(badge.textContent = 0);
    updateSelectionCookie();
  }

  //Deselect a node.
  function deselect_node(ID) {
    //Get node UID
    var nodeUid = $('#jstree').jstree().get_node(ID).original.uid;
    //Deselect the node
    $('#jstree').jstree().deselect_node(ID);
    var tmp_filtered = $.grep(takeOutPrepJSON['items'], function (e) {
      return e.id != ID;
    });
    takeOutPrepJSON['items'] = tmp_filtered;
  }

  //Add Preset Items to the selection
  //ID: takeout preset ID
  function addItems(id) {
    var alreadyTakenCount = 0;
    selectionArray = [];
    takenArray = [];
    addArray = JSON.parse(takeoutPresets[id].Items).items;
    addArray.forEach(element => {
      for (j = 1; j <= d.length; j++) {
        if ($('#jstree').jstree().get_node(j).original.uid == element & $('#jstree').jstree().get_node(j).state.disabled == false) {
          selectionArray.push(j);
        } else if ($('#jstree').jstree().get_node(j).original.uid == element & $('#jstree').jstree().get_node(j).state.disabled == true) {
          takenArray.push($('#jstree').jstree().get_node(j));
          alreadyTakenCount++;
        }
      }
      $('#jstree').jstree().select_node(selectionArray);
    })
    console.log(takenArray);

    //Update badge to display how many items are already taken
    $('#presetButton' + id + ' span')[0].innerHTML = (() => {
      if (alreadyTakenCount > 0) {
        return alreadyTakenCount;
      } else {
        return '';
      }
    })();

    //Id the presetscontainer alredy has a list of taken items, remove it.
    $('#presetsContainer ul').html('');

    //If a h4 already exists, remove it.
    if ($('#presetsContainer h6').length > 0) {
      $('#presetsContainer h6').remove();
    }

    //Inside the presetscontainer, create an unordered list of the taken items
    var takenItemsTitle = $('<h6>Az általad választott presetből a következő tárgyak már ki vannak véve:</h6>');
    var takenItemsList = $('<ul></ul>');
    takenArray.forEach(element => {
      takenItemsList.append('<li>' + element.original.uid + ' - ' + element.original.originalName + '</li>');
    });
    if (takenArray.length > 0) {
      $('#presetsContainer').append(takenItemsTitle);
      $('#presetsContainer').append(takenItemsList);
    }



  };

  //Add items to the selection
  $('#jstree').on("changed.jstree", function (e, data) {
    if (data.action == "select_node") {
      itemArr = {};
      itemArr.id = data.node.id;
      itemArr.name = data.node.original.originalName;
      takeOutPrepJSON.items.push(itemArr);
      selectionArray = [];
      objects = JSON.parse($('#jstree').jstree().get_node(data.node.id).original.activeRelatedItems);
      if (objects != null) {
        for (k = 0; k < objects.length; k++) {
          for (j = 1; j <= d.length; j++) {
            if ($('#jstree').jstree().get_node(j).original.uid == objects[k] & $('#jstree').jstree().get_node(j).state.disabled == false) {
              selectionArray.push(j);
            }
          }
        }
      }
      //Run selection

      $('#jstree').jstree().select_node(selectionArray);
      parseInt(badge.textContent++);

      updateSelectionCookie();
    } else if (data.action == "deselect_node") {
      //Deselecting node should NOT affects the relatedItems.
      deselect_node(data.node.id);

      //Update badge counter
      badge.textContent--;

      updateSelectionCookie();
    } else if (data.action == "deselect_all") {
      //
    }


    decideGiveToAnotherPerson_visibility();

  }).jstree();

  function updateSelectionCookie() {
    console.log("[updateSelectionCookie] - called");
    //Set cookie expire date to 1 day
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    //get IDs of selected items
    var selectedItems = $('#jstree').jstree().get_selected();
    console.log(selectedItems);
    document.cookie = "selectedItems=" + selectedItems + ";" + expires + ";path=/";
  }

  /**
   * Ha csak stúdiós eszközök vannak kiválasztva, akkor engedélyezzük a másik felhasználóra való kivételt.
   */
  function decideGiveToAnotherPerson_visibility() {
    if (containsOnlyStudioItems() && <?php echo (in_array('system', $_SESSION['groups']) || in_array('admin', $_SESSION['groups'])) ? 'true' : 'false' ?>) {
      $(`#givetoAnotherPerson`).css('display', 'block')
      $(`#givetoAnotherPerson_Button`).css('display', 'block')
    } else {
      $(`#givetoAnotherPerson`).css('display', 'none')
      $(`#givetoAnotherPerson_Button`).css('display', 'none')
    }
  }

  $('#jstree').on('changed.jstree', function (e, data) {
    var objects = data.instance.get_selected(true)
    var leaves = $.grep(objects, function (o) {
      return data.instance.is_leaf(o)
    })
    var list;
    if (window.innerWidth < 575) { // Decide if mobile or desktop
      list = $('#output-mobile')
    } else {
      list = $('#output-desktop')
    }
    list.empty()
    $.each(leaves, function (i, o) {
      iName = o.text;
      //console.log(o);
      toAdd = '<span class="selected_name">' + o.text + '</span><button class="btn btn-danger removeSelection" onclick="deselect_node(' + o.id + ')" id="deselectBtn_' + i + '">X</button>';
      //console.log(toAdd);
      $('<li/>').html(toAdd).appendTo(list);
    })
  })



  $('#jstree').jstree().refresh();
  $('*[takeout-info="out"]').css({
    "font-size": "12px",
    "color": "red"
  });
  //Right at load - start autologout.

  var selectList = [];
  var i = 1;

  //Change color of items that are taken out or waiting for usercheck
  function colorTakenItems() {
    for (a = 1; a <= d.length; a++) {
      if ($('#jstree').jstree().get_node(a).original.Status == '2' || $('#jstree').jstree().get_node(a).original.Status == '0') {
        $("#jstree ul li:nth-child(" + a + ") a").attr('takeout', 'true');
        $("#jstree ul li:nth-child(" + a + ") a").css({
          "font-size": "17px",
          "color": "#ebcc83",
          "text-decoration": "line-through !important",
          "font-weight": "normal !important"
        });
        $("#jstree ul li:nth-child(" + a + ") a").removeClass("jstree-search");
        deselect_node(a);
      }

    }
  }

  function containsOnlyStudioItems() {
    if (takeOutPrepJSON.items.length == 0) {
      return false;
    }
    for (j = 0; j < takeOutPrepJSON.items.length; j++) {
      if ($('#jstree').jstree().get_node(parseInt(takeOutPrepJSON.items[0].id)).original.TakeRestrict != 's') {
        return false;
      }
    }
    return true;
  }

  $(document).ready(function() {
    //Color taken items
    setTimeout(function() {
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
      url: "ItemManager.php",
      method: "POST",
      data: {
        mode: "getUsers"
      },
      success: function (response) {
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

    $("#toTop").click(function () {
      $("html, body").animate({
        scrollTop: 0
      }, 1000);
    });

    //Takout gomb a sidebaros listahoz

    document.getElementById("takeout2BTN-mobile").addEventListener("click", function () {
      if (takeOutPrepJSON.items.length == 0) {
        displayMessageInTitle("#doTitle", "Nem választottál ki semmit!");
        return;
      }

      console.log("Kimenet:" + JSON.stringify(takeOutPrepJSON));
      $.ajax({
        url: "./utility/takeout_administrator.php",
        //url:"./utility/dummy.php",
        method: "POST",
        data: {
          takeoutData: takeOutPrepJSON,
          takeoutAsUser: $('#givetoAnotherPerson_UserName').val()
        },
        success: function (response) {
          if (response == '200') {
            displayMessageInTitle("#doTitle", "Sikeres kivétel! \nAz oldal hamarosan újratölt");
            $('#jstree').jstree(true).settings.core.data = d;
            //Fa újratöltése
            setTimeout(() => {
              $('#jstree').jstree().refresh();
            }, 2000);
            setTimeout(() => {
              window.location.href = window.location.href
            }, 1000);
          } else {
            //console.log(response);
            displayMessageInTitle("#doTitle", "Hiba történt.");
          }

        }
      });
    });

    //Main takeout gomb
    document.getElementById("takeout2BTN").addEventListener("click", function () {
      if (takeOutPrepJSON.items.length == 0) {
        displayMessageInTitle("#doTitle", "Nem választottál ki semmit!");
        return;
      }

      console.log("Kimenet:" + JSON.stringify(takeOutPrepJSON));
      $.ajax({
        url: "./utility/takeout_administrator.php",
        //url:"./utility/dummy.php",
        method: "POST",
        data: {
          takeoutData: takeOutPrepJSON,
          takeoutAsUser: $('#givetoAnotherPerson_UserName').val()
        },
        success: function (response) {
          if (response == '200') {
            displayMessageInTitle("#doTitle", "Sikeres kivétel! \nAz oldal hamarosan újratölt");
            $('#jstree').jstree(true).settings.core.data = d;
            //Fa újratöltése
            setTimeout(() => {
              $('#jstree').jstree().refresh();
            }, 2000);
            setTimeout(() => {
              window.location.href = window.location.href
            }, 1000);
          } else {
            //console.log(response);
            displayMessageInTitle("#doTitle", "Hiba történt.");
          }

        }
      });
    });

    $('#submit').click(function () {
      $.ajax({
        url: "name.php",
        method: "POST",
        data: $('#add_name').serialize(),
        success: function(data) {
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
    if (xmlhttp.status == 200) {
      result = xmlhttp.responseText;
    }
    return result.split("\n");

  }

  var dbItems = (loadFile("./utility/DB_Elements.txt"));
  // dbItem remover tool - Prevents an item to be added twice to the list
  function arrayRemove(arr, value) {

    return arr.filter(function(ele) {
      return ele != value;
    });

  }
</script>