

//Preventing double click zoom
document.addEventListener('dblclick', function (event) {
    event.preventDefault();
}, { passive: false });


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
        if (<? php echo in_array('system', $_SESSION['groups']) ? 'true' : 'false' ?>) { //stúdiós restrict
            d[i].state.disabled = false;
        } else {
            if (d[i].TakeRestrict == 's' && <? php echo(in_array('studio', $_SESSION['groups']) || in_array('admin', $_SESSION['groups'])) ? 'false' : 'true' ?>) { //stúdiós restrict
                d[i].state.disabled = true;
            }
            if (d[i].TakeRestrict == '*') {
                d[i].state.disabled = true;
            }
            if (d[i].TakeRestrict == 'e' && <? php echo(in_array('event', $_SESSION['groups']) || in_array('admin', $_SESSION['groups'])) ? 'false' : 'true' ?>) { // event eszköz restrict
                d[i].state.disabled = true;
            }
        }
    }

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



//Add Preset Items to the selection
//ID: takeout preset ID
function addItems(id) {
    if (presetStates[id] == false) {
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
        presetStates[id] = true;
        button = '#presetButton' + id;
        $(button).removeClass('mediaBlue');
        $(button).addClass('btn-outline-success');
    } else {
        console.log("Deselecting preset " + id);
        selectionArray = [];
        takenArray = [];
        addArray = JSON.parse(takeoutPresets[id].Items).items;
        addArray.forEach(element => {
            for (j = 1; j <= d.length; j++) {
                if ($('#jstree').jstree().get_node(j).original.uid == element & $('#jstree').jstree().get_node(j).state.disabled == false) {
                    selectionArray.push(j);
                    break;
                } else if ($('#jstree').jstree().get_node(j).original.uid == element & $('#jstree').jstree().get_node(j).state.disabled == true) {
                    takenArray.push($('#jstree').jstree().get_node(j));
                    break;
                }
            }
            $('#jstree').jstree().deselect_node(selectionArray);

        })
        console.log(takenArray);
        $('#presetButton' + id + ' span')[0].innerHTML = '';
        presetStates[id] = false;
        button = '#presetButton' + id;
        $(button).removeClass('btn-outline-success');
        $(button).addClass('mediaBlue');
    }

};






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

$(document).ready(function () {


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
                    deselect_all();
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
                console.log(response);
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
                    deselect_all();
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
            success: function (data) {
                //alert(data);
                $('#add_name')[0].reset();
            }
        });
    });
});
