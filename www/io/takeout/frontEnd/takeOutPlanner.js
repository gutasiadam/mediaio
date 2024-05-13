

// Calendar
async function loadTakeOutPlanner() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap5',

        // HEADER
        headerToolbar: {
            left: 'title',
            center: '',
            right: 'timeGridWeek dayGridMonth today prev,next'
        },
        buttonText: {
            today: 'Ma',
            month: 'Hónap',
            week: 'Hét',
            list: 'Lista'
        },
        views: {
            dayGridMonth: {
                titleFormat: { year: 'numeric', month: 'long' },
                dayHeaderFormat: { weekday: 'short' },
            },
            timeGridWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
                dayHeaderFormat: { weekday: 'short', day: 'numeric', omitCommas: true },
            },
        },

        // LOCALE
        locale: 'hu',
        firstDay: 1,


        // Style settings
        height: "80dvh",

        nowIndicator: true,

        // Event settings
        events: await getTakeOutEvents(),

        eventClick: function (info) { openEventModal(info); },

        // other options...
        eventDidMount: function (info) {
            var tooltip = new bootstrap.Tooltip(info.el, {
                title: info.event.extendedProps.Description,
                placement: 'top',
                trigger: 'hover'
            });
        },

        // Window resize
        windowResize: function (arg) {
            calendar.updateSize();
        },
    });
    calendar.render();
}



// Get events
async function getTakeOutEvents() {
    const response = JSON.parse(await $.ajax({
        url: "../../ItemManager.php",
        method: "POST",
        data: {
            mode: "getPlannedTakeouts"
        }
    }));

    const users = JSON.parse(await $.ajax({
        url: "../../Accounting.php",
        type: "POST",
        data: {
            mode: "getPublicUserInfo",
        },
    }));


    let events = [];
    const resEvents = response.events;

    console.log(resEvents);

    if (response.events.length == 0) {
        return [];
    }

    resEvents.forEach(element => {

        let isAllDay = false;
        if (element.StartTime.substring(11, 16) == "00:00" && element.ReturnTime.substring(11, 16) == "00:00") {
            isAllDay = true;
        }

        let color = element.eventState == 1 ? "#d3772c" : element.eventState == 2 ? "#28a745" : "#3788d8";
        element.eventState == -1 ? color = "#636363" : null;

        let user = users.find((user) => user.idUsers == element.UserID);

        events.push({
            id: element.ID,
            title: element.Name == "" ? `${user.lastName} ${user.firstName}` : element.Name,
            start: element.StartTime,
            end: element.ReturnTime,
            allDay: isAllDay,
            color: color,
            extendedProps: {
                Description: element.Description,
                itemsList: element.Items,
                isAdmin: response.isAdmin,
                ownerId: element.UserID,
                currentUser: response.currentUser,
                eventState: element.eventState
            },
        });
    });

    return events;

}


// Open event modal

async function openEventModal(info) {

    const headerTitle = document.getElementById('plannedEventsModalLabel');

    headerTitle.innerHTML = info.event.title;
    $('#plannedEventsModal').modal('show');

    const eventDescription = document.getElementById('plannedEventsDescription');
    eventDescription.innerHTML = info.event.extendedProps.Description;

    const eventItems = document.getElementById('plannedEventsItems');
    eventItems.innerHTML = "";

    const items = JSON.parse(info.event.extendedProps.itemsList);
    items.forEach(element => {
        let item = document.createElement('li');
        item.innerHTML = `${element.name} - ${element.uid} `;
        eventItems.appendChild(item);
    });


    const footer = document.getElementById('plannedEventsFooter');
    footer.innerHTML = "";

    let isOwner = info.event.extendedProps.ownerId == info.event.extendedProps.currentUser;
    let canStart = info.event.start > new Date().toISOString();
    // Add start button
    if (isOwner && canStart && info.event.extendedProps.eventState == 0) {
        const startButton = document.createElement('button');
        startButton.classList.add('btn', 'btn-primary');
        startButton.innerHTML = "Elvitel indítása";
        startButton.onclick = async function () {
            const response = JSON.parse(await $.ajax({
                url: "../../ItemManager.php",
                method: "POST",
                data: {
                    mode: "startPlannedTakeout",
                    eventID: info.event.id
                }
            }));

            if (response == 200) {
                successToast("Sikeres elindítás");
                $('#plannedEventsModal').modal('hide');
                document.getElementById('prepared-tab').click();
                loadItems();
            }
            else {
                serverErrorToast();
            }
        }
        footer.append(startButton);
    }

    let canDelete = info.event.extendedProps.eventState == 0 || info.event.extendedProps.eventState == -1;
    // Add delete button
    if ((info.event.extendedProps.isAdmin || isOwner) && canDelete) {
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn', 'btn-danger');
        deleteButton.innerHTML = "Törlés";
        deleteButton.onclick = async function () {
            await deleteEvent(info.event.id);
        }
        footer.append(deleteButton);
    }


    // Add ok button
    const okButton = document.createElement('button');
    okButton.classList.add('btn', 'btn-success');
    okButton.innerHTML = "Ok";
    okButton.onclick = function () {
        $('#plannedEventsModal').modal('hide');
    }
    footer.append(okButton);


    const spinner = document.getElementById('plannedEventsLoading');
    spinner.style.display = "none";
}


async function deleteEvent(eventId) {

    $('#plannedEventsModal').modal('hide');
    $('#areyousureModal').modal('show');

    // Create a new Promise that resolves when the button is clicked
    let buttonClicked = new Promise((resolve, reject) => {
        document.getElementById('sureButton').addEventListener('click', resolve);
        document.getElementById('cancelButton').addEventListener('click', reject);
    });

    return buttonClicked.then(async () => {
        const response = JSON.parse(await $.ajax({
            url: "../../ItemManager.php",
            method: "POST",
            data: {
                mode: "deletePlannedTakeout",
                ID: eventId
            }
        }));

        if (response == 200) {
            successToast("Sikeres törlés");
            $('#areyousureModal').modal('hide');
            document.getElementById('prepared-tab').click();
            loadItems();
        }
        else {
            serverErrorToast();
        }
    }).catch((error) => {
        // Do nothing
        $('#plannedEventsModal').modal('show');
        console.error(error);
        console.log("Delete cancelled");
        return;
    });

}