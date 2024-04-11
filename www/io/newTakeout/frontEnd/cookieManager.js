

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

function updateSelectionCookie() {
    console.log("[updateSelectionCookie] - called");
    //Set cookie expire date to 1 day
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    //get IDs of selected items
    var selectedItems = $('#jstree').jstree().get_selected(); //----------------------------------- TODO
    console.log(selectedItems);
    document.cookie = "selectedItems=" + selectedItems + ";" + expires + ";path=/";
}


function reloadSavedSelections() {
    //Try re-selectiong items that are saved in the takeOutItems cookie.

    var selecteditems = getCookie("selectedItems")
    if (!selecteditems) {
        return;
    }
    selecteditems = selecteditems.split(",");
    if (selecteditems[0] === "") {
        badge.textContent = 0;
        console.log("No items to reload");
    }
    selecteditems.forEach(element => {
        console.log("Reloading item: " + element);
        //$('#jstree').jstree().select_node(element); -------------------------------- TODO: Fix this
    });
}