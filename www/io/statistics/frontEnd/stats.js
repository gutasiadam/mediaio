
$(document).ready(function () {

    loadTableData();

    // Add event listener to buttons

    const buttons = document.getElementsByClassName("filterButton");

});



async function loadTableData(itemState = "all", orderCriteria = "name") {
    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        type: "POST",
        data: {
            mode: "listByCriteria",
            itemState: itemState,
            orderCriteria: orderCriteria,
        },
    }));

    console.log(response);

    const table = document.createElement("table");
    table.id = "itemTable";
    table.className = "table table-striped table-bordered table-hover";

    const header = table.createTHead();
    const headerRow = header.insertRow(0);
    const headers = ["UID", "Név", "Típus", "Kivette"];
    headers.forEach((header, index) => {
        const th = document.createElement("th");
        th.innerHTML = header;
        headerRow.appendChild(th);
    });

    const body = table.createTBody();
    response.forEach((item, index) => {
        const row = body.insertRow(index);
        row.id = item.UID;
        const cellValues = [item.UID, item.Nev, item.Tipus, item.RentBy];
        cellValues.forEach((value, index) => {
            const cell = row.insertCell(index);
            cell.innerHTML = value;
        });
    });


    document.getElementById("tableContainer").innerHTML = "";
    document.getElementById("tableContainer").appendChild(table);

}
