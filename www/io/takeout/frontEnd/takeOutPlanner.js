//Stores every reservation in the calendar
var reservations = [];


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
            right: 'dayGridMonth listWeek today prev,next' //'timeGridWeek'
        },
        buttonText: {
            today: 'Ma',
            month: 'Hónap',
            //week: 'Hét', 
            list: 'Lista' // TODO: Implement list view
        },
        views: {
            dayGridMonth: {
                titleFormat: { year: 'numeric', month: 'long' },
                dayHeaderFormat: { weekday: 'short' },
            },
            /* timeGridWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
                dayHeaderFormat: { weekday: 'short', day: 'numeric', omitCommas: true },
            }, */
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
            if (screen.width < 768) {
                return;
            }
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

    //console.log(resEvents);

    if (response.events.length == 0) {
        return [];
    }

    resEvents.forEach(element => {

        let isAllDay = true;
        //var startTime = new Date(element.StartTime);
        //var returnTime = new Date(element.ReturnTime);
        //var durationInHours = (returnTime - startTime) / (1000 * 60 * 60);

        //if (durationInHours >= 24) {
        //    isAllDay = true;
        //}

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
                originalTimes: { "start": element.StartTime, "end": element.ReturnTime },
                Description: element.Description,
                itemsList: element.Items,
                isAdmin: response.isAdmin,
                owner: user,
                currentUser: response.currentUser,
                eventState: element.eventState
            },
        });
    });

    return events;

}

// Open event modal

async function openEventModal(info) {
    console.log(info.event);
    $('#plannedEventsModal').modal('show');


    const eventDescription = document.getElementById('plannedEventsDescription');
    eventDescription.innerHTML = info.event.extendedProps.Description == "" ? "<i>Nincs leírás</i>" : info.event.extendedProps.Description;

    // Time range
    createTimeRange(info.event.id, info.event.extendedProps.originalTimes.start, info.event.extendedProps.originalTimes.end, info.event.extendedProps.eventState);

    //____________________________________________________

    // Create items list and edit button

    createItemsList(info);

    // Owner label

    const ownerLabel = document.getElementById('plannedEventOwner');
    ownerLabel.innerHTML = `Tulajdonos: ${info.event.extendedProps.owner.lastName} ${info.event.extendedProps.owner.firstName}`;


    const footer = document.getElementById('plannedEventsFooter');
    footer.innerHTML = "";

    let isOwner = info.event.extendedProps.owner.idUsers == info.event.extendedProps.currentUser;
    let canStart = info.event.start.getTime() < new Date().getTime();
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

function createTimeRange(eventID, start, end, eventState) {
    const timeRangeEdit = document.getElementById('timeRangeEdit'); // Container
    timeRangeEdit.innerHTML = "";

    const headerDiv = document.createElement('div');
    headerDiv.className = 'd-flex justify-content-between';

    const label = document.createElement('label');
    label.className = 'form-label';
    label.textContent = 'Idősáv:';
    headerDiv.appendChild(label);

    timeRangeEdit.appendChild(headerDiv);


    const mainDiv = document.createElement('div');
    mainDiv.className = 'input-group mb-2';
    mainDiv.id = 'timeRangeMain';
    timeRangeEdit.appendChild(mainDiv);

    let startDate = new Date(start);
    let endDate = new Date(end);

    let formattedStart = `${(startDate.getMonth() + 1).toString().padStart(2, '0')}.${startDate.getDate().toString().padStart(2, '0')}`;
    let formattedEnd = `${(endDate.getMonth() + 1).toString().padStart(2, '0')}.${endDate.getDate().toString().padStart(2, '0')}`;

    // Create a p element
    const timeRange = document.createElement('p');
    timeRange.textContent = `${formattedStart} - ${formattedEnd}`;
    mainDiv.appendChild(timeRange);


    // Edit button
    if (eventState == 0) {
        const button = document.createElement('button');
        button.className = 'btn btn-sm';
        button.onclick = function () {
            openTimeRangeEditor(eventID, startDate, endDate);
        };

        const icon = document.createElement('i');
        icon.className = 'fas fa-pen';
        button.appendChild(icon);
        headerDiv.appendChild(button);
    }
    //____________________________________________________

}

function createItemsList(info) {

    const headerDiv = document.getElementById('itemsEditHeader');
    headerDiv.innerHTML = "";

    // Label
    const label = document.createElement('label');
    label.innerHTML = "<b>Tárgyak:</b>";
    headerDiv.appendChild(label);

    // Edit button
    const editButton = document.createElement('button');
    editButton.className = 'btn btn-sm';
    editButton.id = 'editItems';
    editButton.onclick = function () {
        location.href = "./?reservationProject=" + info.event.id;
    };
    
    const icon = document.createElement('i');
    icon.className = 'fas fa-pen';
    editButton.appendChild(icon);

    // Only if the event is not started
    info.event.extendedProps.eventState == 0 ? headerDiv.appendChild(editButton) : null;


    // Items list
    const eventItems = document.getElementById('plannedEventsItems');
    eventItems.innerHTML = "";

    const items = JSON.parse(info.event.extendedProps.itemsList);
    items.forEach(element => {
        let item = document.createElement('li');
        item.innerHTML = `${element.name} - ${element.uid} `;
        eventItems.appendChild(item);
    });
    
}

let timeRangeCalendar = null;

function openTimeRangeEditor(eventID, startDate, endDate) {
    const container = document.getElementById('timeRangeMain');
    container.innerHTML = "";

    const timeRangeInput = document.createElement('input');
    timeRangeInput.id = 'timeRangeInput';
    timeRangeInput.className = 'form-control';
    timeRangeInput.type = 'text';
    container.appendChild(timeRangeInput);

    timeRangeCalendar = loadPicker("#timeRangeInput", startDate, endDate);

    const saveButton = document.createElement('button');
    saveButton.className = 'btn btn-success';
    saveButton.innerHTML = `<i class="fas fa-save"></i>`;
    saveButton.onclick = function () {
        editTimeRange(eventID);
    };
    container.appendChild(saveButton);
}

async function editTimeRange(eventID) {
    let startTime = timeRangeCalendar.getStartDate();
    let endTime = timeRangeCalendar.getEndDate();


    // Format the dates in 'YYYY-MM-DD HH:MM:SS' format
    function formatDateTime(date) {
        let d = new Date(date);
        let timezoneOffset = d.getTimezoneOffset() * 60000; // Get timezone offset in milliseconds
        let localDate = new Date(d.getTime() - timezoneOffset); // Adjust the date to local timezone
        return localDate.toISOString().slice(0, 19).replace('T', ' ');
    }

    const response = JSON.parse(await $.ajax({
        url: "../../ItemManager.php",
        method: "POST",
        data: {
            mode: "changeTakeoutTime",
            eventID: eventID,
            startTime: formatDateTime(startTime),
            endTime: formatDateTime(endTime),
        }
    }));

    if (response == 200) {
        successToast("Sikeres módosítás");
        createTimeRange(eventID, startTime, endTime, 0);
        document.getElementById('prepared-tab').click(); // Refresh the calendar
    } else if (response == 409) {
        errorToast("Az időpontok ütköznek");
    }
    else {
        serverErrorToast();
    }

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