

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

    // Get user information (name, username)

    const users = JSON.parse(await $.ajax({
        url: "../../Accounting.php",
        method: "POST",
        data: {
            mode: "getPublicUserInfo"
        }
    }));

    // Create the event cards
    events.forEach(event => {
        const user = users.find(user => user.idUsers == event.UserID);

        const card = document.createElement('div');
        card.classList.add('card');
        card.classList.add('mb-3');

        const cardBody = document.createElement('div');
        cardBody.classList.add('card-body', 'confirm-card');

        const cardNameDiv = document.createElement('div');
        cardNameDiv.classList.add('card-name-div');
        cardBody.appendChild(cardNameDiv);

        const cardTitle = document.createElement('h5');
        cardTitle.classList.add('card-title');
        cardTitle.textContent = `${user.lastName} ${user.firstName}`
        cardNameDiv.appendChild(cardTitle);

        const cardUsername = document.createElement('p');
        cardUsername.classList.add('card-username');
        cardUsername.textContent = user.usernameUsers;
        cardNameDiv.appendChild(cardUsername);

        // Second part of the card, the taken items list

        const takenItems = document.createElement('div');
        takenItems.classList.add('taken-items');
        cardBody.appendChild(takenItems);

        const items = JSON.parse(event.Items);
        console.log(items);

        items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('taken-item');
            itemDiv.textContent = `${item.name} - ${item.uid}`;
            takenItems.appendChild(itemDiv);
        });

        // Third part of the card, the confirm button and the timestamp

        const cardButtonHolder = document.createElement('div');
        cardButtonHolder.classList.add('card-button-holder');
        cardBody.appendChild(cardButtonHolder);

        const cardTimestamp = document.createElement('p');
        cardTimestamp.classList.add('card-timestamp');
        cardTimestamp.textContent = event.Date;
        cardButtonHolder.appendChild(cardTimestamp);

        const confirmButton = document.createElement('button');
        confirmButton.classList.add('btn');
        confirmButton.classList.add('btn-primary');
        confirmButton.textContent = 'Jóváhagyás';
        confirmButton.onclick = function () {
            // Open settings modal
            openSettingsModal(event);
        }
        cardButtonHolder.appendChild(confirmButton);

        card.appendChild(cardBody);
        Container.appendChild(card);

    });

}



async function openSettingsModal(event) {
    // Set the event data
    //document.getElementById('eventDate').textContent = event.Date;

    // Set the event items
    const items = JSON.parse(event.Items);
    const itemList = document.getElementById('itemsList');
    itemList.innerHTML = '';
    items.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('item');
        itemDiv.textContent = item.name;
        itemList.appendChild(itemDiv);
    });

    // Set the event ID
    //document.getElementById('eventID').value = event.ID;

    // Open the modal
    $('#SettingsModal').modal('show');

}