
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

    //console.log(response);

    const table = document.createElement("table");
    table.id = "itemTable";
    table.className = "table table-striped table-bordered table-hover";

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