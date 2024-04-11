


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