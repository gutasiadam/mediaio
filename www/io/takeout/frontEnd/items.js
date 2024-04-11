
$(document).ready(function () {
    // Search

    const searchInput = document.getElementById("search");

    searchInput.addEventListener("input", function () {
        const items = Array.from(document.getElementsByClassName("leltarItem"));
        const inputValue = searchInput.value.toLowerCase();
        const showAvailable = document.getElementById("show_unavailable").checked;
    
        items.forEach(item => {
            const itemLabelElement = item.querySelector(".leltarItemLabel");
    
            // Restore the original item label
            const originalItemLabel = `${itemLabelElement.parentElement.getAttribute("data-name")} - ${item.id}`;
            const itemName = originalItemLabel.toLowerCase();
    
            const shouldDisplay = itemName.includes(inputValue) && (item.getAttribute("data-status") == 1 || !showAvailable);
    
            item.style.display = shouldDisplay ? "flex" : "none";
    
            if (shouldDisplay && inputValue) {
                // Highlight matching characters
                const regex = new RegExp(`(${inputValue})`, 'gi');
                const highlightedLabel = originalItemLabel.replace(regex, '<span class="highlight">$1</span>');
    
                if (itemLabelElement.innerHTML !== highlightedLabel) {
                    itemLabelElement.innerHTML = highlightedLabel;
                }
            } else if (itemLabelElement.innerHTML !== originalItemLabel) {
                itemLabelElement.innerHTML = originalItemLabel;
            }
        });
    });


    // Add eventlistener to unavailable items
    const show_unavailable = document.getElementById("show_unavailable");

    show_unavailable.addEventListener("change", function () {
        const items = Array.from(document.getElementsByClassName("leltarItem"));
        const showAvailable = show_unavailable.checked;

        items.forEach(item => {
            const itemStatus = item.getAttribute("data-status");

            if (showAvailable) {
                item.style.display = itemStatus == 1 ? "flex" : "none";
            } else {
                item.style.display = "flex";
            }
        });

        searchInput.dispatchEvent(new Event("input"));
    });
});



async function loadItems() {
    const itemsList = document.getElementById("itemsList");
    itemsList.innerHTML = "";

    //Get items from server
    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItems"
        }
    }));

    //console.log(response);

    response.forEach(item => {
        if (item.TakeRestrict == 'ü') {
            return;
        }
        const itemElement = document.createElement("div");
        itemElement.classList.add("form-check", "mb-1", "leltarItem");
        itemElement.setAttribute("data-takeRestrict", item.TakeRestrict);
        itemElement.setAttribute("data-status", item.Status);
        itemElement.setAttribute("data-main-id", item.ID);
        itemElement.setAttribute("data-name", item.Nev);
        itemElement.id = `${item.UID}`;
        if (item.Status == 1) {
            itemElement.onclick = () => {
                toggleSelectItem(item);
            };
        }

        const checkBox = document.createElement("input");
        checkBox.type = "checkbox";
        if (item.Status == 0) {
            checkBox.disabled = true;
        }
        checkBox.classList.add("form-check-input", "leltarItemCheckbox");

        itemElement.appendChild(checkBox);


        const itemLabel = document.createElement("label");
        itemLabel.classList.add("form-check-label", "leltarItemLabel");
        if (item.Status == 1) {
            itemLabel.innerHTML = `${item.Nev} - ${item.UID}`;
        } else {
            itemLabel.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="Kivette: ${item.RentBy}">${item.Nev} - ${item.UID}</a>`;
        }
        itemElement.appendChild(itemLabel);

        itemsList.appendChild(itemElement);
    });

    reloadSavedSelections();
}


function toggleSelectItem(item) {
    const itemElement = document.getElementById(item.UID);
    const checkBox = itemElement.querySelector(".leltarItemCheckbox");

    if (itemElement.classList.contains("selected")) {
        checkBox.checked = false;
        let selectedCards = document.querySelectorAll(`#selected-${item.UID}`);
        selectedCards.forEach(card => {
            card.remove();
        });
        parseInt(badge.textContent = parseInt(badge.textContent) - 1);
    } else {
        checkBox.checked = true;
        addItemCard(item);
        parseInt(badge.textContent = parseInt(badge.textContent) + 1);
    }

    itemElement.classList.toggle("selected");
    updateSelectionCookie();
}



function addItemCard(item) {
    const selectedItems = document.getElementsByClassName("selectedList");

    const card = document.createElement("div");
    card.classList.add("card", "mb-2", "selected-card");
    card.id = `selected-${item.UID}`;

    const cardBody = document.createElement("div");
    cardBody.classList.add("card-body", "d-flex", "justify-content-between");

    const infoDiv = document.createElement("div");

    const cardTitle = document.createElement("h5");
    cardTitle.classList.add("card-title");
    cardTitle.innerHTML = item.Nev;

    const cardText = document.createElement("p");
    cardText.classList.add("card-text");
    cardText.innerHTML = item.UID;

    infoDiv.appendChild(cardTitle);
    infoDiv.appendChild(cardText);

    function attachRemoveButtonListener(button, item) {
        button.onclick = () => {
            toggleSelectItem(item);
        }
    }

    const removeButton = document.createElement("button");
    removeButton.classList.add("btn", "btn-danger");
    removeButton.innerHTML = `<i class="fas fa-trash-alt"></i>`;
    attachRemoveButtonListener(removeButton, item);

    cardBody.appendChild(infoDiv);
    cardBody.appendChild(removeButton);
    card.appendChild(cardBody);

    Array.from(selectedItems).forEach(selectedList => {
        const clonedCard = card.cloneNode(true);
        const clonedRemoveButton = clonedCard.querySelector(".btn-danger");
        attachRemoveButtonListener(clonedRemoveButton, item);
        selectedList.appendChild(clonedCard);
    });
}


function deselect_all() {
    console.log("Deselecting all items");

    document.querySelectorAll('.selected').forEach(item => {
        item.classList.remove("selected");
        item.querySelector(".leltarItemCheckbox").checked = false;
    });

    document.querySelectorAll('.selected-card').forEach(item => {
        item.remove();
    });

    //decideGiveToAnotherPerson_visibility();
    //parseInt(badge.textContent = 0);
    updateSelectionCookie();
}




async function showPresetsModal() {
    $('#presets_Modal').modal('show');

    //get Preset Items
    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getPresets"
        }
    });

    //Convert rerponse to JSON
    var presets = JSON.parse(response);
    //For each user add a select option to givetoAnotherPerson_UserName
    if (presets.length > 0) {
        $('#presetsLoading').hide();
    }
    $('#presetsContainer').html('');

    presets.forEach((preset, i) => {
        const button = document.createElement('button');
        button.className = 'btn mediaBlue position-relative';
        button.id = `presetButton${i}`;
        button.onclick = function () { addItems(preset.Items); };
        button.innerHTML = `${preset.Name}<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>`;

        document.getElementById('presetsContainer').appendChild(button);
    });
}


function addItems(items) {
    console.log("Adding preset items");

    items = JSON.parse(items);
    items = items.items;

    items.forEach(item => {
        const itemElement = document.getElementById(item);
        itemElement.click();
    });
    //$('#presets_Modal').modal('hide');
}


