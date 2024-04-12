function AnnounceDamage() {
    window.location.href = "../utility/damage_report/announce_Damage.php";
}


$(document).ready(function () {

    //reloadSavedSelections();

    loadUserItems();

});



async function loadUserItems() {

    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItems"
        }
    }));

}

