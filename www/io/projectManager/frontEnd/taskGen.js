


async function generateTasks(projectID, canEdit) {
    let taskHolder = document.createElement("div");
    taskHolder.classList.add("taskHolder");
    taskHolder.id = projectID + "-taskHolder";

    // Fetch the tasks
    let tasks = await fetchTask(projectID);
    // Parse the tasks
    tasks = JSON.parse(tasks);

    // Append each task to taskHolder
    for (let i = 0; i < tasks.length; i++) {
        taskHolder.appendChild(await createTask(tasks[i], projectID, canEdit));
    }

    return taskHolder;
}

async function createTask(task, projectID, canEdit) {
    var uData = await userTaskData(task.ID, projectID);

    let taskCard = document.createElement("div");
    taskCard.classList.add("card", "taskCard");
    taskCard.id = "task-" + task.ID;
    taskCard.draggable = false;
    if (canEdit) {
        taskCard.oncontextmenu = function (event) {
            event.preventDefault(); // Prevent the browser's context menu from appearing
            openTask(task.ID, projectID);
        }

        //MOBILE DOUBLE TAP
        let touchCount = 0;

        taskCard.addEventListener('touchend', function (event) {
            touchCount++;
            if (touchCount === 1) {
                setTimeout(function () {
                    if (touchCount === 2) {
                        if ('vibrate' in navigator) {
                            // Vibration supported
                            navigator.vibrate(100);
                        }
                        openTask(task.ID, projectID);
                    }
                    touchCount = 0;
                }, 300); // 300 milliseconds = 0.3 seconds
            }
        });
    }

    // Add task creator tooltip
    var creatorfirstName = task.CreatorFirstName;
    var creatorlastName = task.CreatorLastName;
    var creatorUsername = task.CreatorUsername;


    var creatorTooltip = '<a data-bs-toggle="tooltip" data-bs-title="Készítette: ' + creatorlastName + " " + creatorfirstName + " (" + creatorUsername + ")" + '"><i class="fas fa-info-circle"></i></a>';

    var taskHeader = document.createElement("div");
    taskHeader.classList.add("card-header", "taskHeader");

    if (task.Task_title) {
        var taskTitle = document.createElement("p");
        taskTitle.classList.add("card-title", "taskTitle");
        taskTitle.style.marginBottom = "0px";
        taskTitle.innerHTML = task.Task_title;
        taskHeader.appendChild(taskTitle);
    } else {
        taskHeader.style.justifyContent = "end";
    }
    let creatorSpan = document.createElement("div");
    //creatorSpan.classList.add("badge", "bg-light", "text-dark");
    creatorSpan.innerHTML = creatorTooltip;
    taskHeader.appendChild(creatorSpan);

    // Create drag handle
    if (window.innerWidth > 768) {
        let dragHandle = document.createElement("span");
        dragHandle.classList.add("dragHandle");
        dragHandle.innerHTML = "<i class='fas fa-grip-vertical'></i>";
        dragHandle.style.marginLeft = "5px";
        dragHandle.style.cursor = "grab";
        dragHandle.draggable = true;
        creatorSpan.appendChild(dragHandle);
    }

    taskCard.appendChild(taskHeader);

    let taskBody = document.createElement("div");
    taskBody.classList.add("card-body", "taskBody");

    // Generate certain task elements

    switch (task.Task_type) {

        case "task":
            var taskData = JSON.parse(task.Task_data);

            if (taskData.image == '') {
                let caption = document.createElement("p");
                caption.classList.add("card-text", "taskText");
                caption.innerHTML = makeFormatting(taskData.text);
                taskBody.appendChild(caption);
            } else {

                let imageContainer = document.createElement("div");
                imageContainer.style.position = "relative";

                let image = document.createElement("img");
                image.classList.add("card-img-top", "taskImage");
                image.src = taskData.image;
                imageContainer.appendChild(image);

                var expandButton = document.createElement("button");
                expandButton.classList.add("btn", "btn-sm", "expandButton");
                expandButton.style.position = "absolute";
                expandButton.style.top = "10px";
                expandButton.style.right = "10px";
                expandButton.style.color = "white";
                expandButton.innerHTML = "<i class='fas fa-expand-alt'></i>";
                expandButton.onclick = function () {
                    document.getElementById('expandedImage').src = taskData.image;
                    document.getElementById('imgDownloadButton').onclick = function () {
                        // Download the image
                        let imageUrl = taskData.image;
                        let imageName = "image.jpg";
                        let a = document.createElement("a");
                        a.href = imageUrl;
                        a.download = imageName;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }
                    $('#expandImageModal').modal('show');
                }
                imageContainer.appendChild(expandButton);

                taskBody.appendChild(imageContainer);

                let caption = document.createElement("p");
                caption.classList.add("card-text", "taskText");
                caption.innerHTML = makeFormatting(taskData.text);
                taskBody.appendChild(caption);
            }

            if (taskData.files) {
                let files = taskData.files;
                let filesContainer = document.createElement("div");
                filesContainer.classList.add("taskCardFiles");
                files.forEach(file => {
                    let fileDiv = document.createElement("div");
                    fileDiv.classList.add("fileElement");
                    fileDiv.innerHTML = `<i class="fas fa-file"></i> <a href="${file.link}" target="_blank">${file.name}</a>`;

                    let downloadButton = document.createElement("button");
                    downloadButton.classList.add("btn", "btn-sm", "btn-secondary", "float-end");
                    downloadButton.style.marginLeft = "5px";
                    downloadButton.innerHTML = '<i class="fas fa-download"></i>';
                    downloadButton.onclick = function (event) {
                        event.preventDefault();
                        getDownloadLink(file.path);
                    }

                    fileDiv.appendChild(downloadButton);
                    filesContainer.appendChild(fileDiv);
                });
                taskBody.appendChild(filesContainer);
            }
            break;
        case 'checklist':
            cardCheckOrRadio(taskBody, task, "checklist");
            break;
        case 'radio':
            cardCheckOrRadio(taskBody, task, "radio");
            break;
    }

    taskCard.appendChild(taskBody);


    cardFooter = document.createElement("div");
    cardFooter.classList.add("card-footer", "taskFooter");

    // check deadline and color the card accordingly
    if (task.Deadline) {
        var deadlineText = await getDeadline(task.Deadline);
        if (task.isSubmittable && uData == 100 || !task.isSubmittable) {
            colorTaskCard(taskHeader, task.Deadline);
            let deadline = document.createElement("span");
            deadline.classList.add("badge");
            deadline.innerHTML = deadlineText;
            //deadline.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="${task.Deadline.slice(0, -3)}">${deadlineText}</a>`;
            let deadlineColor = getDeadlineColor(task.Deadline);
            switch (deadlineColor) {
                case "overdue":
                    deadline.classList.add("bg-danger");
                    break;
                case "soon":
                    deadline.classList.add("bg-warning", "text-dark");
                    break;
                case "future":
                    deadline.classList.add("bg-success", "text-white");
                    break;
            }
            cardFooter.appendChild(deadline);
        }
        else {
            cardFooter.style.justifyContent = "end";
        }
    } else {
        cardFooter.style.justifyContent = "end";
    }

    // Check if task is filled out
    if (task.isSubmittable == 1 && uData != 404) {
        if (uData != 100) {
            // Add show answers button
            let showAnswersButton = document.createElement("button");
            showAnswersButton.classList.add("btn", "btn-sm", "showAnswersButton");
            showAnswersButton.innerHTML = `<i class="fas fa-stream fa-lg"></i>`;
            showAnswersButton.onclick = function () {
                openTaskAnswers(task.ID, projectID);
            }
            cardFooter.appendChild(showAnswersButton);
            cardFooter.style.justifyContent = "space-between";
        }
        if (uData == 100 || task.SingleAnswer == 0) {
            // ADD fill out button to task
            let fillOutButton = document.createElement("button");
            fillOutButton.classList.add("btn", "btn-sm", "fillOutButton", uData != 100 ? "btn-warning" : "btn-primary");
            fillOutButton.innerHTML = uData != 100 ? "Módosítás" : "Kitöltés";
            fillOutButton.onclick = function () {
                fillOutTask(task.ID);
            }
            cardFooter.appendChild(fillOutButton);
        } else {
            // Add "Leadva" text
            let filledOutText = document.createElement("p");
            filledOutText.classList.add("card-text", "taskText");
            filledOutText.innerHTML = "<i>Leadva</i>";
            cardFooter.appendChild(filledOutText);
        }

        taskCard.appendChild(cardFooter);
    }

    return taskCard;
}



async function openTask(TaskId, projectID) {
    console.log("Opening task: " + TaskId);

    // Fetch task
    let task = await fetchTask(null, TaskId);
    if (task == 403) {
        noAccessToast();
        return;
    }
    //console.log(task);
    task = JSON.parse(task);

    let modalTitle = document.getElementById("taskTitle");
    modalTitle.innerHTML = "Feladat szerkesztése";

    // Get the task data holder
    let taskDataHolder = document.getElementById("taskData");
    taskDataHolder.innerHTML = "";

    // Get the task title
    let taskTitle = task.Task_title;
    document.getElementById("textTaskName").value = taskTitle;

    // Get the task data
    let taskData = task.Task_data;

    document.getElementById("fillOutText").style.display = "none";
    document.getElementById("fillOutText").value = task.fillOutText;

    switch (task.Task_type) {
        case "task":
            await taskBodyGenerator(projectID, TaskId, taskDataHolder, taskData);
            break;
        case "checklist":
        case "radio":
            generateCheckOrRadioEditor(taskDataHolder, taskData, task.Task_type);
            document.getElementById('taskSubmittable').classList.add('active');
            break;
    }

    // Get task assigned users

    let taskMembers = await fetchTaskMembers(TaskId, projectID);
    taskMembers = JSON.parse(taskMembers);


    let taskMembersHolder = document.getElementById("taskMembers");
    taskMembersHolder.innerHTML = "<i>Adj hozzá tagokat a projekthez először!</i>";

    if (taskMembers != null) {
        taskMembersHolder.innerHTML = "";
        taskMembers.forEach(member => {
            var option = document.createElement("div");
            option.classList.add("availableMember");
            option.style.cursor = "pointer";
            option.id = member.UserId;
            option.innerHTML = `${member.lastName} ${member.firstName}`;
            option.onclick = function () {
                this.classList.toggle("selectedMember");
            }

            if (member.assignedToTask) {
                option.classList.add("selectedMember");
            }

            taskMembersHolder.appendChild(option);
        });
    }

    // Set the task submittable checkbox
    if (task.isSubmittable == 1) {
        document.getElementById("taskSubmittable").classList.add("active");
        document.getElementById("singleAnswer").disabled = false;
    } else {
        document.getElementById("taskSubmittable").classList.remove("active");
        document.getElementById("singleAnswer").disabled = true;
    }

    if (task.SingleAnswer == 1) {
        document.getElementById("singleAnswer").classList.add("active");
    } else {
        document.getElementById("singleAnswer").classList.remove("active");
    }

    // Get the task deadline
    let deadline = task.Deadline;
    let [date, time] = deadline ? deadline.split(" ") : ["", ""];
    time = time ? time.split(':').slice(0, 2).join(':') : "";

    document.getElementById("taskDate").value = date;
    document.getElementById("taskTime").value = time;

    // Set max date to project deadline
    let projectDeadline = task.ProjectDeadline;
    if (projectDeadline) {
        document.getElementById("taskDate").max = projectDeadline.split(" ")[0];
    }


    // Add delete button
    if (task.canDelete) {
        let deleteButton = document.getElementById("deleteTask");
        deleteButton.style.display = "block";
        deleteButton.onclick = function () {
            deleteTask(TaskId);
        }
    } else {
        let deleteButton = document.getElementById("deleteTask");
        deleteButton.style.display = "none";
    }

    // Add save button
    let saveButton = document.getElementById("saveNewTask");

    // Enable the button and remove the previous event listener
    saveButton.disabled = false;
    try {
        saveButton.removeEventListener('click', saveButtonHandler);
    } catch (error) {
        console.log("No event listener to remove");
    }
    // Create a new event handler with the current TaskId, taskType, and projectID
    saveButtonHandler = createSaveButtonHandler(TaskId, task.Task_type, projectID);

    // Add the new event listener
    saveButton.addEventListener('click', saveButtonHandler);

    // Display task editor modal
    $('#taskEditorModal').modal('show');
}

// Define the event handler function outside of the settings function
function createSaveButtonHandler(TaskId, taskType, projectID) {
    return async function saveButtonHandler(e) {
        e.preventDefault();
        this.disabled = true;
        saveTaskSettings(TaskId, taskType, projectID);
    };
}

function fillOutTask(TaskId) {
    console.log("Filling out task: " + TaskId);

    // Fetch task
    fetchTask(null, TaskId, true)
        .then(async response => {
            task = JSON.parse(response);

            // Get the task title
            let taskTitle = task.Task_title;
            document.getElementById("taskFillTitle").innerHTML = taskTitle;

            // Get the task data
            let taskData = task.Task_data;
            let taskBody = document.getElementById("taskFillData");
            taskBody.innerHTML = "";

            // If fillOutText is null, set it
            if (task.fillOutText == null || task.fillOutText == "") {
                task.fillOutText = "Megerősítés.";
            }

            switch (task.Task_type) {
                case "text":
                    let text = document.createElement("p");
                    text.classList.add("card-text", "taskText");
                    text.innerHTML = makeFormatting(taskData);
                    taskBody.appendChild(text);

                    addFillOutText(taskBody, task.fillOutText, TaskId);

                    break;
                case "image":
                    taskData = JSON.parse(taskData);
                    let image = document.createElement("img");
                    image.classList.add("card-img-top", "taskImage", "mb-2");
                    image.src = taskData.image;
                    taskBody.appendChild(image);

                    let caption = document.createElement("p");
                    caption.classList.add("card-text", "taskText");
                    caption.innerHTML = makeFormatting(taskData.text);
                    taskBody.appendChild(caption);

                    addFillOutText(taskBody, task.fillOutText, TaskId);
                    break;
                case "checklist":
                    generateCheckOrRadioFillOut(taskBody, task, "checklist");
                    break;
                case "radio":
                    generateCheckOrRadioFillOut(taskBody, task, "radio");
                    break;
            }

            // Get the task deadline
            let deadline = task.Deadline;
            let deadlineHolder = document.getElementById("taskFillDeadline");
            deadlineHolder.innerHTML = "";
            if (deadline) {
                let date = deadline.split(" ")[0];
                let time = deadline.split(" ")[1].split(":").slice(0, 2).join(":");

                let badge = document.createElement("span");
                badge.classList.add("badge", "bg-secondary");
                badge.style.fontSize = "1rem";
                badge.innerHTML = "Határidő: " + date + " " + time;
                deadlineHolder.appendChild(badge);
            } else {
                deadlineHolder.innerHTML = "";
            }

            // Add submit button
            let submitButton = document.getElementById("submitAnswer");
            submitButton.onclick = function () {
                submitTask(TaskId, task.Task_type);
            }

            // Display task editor modal
            $('#taskFillModal').modal('show');
        }).catch(() => {
            errorToast();
        });
}

// Task submission

async function addFillOutText(taskBody, fillOutText, taskId) {

    let UI = await fetchUI(taskId);
    if (UI != 404) {
        UI = JSON.parse(UI);
        var UIData = JSON.parse(UI.Data);
    } else {
        var UIData = false;
    }

    // Create a div for the checkbox
    let fillOutDiv = document.createElement("div");
    fillOutDiv.classList.add("form-check");

    // Create a checkbox
    let fillOutCheckbox = document.createElement("input");
    fillOutCheckbox.type = "checkbox";
    fillOutCheckbox.id = "fillOutCheckbox";
    fillOutCheckbox.classList.add("form-check-input");
    fillOutCheckbox.style.marginRight = "5px";
    if (UIData == 'done') {
        fillOutCheckbox.checked = true;
    }
    fillOutDiv.appendChild(fillOutCheckbox);

    // Create a label for the checkbox
    let fillOutLabel = document.createElement("label");
    fillOutLabel.classList.add("form-check-label");
    fillOutLabel.innerHTML = fillOutText;
    fillOutLabel.id = "fillOutText";
    fillOutDiv.appendChild(fillOutLabel);

    taskBody.appendChild(fillOutDiv);
}

// Task settings modal

async function addNewTask(projectID, taskType, deadline = null) {
    console.log("Adding new " + taskType + " task to project: " + projectID);

    let modalTitle = document.getElementById("taskTitle");
    document.getElementById("textTaskName").value = "";

    let taskDataHolder = document.getElementById("taskData");
    taskDataHolder.innerHTML = "";

    // Create the task data label
    let taskDataLabel = document.createElement("label");
    taskDataLabel.classList.add("col-form-label");
    taskDataLabel.innerHTML = "Adatok:";
    taskDataHolder.appendChild(taskDataLabel);

    document.getElementById("fillOutText").style.display = "none";
    switch (taskType) {
        case "task":
            modalTitle.innerHTML = "Új feladat hozzáadása";
            await taskBodyGenerator(projectID, null, taskDataHolder);
            break;

        case "checklist":
        case "radio":
            modalTitle.innerHTML = `Új lista hozzáadása (${taskType})`;
            generateNewCheckOrRadioEditor(taskDataHolder, taskType);
            document.getElementById('taskSubmittable').classList.add('active');
            break;
    }

    // Get task assigned users

    let taskMembers = await fetchProjectMembers(projectID);
    taskMembers = JSON.parse(taskMembers);


    let taskMembersHolder = document.getElementById("taskMembers");
    taskMembersHolder.innerHTML = "";

    taskMembers.forEach(member => {
        let option = document.createElement("div");
        option.classList.add("availableMember");
        option.style.cursor = "pointer";
        option.id = member.UserID;
        option.innerHTML = `${member.lastName} ${member.firstName}`;
        option.onclick = function () {
            this.classList.toggle("selectedMember");
        }
        option.classList.add("selectedMember");
        taskMembersHolder.appendChild(option);
    });

    // Set the max date to project deadline
    if (deadline) {
        document.getElementById("taskDate").max = deadline.split(" ")[0];
    }


    // Hide delete button if shown
    let deleteButton = document.getElementById("deleteTask");
    deleteButton.style.display = "none";

    // Display task editor modal
    $('#taskEditorModal').modal('show');

    // Get the save button
    let saveButton = document.getElementById('saveNewTask');

    // Add a click event listener to the button
    saveButton.addEventListener('click', async function (e) {
        e.preventDefault();
        saveTaskSettings(null, taskType, projectID);
        this.disabled = true;
    });

}


async function saveTaskSettings(task_id, taskType, projectID = null) {

    // Get the task name
    let taskName = document.getElementById("textTaskName").value;

    // Get the task deadline
    let taskDate = document.getElementById("taskDate").value;
    let taskTime = document.getElementById("taskTime").value;

    // Combine the date and time
    let taskDeadline = taskDate ? (taskTime ? `${taskDate} ${taskTime}` : `${taskDate} 23:59:59`) : "NULL";

    // Get the task data
    let taskDataHolder = document.getElementById("taskData");

    // Check if the task is interactable
    let isSubmittable = document.getElementById("taskSubmittable").classList.contains("active") ? 1 : 0;

    // Check if the task is single answer
    let singleAnswer = document.getElementById("singleAnswer").classList.contains("active") ? 1 : 0;

    let imageToUpload = null;
    let fillOutText = null;
    let taskData;

    switch (taskType) {
        case "task":
            fillOutText = document.getElementById('fillOutText').value;
            var caption = taskDataHolder.querySelector("#textTaskData").value;
            let imageLink = taskDataHolder.querySelector("#imageLink").value;
            let uploadImage = taskDataHolder.querySelector("#imageUpload").files[0];
            if (uploadImage) {
                imageToUpload = uploadImage;
            }

            let files = Array.from(document.getElementById("taskFiles").childNodes);
            let fileArray = files.map(file => {
                return {
                    name: file.innerText,
                    link: file.getAttribute("data-link"),
                    path: file.getAttribute("data-path"),
                };
            });

            taskData = {
                text: caption,
                image: imageLink,
                files: fileArray,
            }
            break;
        case "checklist":
        case "radio":
            var caption = taskDataHolder.querySelector("#textTaskData").value;
            let items = taskDataHolder.getElementsByClassName(taskType + "Item");
            let itemData = Array.from(items).map((item, i) => ({
                pos: i,
                value: item.value,
                checked: false,
            }));
            taskData = {
                text: caption,
                checklist: itemData
            }
            break;
    }

    // Getting assigned users
    let taskMembers = Array.from(document.getElementById("taskMembers").getElementsByClassName("availableMember"));
    let taskMembersArray = taskMembers.filter(member => member.classList.contains("selectedMember")).map(member => member.id);

    let task = {
        "ProjectId": projectID,
        "Task_type": taskType,
        "Task_title": taskName,
        "Task_data": taskData,
        "isSubmittable": isSubmittable,
        "fillOutText": fillOutText,
        "singleAnswer": singleAnswer,
        "Deadline": taskDeadline
    }

    // Save the task settings
    try {
        let response = await saveTaskToDB(task, taskMembersArray, imageToUpload, task_id);
        if (response == 200) {
            console.log("Task saved successfully");
            refreshProjects();
            successToast("Feladat mentve!");
            $('#taskEditorModal').modal('hide');
        } else {
            throw new Error(response);
        }
    } catch (error) {
        console.error(`Error: ${error}`);
        serverErrorToast();
    }


}


async function deleteTask(taskId) {
    console.log("Deleting task: " + taskId);

    document.getElementById('sureButton').innerHTML = "Törlés";
    document.getElementById('sureButton').classList.remove("btn-warning");
    document.getElementById('sureButton').classList.add("btn-danger");


    // Create a new Promise that resolves when the button is clicked
    let buttonClicked = new Promise((resolve, reject) => {
        document.getElementById('sureButton').addEventListener('click', resolve);
        document.getElementById('cancelButton').addEventListener('click', reject);
    });

    buttonClicked.then(async () => {
        // Delete the task
        let res = await deleteTaskFromDB(taskId)
        if (res == 200) {
            console.log("Task deleted successfully");
            simpleToast("Feladat törölve");
            document.getElementById("task-" + taskId).remove();
            $('#areyousureModal').modal('hide');
        } else if (res == 403) {
            noAccessToast();
            return;
        }
    }).catch(() => {
        // Code to run when 'otherButtonId' is clicked
        console.log("Task deletion cancelled");
        return;
    });


}

// Task submission

async function submitTask(taskId, taskType) {
    console.log(`Submitting task: ${taskId}`);

    // Get the task data
    let taskDataHolder = document.getElementById("taskFillData");
    let taskData = [];

    switch (taskType) {
        case "text":
        case "image":
            let done = document.getElementById("fillOutCheckbox").checked;
            taskData = done ? ['done'] : null;
            break;
        case "checklist":
        case "radio":
            let items = Array.from(taskDataHolder.getElementsByClassName("form-check"));
            let noneChecked = true;

            taskData = items.map((item, i) => {
                let isChecked = item.querySelector("input").checked;
                noneChecked = noneChecked && !isChecked;
                return {
                    pos: i,
                    value: item.querySelector("label").innerHTML,
                    checked: isChecked
                };
            });

            if (noneChecked) {
                taskData = null;
            }
            break;
    }

    // Save the task settings
    try {
        let response = await submitTaskToDB(taskId, taskData);
        if (response == 200) {
            refreshProjects();
            successToast("Feladat leadva");
            $('#taskFillModal').modal('hide');
        } else {
            throw new Error(response);
        }
    } catch (error) {
        console.error(`Error: ${error}`);
        serverErrorToast();
    }
}


// Extra functions

function colorTaskCard(taskHeader, deadline) {
    let currentDate = new Date();
    let taskDeadline = new Date(deadline);

    if (taskDeadline < currentDate) {
        taskHeader.classList.add("bg-danger", "text-white");
    } else if (taskDeadline - currentDate < (1000 * 60 * 60 * 48)) {
        taskHeader.classList.add("bg-warning");
    } else {
        //taskHeader.classList.add("bg-success", "text-white");
    }
}

function makeFormatting(taskData) {

    if (taskData == null) {
        return "";
    }

    // Check for new lines
    taskData = taskData.replace(/\n/g, "<br>");

    // Check for bold text
    taskData = taskData.replace(/\*\*(.*?)\*\*/g, "<b>$1</b>");

    // Check for italic text
    taskData = taskData.replace(/\*(.*?)\*/g, "<i>$1</i>");

    // Check for underline text
    taskData = taskData.replace(/__(.*?)__/g, "<u>$1</u>");

    // Check for links
    taskData = taskData.replace(/\[(.*?)\]\((.*?)\)/g, "<a href='$2' target='_blank'>$1</a>");

    return taskData;

}

function textEditor(taskDataHolder, taskData = "", height = "250px") {
    let textFormatOptions = document.createElement("div");
    textFormatOptions.classList.add("btn-group", "mb-1");

    let boldButton = document.createElement("button");
    boldButton.classList.add("btn");
    boldButton.innerHTML = "<b>B</b>";
    boldButton.onclick = function () {
        // Assume textarea is the textarea or input element where you want to bold the text
        var textarea = document.getElementById('textTaskData');

        // Get the current selection
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // If the start and end contain **, remove them else add them
        if (textarea.value.substring(start + 2, start) == "**" && textarea.value.substring(end, end - 2) == "**") {
            // Remove the **
            var boldText = textarea.value.substring(start + 2, end - 2);
            textarea.value = textarea.value.replace(textarea.value.substring(start, end), boldText);
            // Adjust the selection to exclude the **
            textarea.selectionStart = start;
            textarea.selectionEnd = end - 4;
        } else {
            // Add ** at the start and end
            var boldText = "**" + textarea.value.substring(start, end) + "**";
            textarea.value = textarea.value.substring(0, start) + boldText + textarea.value.substring(end);
            // Adjust the selection to include the added **
            textarea.selectionStart = start;
            textarea.selectionEnd = end + 4; // 4 is the total length of the added **
        }
        // Set the focus back to the textarea
        textarea.focus();
    }

    let italicButton = document.createElement("button");
    italicButton.classList.add("btn");
    italicButton.innerHTML = "<i>I</i>";
    italicButton.onclick = function () {
        var textarea = document.getElementById('textTaskData');
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        if (textarea.value.substring(start + 1, start) == "*" && textarea.value.substring(end, end - 1) == "*") {
            var italicText = textarea.value.substring(start + 1, end - 1);
            textarea.value = textarea.value.replace(textarea.value.substring(start, end), italicText);
            textarea.selectionStart = start;
            textarea.selectionEnd = end - 2;
        } else {
            var italicText = "*" + textarea.value.substring(start, end) + "*";
            textarea.value = textarea.value.substring(0, start) + italicText + textarea.value.substring(end);
            textarea.selectionStart = start;
            textarea.selectionEnd = end + 2;
        }
        textarea.focus();
    }

    let underlineButton = document.createElement("button");
    underlineButton.classList.add("btn");
    underlineButton.innerHTML = "<u>U</u>";
    underlineButton.onclick = function () {
        var textarea = document.getElementById('textTaskData');
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        if (textarea.value.substring(start + 2, start) == "__" && textarea.value.substring(end, end - 2) == "__") {
            var underlineText = textarea.value.substring(start + 2, end - 2);
            textarea.value = textarea.value.replace(textarea.value.substring(start, end), underlineText);
            textarea.selectionStart = start;
            textarea.selectionEnd = end - 4;
        } else {
            var underlineText = "__" + textarea.value.substring(start, end) + "__";
            textarea.value = textarea.value.substring(0, start) + underlineText + textarea.value.substring(end);
            textarea.selectionStart = start;
            textarea.selectionEnd = end + 4;
        }
        textarea.focus();
    }

    let linkButton = document.createElement("button");
    linkButton.classList.add("btn");
    linkButton.innerHTML = "<a href='#'>Link</a>";
    linkButton.onclick = function () {
        var textarea = document.getElementById('textTaskData');
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        if (textarea.value.substring(start + 1, start) == "[" && textarea.value.substring(end, end - 3) == "]()") {
            //var linkText = textarea.value.substring(start + 1, end - 3);
            //textarea.value = textarea.value.replace(textarea.value.substring(start, end), linkText);
            //textarea.selectionStart = start;
            //textarea.selectionEnd = end - 4;
        } else {
            var linkText = "[" + textarea.value.substring(start, end) + "]()";
            textarea.value = textarea.value.substring(0, start) + linkText + textarea.value.substring(end);
            textarea.selectionStart = start;
            textarea.selectionEnd = end + 4;
        }
        textarea.focus();
    }

    textFormatOptions.appendChild(boldButton);
    textFormatOptions.appendChild(italicButton);
    textFormatOptions.appendChild(underlineButton);
    textFormatOptions.appendChild(linkButton);

    // Preventing default action for buttons
    boldButton.addEventListener('click', function (event) {
        event.preventDefault();
    });
    italicButton.addEventListener('click', function (event) {
        event.preventDefault();
    });
    underlineButton.addEventListener('click', function (event) {
        event.preventDefault();
    });
    linkButton.addEventListener('click', function (event) {
        event.preventDefault();
    });

    taskDataHolder.appendChild(textFormatOptions);

    let textArea = document.createElement("textarea");
    textArea.classList.add("form-control", "mb-2");
    textArea.id = "textTaskData";
    textArea.value = taskData;
    textArea.placeholder = "Szöveg...";
    textArea.style.height = height;

    taskDataHolder.appendChild(textArea);
}

async function taskBodyGenerator(projectID, TaskId, taskDataHolder, taskData = null) {
    taskData = taskData ? JSON.parse(taskData) : null;

    document.getElementById("fillOutText").style.display = "block";
    textEditor(taskDataHolder, taskData ? taskData.text : "", "150px");

    var pictureDiv = document.createElement("div");
    pictureDiv.classList.add("input-group");

    let label = document.createElement("span");
    label.classList.add("input-group-text");
    label.innerHTML = "Kép:";
    pictureDiv.appendChild(label);

    var imageInput = document.createElement("input");
    imageInput.classList.add("form-control");
    imageInput.id = "imageLink";
    if (taskData) {
        imageInput.value = taskData.image;
    }
    imageInput.placeholder = "Kép URL...";
    pictureDiv.appendChild(imageInput);

    var uploadImage = document.createElement("input");
    uploadImage.type = "file";
    uploadImage.style.display = "none"; // Hide the file input
    uploadImage.accept = "image/*";
    uploadImage.name = "fileToUpload";
    uploadImage.id = "imageUpload";

    // Add an onchange event listener to the file input
    uploadImage.addEventListener("change", function () {
        if (this.files && this.files[0]) {
            imageInput.value = this.files[0].name;
        }
    });

    var uploadButton = document.createElement("button");
    uploadButton.type = "button";
    uploadButton.classList.add("btn", "btn-outline-success");
    uploadButton.innerHTML = `<i class="fas fa-upload"></i>`;

    // Trigger the file input when the button is clicked
    uploadButton.addEventListener("click", function () {
        uploadImage.click();
    });

    pictureDiv.appendChild(uploadImage);
    pictureDiv.appendChild(uploadButton);

    var resetButton = document.createElement("button");
    resetButton.classList.add("btn", "btn-outline-danger");
    resetButton.type = "button";
    resetButton.innerHTML = `<i class="fas fa-trash-alt"></i>`;
    if (TaskId == null) {
        resetButton.disabled = true;
    }
    resetButton.onclick = function () {
        deleteImage(TaskId);
    }
    pictureDiv.appendChild(resetButton);

    taskDataHolder.appendChild(pictureDiv);

    // Create file linker from NAS

    const filediv = document.getElementById("taskFileManager");
    filediv.style.display = "block";

    const fileHolder = document.getElementById("taskFiles");
    fileHolder.innerHTML = "";

    if (taskData.files != '') {
        
        taskData.files.forEach(file => {
            let fileDiv = document.createElement("div");
            fileDiv.classList.add("fileElement");
            fileDiv.innerHTML = `<i class="fas fa-file"></i> ${file.name}`;
            fileDiv.setAttribute("data-link", file.link);
            fileDiv.setAttribute("data-path", file.path);

            let deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-danger", "btn-sm", "float-end");
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
            deleteButton.style.marginLeft = "5px";
            deleteButton.onclick = function (event) {
                event.preventDefault();
                this.parentElement.remove();
            }

            fileDiv.appendChild(deleteButton);
            fileHolder.appendChild(fileDiv);
        });
    }

    const projectRoot = await fetchProjectRoot(projectID);

    const openBrowserButton = document.getElementById("browseProjectFiles");
    openBrowserButton.addEventListener("click", function (event) {
        event.preventDefault();
        browseNASFolder(projectID, TaskId, "selectFiles", projectRoot);
    });

}


function generateNewCheckOrRadioEditor(taskDataHolder, type) {
    taskDataHolder.innerHTML = "";

    textEditor(taskDataHolder, "", "100px");

    let label = document.createElement("label");
    label.classList.add("form-label");
    label.innerHTML = "Elemek:";
    taskDataHolder.appendChild(label);

    let checklist = document.createElement("ul");
    checklist.classList.add("list-group", "list-group-flush");
    checklist.id = type;

    let checklistItem = document.createElement("li");
    checklistItem.classList.add("list-group-item");
    checklistItem.innerHTML = "<input type='text' class='form-control " + type + "Item' placeholder='Új elem'>";
    checklist.appendChild(checklistItem);

    let newChecklistItem = document.createElement("button");
    newChecklistItem.classList.add("btn", "btn-success", "btn-sm");
    newChecklistItem.innerHTML = "Új elem";
    newChecklistItem.onclick = function () {
        let checklist = document.getElementById(type);
        let checklistItem = document.createElement("li");
        checklistItem.classList.add("list-group-item");
        checklistItem.innerHTML = "<input type='text' class='form-control " + type + "Item' placeholder='Új elem'>";
        checklist.appendChild(checklistItem);
    }

    // Prevent form submission
    newChecklistItem.addEventListener('click', function (event) {
        event.preventDefault();
    });

    taskDataHolder.appendChild(checklist);
    taskDataHolder.appendChild(newChecklistItem);
}


function generateCheckOrRadioEditor(taskDataHolder, taskData, type) {
    taskData = JSON.parse(taskData);
    var checklistItems = taskData.checklist;
    // Adding text before the checklist
    textEditor(taskDataHolder, taskData.text, '100px');

    let checklist = document.createElement("ul");
    checklist.classList.add("list-group", "list-group-flush");
    checklist.id = type;

    checklistItems.forEach(item => {
        let checklistItem = document.createElement("li");
        checklistItem.classList.add("list-group-item", "d-flex");

        let input = document.createElement("input");
        input.classList.add("form-control", type + "Item");
        input.value = item.value;
        checklistItem.appendChild(input);

        let deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-danger", "btn-sm");
        deleteButton.innerHTML = `<i class="fas fa-trash-alt"></i>`;
        deleteButton.style.marginLeft = "5px";
        deleteButton.addEventListener('click', function (event) {
            event.preventDefault();
            // If only one item is left return
            if (this.parentElement.parentElement.childElementCount == 1) {
                errorToast("Legalább egy elemnek kell lennie!");
            } else {
                checklistItem.remove();
            }
        });
        checklistItem.appendChild(deleteButton);
        checklist.appendChild(checklistItem);
    });

    let newChecklistItem = document.createElement("button");
    newChecklistItem.classList.add("btn", "btn-success", "btn-sm");
    newChecklistItem.innerHTML = "Új elem";
    newChecklistItem.onclick = function () {
        let checklistItem = document.createElement("li");
        checklistItem.classList.add("list-group-item", "d-flex");

        let input = document.createElement("input");
        input.classList.add("form-control", type + "Item");
        input.placeholder = "Új elem";
        checklistItem.appendChild(input);

        let deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-danger", "btn-sm");
        deleteButton.innerHTML = `<i class="fas fa-trash-alt"></i>`;
        deleteButton.style.marginLeft = "5px";
        deleteButton.addEventListener('click', function (event) {
            event.preventDefault();
            // If only one item is left return
            if (this.parentElement.parentElement.childElementCount == 1) {
                errorToast("Legalább egy elemnek kell lennie!");
            } else {
                checklistItem.remove();
            }
        });
        checklistItem.appendChild(deleteButton);
        checklist.appendChild(checklistItem);
    }

    // Prevent form submission
    newChecklistItem.addEventListener('click', function (event) {
        event.preventDefault();
    });

    taskDataHolder.appendChild(checklist);
    taskDataHolder.appendChild(newChecklistItem);
}


async function cardCheckOrRadio(taskBody, task, type) {
    taskData = JSON.parse(task.Task_data);

    if (taskData.text) {
        // Adding text before the checklist
        let text = document.createElement("p");
        text.classList.add("card-text", "taskText");
        text.innerHTML = makeFormatting(taskData.text);
        taskBody.appendChild(text);
    }
    let checklist = document.createElement("div");
    checklist.classList.add(type + "Holder");

    let checklistItems = taskData.checklist;
    try {
        var UIs = JSON.parse(await fetchUIs(task.ID));
        var UIData = UIs.map(ui => JSON.parse(ui.Data));
    } catch (error) {
        var UIData = false;
    }

    if (UIData[0] == null) {
        var UIData = false;
    }

    function decodeHtml(html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.textContent;
    }

    for (let i = 0; i < checklistItems.length; i++) {
        let checklistItem = document.createElement("div");
        checklistItem.classList.add("form-check");

        let input = document.createElement("input");
        input.classList.add("form-check-input");
        if (type == "checklist") {
            input.type = "checkbox";
        } else {
            input.type = "radio";
        }
        input.disabled = task.isSubmittable == 0 ? false : true;
        input.id = checklistItems[i].pos;

        if (task.isSubmittable == 0) {
            input.checked = checklistItems[i].checked;
            input.onclick = function () {
                saveCheckOrRadio(task.ID);
            }
        }

        checklistItem.appendChild(input);

        var selectedCount = 0;
        if (task.isSubmittable == 1) {
            for (let j = 0; j < UIData.length; j++) {
                if (UIData[j].length <= i) {
                    continue;
                }
                if (UIData[j][i].checked && decodeHtml(UIData[j][i].value) == checklistItems[i].value) {
                    selectedCount += 1;
                }
            }
        }
        let label = document.createElement("label");
        label.classList.add("form-check-label");
        label.htmlFor = checklistItems[i].pos;
        if (task.isSubmittable == 1) {
            label.innerHTML = checklistItems[i].value + " (" + selectedCount + ")";
        } else {
            label.innerHTML = checklistItems[i].value;
        }
        checklistItem.appendChild(label);

        checklist.appendChild(checklistItem);
    }

    taskBody.appendChild(checklist);
}

async function generateCheckOrRadioFillOut(taskBody, task, type) {
    taskData = JSON.parse(task.Task_data);

    if (taskData.text) {
        // Adding text before the checklist
        let text = document.createElement("p");
        text.classList.add("card-text", "taskText");
        text.innerHTML = makeFormatting(taskData.text);
        taskBody.appendChild(text);
    }
    let checklist = document.createElement("div");
    checklist.classList.add(type + "Holder");

    let checklistItems = taskData.checklist;

    let UI = await fetchUI(task.ID);
    if (UI != 404) {
        UI = JSON.parse(UI);
        var UIData = JSON.parse(UI.Data);
    } else {
        var UIData = false;
    }
    for (let i = 0; i < checklistItems.length; i++) {
        let checklistItem = document.createElement("div");
        checklistItem.classList.add("form-check");

        let input = document.createElement("input");
        input.classList.add("form-check-input");
        if (type == "checklist") {
            input.type = "checkbox";
        } else {
            input.type = "radio";
            input.name = "radio-" + task.ID;
        }
        input.id = checklistItems[i].pos;
        if (UIData) {
            input.checked = UIData[i].checked;
        }
        checklistItem.appendChild(input);

        let label = document.createElement("label");
        label.classList.add("form-check-label");
        label.htmlFor = checklistItems[i].pos;
        label.innerHTML = checklistItems[i].value;
        checklistItem.appendChild(label);

        checklist.appendChild(checklistItem);
    }
    taskBody.appendChild(checklist);

}

async function saveCheckOrRadio(taskId) {

    let taskCard = document.getElementById(`task-${taskId}`);
    let checklistItems = Array.from(taskCard.getElementsByClassName("form-check"));

    let taskTextElement = taskCard.querySelector(".taskText");
    let taskText = taskTextElement ? taskTextElement.innerHTML : null;
    let taskData = {
        text: taskText,
        checklist: checklistItems.map((item, i) => ({
            pos: i,
            value: item.querySelector("label").innerHTML,
            checked: item.querySelector("input").checked
        }))
    }

    try {
        let response = await $.post('../projectManager.php', {
            taskId: taskId,
            Task_data: JSON.stringify(taskData),
            mode: 'saveCheckOrRadio',
        });

        if (response == 200) {
            console.log("Checklist saved successfully");
            simpleToast(`Sikeres mentés!`);
        } else {
            throw new Error(response);
        }
    } catch (error) {
        console.error("Error: " + error);
        serverErrorToast();
    }
}


async function deleteImage(taskId) {
    console.log("Deleting image");

    let imageLink = document.getElementById("imageLink");
    imageLink.value = "";


    let formData = new FormData();
    formData.append('taskId', taskId);
    formData.append('mode', 'deleteImage');

    $.ajax({
        url: './upload-handler.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            console.log(data);
            if (data == 200) {
                simpleToast("Kép törölve");
            } else if (data == 404) {
                errorToast("Nincs feltöltött kép");
            } else {
                serverErrorToast();
            }
        }
    });
}