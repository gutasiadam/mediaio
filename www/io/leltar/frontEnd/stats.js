var editModal;
var createModal;

$(document).ready(function () {

    getSearchQuery("Név");

    // Add event listener to buttons

    const buttons = document.getElementsByClassName("filterButton");

    Array.from(buttons).forEach((button) => {
        button.addEventListener("click", (event) => {
            getSearchQuery();
        });
    });

    $('#tableContainer').scroll(function () {
        if ($(this).scrollTop()) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });

    $("#toTop").click(function () {
        $("#tableContainer").animate({
            scrollTop: 0
        }, 700);
    });

    editModal = new bootstrap.Modal(document.getElementById('editItemModal'), {
        keyboard: false
      })
    createModal = new bootstrap.Modal(document.getElementById('newItemModal'), {
        keyboard: false
      })

});


function getSearchQuery(order = null) {
    const settings = document.getElementById("settings");
    const checkboxes = Array.from(settings.getElementsByTagName("input"));

    const medias = checkboxes.find((checkbox) => checkbox.id === "medias").checked;
    const studios = checkboxes.find((checkbox) => checkbox.id === "studios").checked;
    const eventes = checkboxes.find((checkbox) => checkbox.id === "event").checked;
    const isOut = checkboxes.find((checkbox) => checkbox.id === "isOut").checked;
    const nonRentable = checkboxes.find((checkbox) => checkbox.id === "nonRentable").checked;

    // Order criteria
    let orderCriteria;
    let orderDirection = "asc";
    if (!order) {
        const headers = Array.from(document.getElementById("itemTable").getElementsByTagName("th"));
        orderDirection = headers.find((header) => header.getAttribute("data-order")).getAttribute("data-order");
        orderCriteria = headers.find((header) => header.getAttribute("data-order")).getAttribute("data-header");
    } else {
        orderCriteria = order;
    }

    console.log(orderDirection);

    let orderBY;
    switch (orderCriteria) {
        case "UID":
            orderBY = "id";
            break;
        case "Név":
            orderBY = "name";
            break;
        case "Típus":
            orderBY = "type";
            break;
        case "Kivette":
            orderBY = "rentby";
            break;
        default:
            orderBY = "name";
            break;
    }


    let itemState = isOut ? "out" : "all";
    let takeRestrict;
    if (medias && studios && eventes) {
        takeRestrict = "mediaAndStudioAndEvent";
    } else if (medias && studios) {
        takeRestrict = "mediaAndStudio";
    } else if (medias && eventes) {
        takeRestrict = "mediaAndEvent";
    } else if (studios && eventes) {
        takeRestrict = "studioAndEvent";
    } else if (medias) {
        takeRestrict = "medias";
    } else if (studios) {
        takeRestrict = "studios";
    } else if (eventes) {
        takeRestrict = "eventes";
    } else {
        takeRestrict = "none";
    }
    if (nonRentable) {
        takeRestrict = "nonRentable";
        itemState = "all";
        checkboxes.forEach((checkbox) => {
            if (checkbox.id !== "nonRentable") {
                checkbox.disabled = true;
                checkbox.checked = false;
            }
        });
    } else {
        checkboxes.forEach((checkbox) => {
            checkbox.disabled = false;
        });
    }

    loadTableData(takeRestrict, itemState, orderBY, orderDirection);
}


async function loadTableData(takeRestrict = "none", itemState = "all", orderCriteria = "name", orderDirection = "asc") {
    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        type: "POST",
        data: {
            mode: "listByCriteria",
            takeRestrict: takeRestrict,
            itemState: itemState,
            orderCriteria: orderCriteria,
            orderDirection: orderDirection,
        },
    }));

    const users = JSON.parse(await $.ajax({
        url: "../Accounting.php",
        type: "POST",
        data: {
            mode: "getPublicUserInfo",
        },
    }));


    let orderBY;
    switch (orderCriteria) {
        case "id":
            orderBY = "UID";
            break;
        case "name":
            orderBY = "Név";
            break;
        case "type":
            orderBY = "Típus";
            break;
        case "rentby":
            orderBY = "Kivette";
            break;
        default:
            orderBY = "Név";
            break;
    }

    const table = document.createElement("table");
    table.id = "itemTable";
    table.className = "table table-striped table-bordered table-hover";

    //clear table
    try {
        $("#itemTable > tbody")[0].remove();
    }
    catch {
        console.log('table is empty');
    }


    const header = table.createTHead();
    const headerRow = header.insertRow(0);
    const headers = ["UID", "Név", "Típus", "Kivette"];
    headers.forEach((header, index) => {
        const th = document.createElement("th");
        th.innerHTML = header == orderBY ? `${header} <i class="fas fa-chevron-${orderDirection == "asc" ? "up" : "down"}"></i>` : header;
        header == orderBY ? th.setAttribute("data-order", orderDirection == "asc" ? "asc" : "desc") : null;
        th.style.cursor = "pointer";
        th.setAttribute("data-header", header);
        th.onclick = function () {
            setHeaderSortIcon(this);
        }
        headerRow.appendChild(th);
    });

    const body = table.createTBody();
    response.forEach((item) => {
        if (item.TakeRestrict == 'ü') {
            return;
        }
        const row = body.insertRow(-1);
        row.id = item.UID;


        item.TakeRestrict == '*' ? row.classList.add("table-danger") : null;
        item.TakeRestrict == 's' ? row.classList.add("table-primary") : null;
        item.TakeRestrict == 'e' ? row.classList.add("table-success") : null;
        
        item.RentBy != null ? row.classList.add("table-warning") : null;

        if (item.Status == 2) {
            row.classList.add("waitForConfirm");
        }

        //Adds right-click functionality for editing item values
        row.addEventListener('contextmenu', function(ev) {
            ev.preventDefault();
            showEditModal(item);
            return false;
        }, false);


                //MOBILE DOUBLE TAP
                let touchCount = 0;
        row.addEventListener('touchend', function (event) {
            touchCount++;
            if (touchCount === 1) {
                setTimeout(function () {
                    if (touchCount === 2) {
                        if ('vibrate' in navigator) {
                            // Vibration supported
                            navigator.vibrate(100);
                        }
                        showEditModal(item);
                    }
                    touchCount = 0;
                }, 300); // 300 milliseconds = 0.3 seconds
            }
        });

        const RentByUsername = users.find((user) => user.idUsers == item.RentBy)?.usernameUsers || '';
        const cellValues = [item.UID, item.Nev, item.Tipus, RentByUsername];
        cellValues.forEach((value, index) => {
            const cell = row.insertCell(index);
            cell.innerHTML = value;
            item.RentBy != null ? cell.style.fontStyle = "italic" : null;
        });
    });


    document.getElementById("tableContainer").innerHTML = "";
    document.getElementById("tableContainer").appendChild(table);

}

/* Displays the edit modal with a specific item:
   WARNING! This naming scheme might break in case a new form is added to the site!*/
function showEditModal(item) {

    //Title
    document.getElementById("editItemModalLabel").innerText="Szerkesztés -"+item.UID

    //ID
    document.getElementsByTagName("form")[1][0].value=item.ID

    //UID
    document.getElementsByTagName("form")[1][1].value=item.UID
    
    //Név
    document.getElementsByTagName("form")[1][2].value=item.Nev
    
    //Típus
    document.getElementsByTagName("form")[1][3].value=item.Tipus
    
    //Kategória
    document.getElementsByTagName("form")[1][4].value=item.Category

    //takeRestrict
    document.getElementsByTagName("form")[1][5].value=item.TakeRestrict

    //toggle Bootstrap Modal
    editModal.toggle();

}

//Creates a new item in the Database
function createItem() {
    var item = {}
    var createItemForm=document.getElementsByTagName("form")[2]

    item.UID=createItemForm[0].value
    item.Nev=createItemForm[1].value
    item.Tipus=createItemForm[2].value
    item.Category=createItemForm[3].value
    item.TakeRestrict=createItemForm[4].value;

    const response = ($.ajax({
        url: "../ItemManager.php",
        type: "POST",
        data: {
            mode: "createItem",
            item: JSON.stringify(item),
        },
        success: function(response){
            console.log(response);
            if(response==200){
                document.getElementById("newItemModalLabel").innerText="Sikeres létrehozás!";
                const myTimeout = setTimeout(() => getSearchQuery(), 400);
                createModal.toggle();

            }else{
                document.getElementById("newItemModalLabel").innerText="Sikertelen létrehozás!";
            }
        }
    }));
}

// Update item data on the server
function updateItemData() {

    //Collect updated values from the form
    var item= {};
    item.ID=document.getElementsByTagName("form")[1][0].value
    item.UID=document.getElementsByTagName("form")[1][1].value
    item.Nev=document.getElementsByTagName("form")[1][2].value
    item.Tipus=document.getElementsByTagName("form")[1][3].value
    item.Category=document.getElementsByTagName("form")[1][4].value
    item.TakeRestrict=document.getElementsByTagName("form")[1][5].value

    
    const response = ($.ajax({
        url: "../ItemManager.php",
        type: "POST",
        data: {
            mode: "updateItemAttributes",
            item: JSON.stringify(item),
        },
    }));
    if(response){
        document.getElementById("editItemModalLabel").innerText="Sikeres módosítás!";
        editModal.toggle();
        getSearchQuery();

    }
}

async function setHeaderSortIcon(header) {
    // Clear all other sort icons
    const headers = Array.from(document.getElementById("itemTable").getElementsByTagName("th"));

    const previusHeaderDirection = header.getAttribute("data-order");
    headers.forEach((header) => {
        header.removeAttribute("data-order");
    });

    header.setAttribute("data-order", previusHeaderDirection === "asc" ? "desc" : "asc");


    getSearchQuery();
}