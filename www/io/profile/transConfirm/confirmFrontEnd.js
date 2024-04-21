

$(document).ready(function () {

    loadPage();

});

async function loadPage() {

    const Container = document.getElementById('confirmEvents');

    // Get the events from database

    const response = await $.ajax({
        url: "../../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItemsForConfirmation"
        }
    });

    const events = JSON.parse(response);

    console.log(events);

    console.log(response);

}