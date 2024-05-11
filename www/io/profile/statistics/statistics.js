


$(document).ready(function () {

    loadPage();

});



async function loadPage() {
    const response = JSON.parse(await $.ajax({
        url: "../../ItemManager.php",
        type: "POST",
        data: {
            mode: "getInventoryHistory",
        },
    }));

    const users = JSON.parse(await $.ajax({
        url: "../../Accounting.php",
        type: "POST",
        data: {
            mode: "getPublicUserInfo",
        },
    }));

    //console.log(response);

    const Container = document.getElementById("eventsContainer");

    if (response.length == 0) {
        const noItems = document.createElement("div");
        noItems.classList.add("alert", "alert-info", "mt-3", "text-center");
        noItems.style.width = "400px";
        noItems.innerHTML = "Nem történt esemény az elmúlt héten!";
        Container.appendChild(noItems);

        Container.style.display = "flex";
        Container.style.justifyContent = "center";
        Container.style.alignItems = "center";
        return;
    }

    // Create the event cards
    response.forEach(event => {
        const user = users.find(user => user.idUsers == event.UserID);

        const card = document.createElement('div');
        card.classList.add('card', 'mb-3');
        card.classList.add(event.Event === 'OUT' ? 'border-danger' : event.Event === 'DECLINE' ? 'border-warning' : 'border-success');
        card.classList.add(event.Event === 'OUT' ? 'card-out' : event.Event === 'DECLINE' ? 'card-decline' : 'card-in');

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

        for (let i = 0; i < 5; i++) {
            if (i >= items.length) {
                break;
            }
            const item = items[i];
            const itemDiv = document.createElement('li');
            itemDiv.classList.add('taken-item');
            itemDiv.textContent = `${item.name} - ${item.uid}`;
            takenItems.appendChild(itemDiv);
        }
        if (items.length > 5) {
            const itemDiv = document.createElement('div');
            itemDiv.style.fontStyle = 'italic';
            itemDiv.textContent = `Még ${items.length - 5} eszköz...`;
            takenItems.appendChild(itemDiv);
        }


        // Third part of the card, the confirm button and the timestamp

        const cardButtonHolder = document.createElement('div');
        cardButtonHolder.classList.add('card-button-holder');
        cardBody.appendChild(cardButtonHolder);

        const cardTimestamp = document.createElement('p');
        cardTimestamp.classList.add('card-timestamp');
        let date = new Date(event.Date);
        let formattedDate = `${date.getFullYear()}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getDate().toString().padStart(2, '0')} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
        cardTimestamp.textContent = formattedDate;
        cardButtonHolder.appendChild(cardTimestamp);

        const inOrOut = document.createElement('p');
        inOrOut.classList.add('card-timestamp');
        inOrOut.style.fontSize = '1.2em';
        inOrOut.style.fontWeight = 'bold';
        inOrOut.textContent = event.Event === 'OUT' ? 'Kiadva' : event.Event === 'DECLINE' ? 'Elutasítva' : 'Visszahozva';
        if (event.Acknowledged == 0) {
            inOrOut.style.fontSize = '1em';
            inOrOut.textContent += ' (Megerősítésre vár)';
        }
        cardButtonHolder.appendChild(inOrOut);

        const moreInfo = document.createElement('button');
        moreInfo.classList.add('btn', 'btn-secondary');
        moreInfo.innerHTML = `<i class="fas fa-info"></i>`;
        moreInfo.onclick = function () {
            // Open info modal
            openInfoModal(event);
        }
        cardButtonHolder.appendChild(moreInfo);

        card.appendChild(cardBody);
        Container.appendChild(card);
    });

}


async function openInfoModal(event) {
    //Get event type and set modal title
    document.getElementById('SettingsModalLabel').textContent = event.Event === 'OUT' ? 'Kiadás' : event.Event === 'DECLINE' ? 'Elutasítás' : 'Visszahozás';

    // Set the event items
    const items = JSON.parse(event.Items);
    const itemList = document.getElementById('itemsList');
    itemList.innerHTML = '';
    items.forEach(item => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `${item.name} - ${item.uid}`;
        itemList.appendChild(listItem);
    });

    // Open the modal
    $('#SettingsModal').modal('show');
}