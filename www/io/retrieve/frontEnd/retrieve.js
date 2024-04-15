function AnnounceDamage() {
    window.location.href = "../utility/damage_report/announce_Damage.php";
}


$(document).ready(function () {

    loadUserItems();

    // Add event listener to manual retrieve checkbox

    const manualRetrieve = document.getElementById("manual_Retrieve");

    manualRetrieve.addEventListener("change", function () {
        const items = document.getElementsByClassName("card");
        if (this.checked) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.add("selectable");
            }
        } else {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove("selectable");
            }
        }
    });


});



async function loadUserItems() {

    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "listUserItems"
        }
    }));

    const itemHolder = document.getElementById("itemsHolder");
    itemHolder.innerHTML = "";

    if (response.length == 0) {
        const noItems = document.createElement("div");
        noItems.classList.add("alert", "alert-info", "mt-3", "text-center");
        noItems.style.width = "400px";
        noItems.innerHTML = "Nincsen nálad egy tárgy sem!";
        itemHolder.appendChild(noItems);

        itemHolder.style.gridTemplateColumns = "none";
        itemHolder.style.justifyContent = "center";
        itemHolder.style.alignItems = "center";

        document.getElementById("submission").style.display = "none";
        return;
    }

    response.forEach(element => {
        createItemCard(element);
    });

    reloadSavedSelections();

}


function createItemCard(item) {

    const itemHolder = document.getElementById("itemsHolder");

    const itemCard = document.createElement("div");
    itemCard.classList.add("card", "itemCard");
    itemCard.id = item.UID;
    itemCard.setAttribute("data-name", item.Nev);
    itemCard.onclick = function () {
        if (this.classList.contains("selectable")) {
            toggleSelectItem(item);
        } else {
            warningToast("Engedélyezd a tárgyak kiválasztását!");
            // Shake the manual retrieve button
            document.getElementById("manualHolder").classList.add("animate_shakeY");
            setTimeout(() => {
                document.getElementById("manualHolder").classList.remove("animate_shakeY");
            }, 1000);
        }
    }


    const cardBody = document.createElement("div");
    cardBody.classList.add("card-body");

    const cardTitle = document.createElement("h5");
    cardTitle.classList.add("card-title");
    cardTitle.innerHTML = item.Nev;

    const cardText = document.createElement("p");
    cardText.classList.add("card-text");
    cardText.innerHTML = item.UID;



    cardBody.appendChild(cardTitle);
    cardBody.appendChild(cardText);
    itemCard.appendChild(cardBody);

    itemHolder.appendChild(itemCard);

}


function toggleSelectItem(item) {
    const itemElement = document.getElementById(item.UID);

    itemElement.classList.toggle("text-bg-success");
    itemElement.classList.toggle("selected");
    updateSelectionCookie();
}


async function submitRetrieve() {
    if (!document.getElementById("intactItems").checked) {
        $('#confirmModal').modal('hide');
        warningToast("Ha sérülést észleltél, kérlek jelezd a vezetőség felé!");
        const damageButton = document.getElementById("AnnounceDamage");
        damageButton.classList.add("animate_shakeY");
        setTimeout(() => {
            damageButton.classList.remove("animate_shakeY");
        }, 1000);
        return;
    }

    const selectedItems = document.getElementsByClassName("selected");
    if (selectedItems.length == 0) {
        warningToast("Nincs kiválasztva egy tárgy sem!");
        return;
    }

    const itemsToRetrieve = Array.from(selectedItems).map(item => ({
        uid: item.id,
        name: item.getAttribute("data-name"),
    }));

    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "retrieveStaging",
            data: JSON.stringify(itemsToRetrieve),
        }
    });

    if (response == 200) {
        successToast("Sikeres visszahozás!");
        $('#confirmModal').modal('hide');
        document.getElementById("intactItems").checked = false;
        document.getElementById("manual_Retrieve").checked = false;
        loadUserItems();
        clearSelectionCookie();
    } else {
        serverErrorToast();
    }
}