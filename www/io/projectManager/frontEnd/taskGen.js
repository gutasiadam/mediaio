
async function generateTasks(projectID, canEdit) {
    let taskHolder = document.createElement("div");
    taskHolder.classList.add("taskHolder");
    taskHolder.id = projectID + "-taskHolder";

    // Adding dropping task card functionality
    taskHolder.addEventListener("dragover", function (event) {
        event.preventDefault();
    });

    taskHolder.addEventListener("drop", async function (event) {
        console.log("Dropped task");
        event.preventDefault();
        let taskID = event.dataTransfer.getData("text/plain");
        let taskCard = document.getElementById(taskID);

        // Append the task card to the task holder
        taskHolder.appendChild(taskCard);
    });

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
    //taskCard.draggable = true; // Make the taskCard draggable
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
    let dragHandle = document.createElement("span");
    dragHandle.classList.add("dragHandle");
    dragHandle.innerHTML = "<i class='fas fa-grip-vertical'></i>";
    dragHandle.style.marginLeft = "5px";
    dragHandle.style.cursor = "grab";
    dragHandle.draggable = true;
    creatorSpan.appendChild(dragHandle);

    // Add event listeners for the drag events
    dragHandle.addEventListener("mousedown", function (event) {
        event.preventDefault();
        console.log("Grabbing task mouse down");
        taskCard.style.cursor = "grabbing";
        taskCard.draggable = true; // Make the taskCard draggable when the mouse button is down on the dragHandle
    });

    document.addEventListener("mouseup", function (event) {
        if (taskCard.style.cursor === "grabbing") {
            event.preventDefault();
            taskCard.style.cursor = "grab";
            taskCard.draggable = false; // Make the taskCard not draggable when the mouse button is up
        }
    });

    // Add event listeners for the drag events
    taskCard.addEventListener("dragstart", function (event) {
        console.log("Dragging task");
        event.dataTransfer.setData("text/plain", taskCard.id);
        taskCard.style.cursor = "grabbing";
    });

    taskCard.addEventListener("dragend", function (event) {
        taskCard.style.cursor = "grab";
        taskCard.draggable = false; // Make the taskCard not draggable after the drag operation
    });


    taskCard.appendChild(taskHeader);

    let taskBody = document.createElement("div");
    taskBody.classList.add("card-body", "taskBody");

    // Generate certain task elements

    switch (task.Task_type) {

        case "text":
            let text = document.createElement("p");
            text.classList.add("card-text", "taskText");
            text.innerHTML = makeFormatting(task.Task_data);
            taskBody.appendChild(text);
            break;

        case 'image':
            var taskData = JSON.parse(task.Task_data);

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
        if (task.isInteractable && uData == 100 || !task.isInteractable) {
            colorTaskCard(taskHeader, task.Deadline);
            let deadline = document.createElement("span");
            deadline.classList.add("badge");
            deadline.innerHTML = deadlineText;
            if (deadlineText == "Lejárt" || deadlineText.includes("perc") || deadlineText == "Épp most") {
                deadline.classList.add("bg-danger");
            } else if (deadlineText.includes("óra")) {
                deadline.classList.add("bg-warning", "text-dark");
            } else {
                deadline.classList.add("bg-success");
            }
            cardFooter.appendChild(deadline);
        }
        else {
            cardFooter.style.justifyContent = "end";
        }
    }

    // Check if task is filled out

    if (task.isInteractable == 1 && uData != 404) {
        if (uData == 100 || task.SingleAnswer == 0) {
            // ADD  fill out button to task
            let fillOutButton = document.createElement("button");
            fillOutButton.classList.add("btn", "btn-primary", "btn-sm", "fillOutButton");
            fillOutButton.innerHTML = "Kitöltés";
            fillOutButton.onclick = function () {
                fillOutTask(task.ID);
            }
            cardFooter.appendChild(fillOutButton);
        } else {
            //console.log("Task not filled out yet" + task.ID);
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
        console.error("Ejnye ilyet nem lehet!");
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
        case "text":
            textEditor(taskDataHolder, taskData, "150px");
            document.getElementById("fillOutText").style.display = "block";
            break;
        case "image":
            taskData = JSON.parse(taskData);
            textEditor(taskDataHolder, taskData.text, "100px");
            document.getElementById("fillOutText").style.display = "block";


            let imageInput = document.createElement("input");
            imageInput.classList.add("form-control", "mb-2");
            imageInput.id = "imageLink";
            imageInput.value = taskData.image;
            imageInput.placeholder = "Kép URL...";
            taskDataHolder.appendChild(imageInput);

            let uploadDiv = document.createElement("div");
            uploadDiv.classList.add("input-group");

            let uploadImage = document.createElement("input");
            uploadImage.type = "file";
            uploadImage.classList.add("form-control");
            uploadImage.placeholder = "Kép feltöltése";
            uploadImage.name = "fileToUpload";
            uploadImage.id = "imageUpload";
            uploadImage.accept = "image/*";
            uploadDiv.appendChild(uploadImage);

            let resetButton = document.createElement("button");
            resetButton.classList.add("btn", "btn-outline-danger");
            resetButton.type = "button";
            resetButton.innerHTML = "Törlés";
            resetButton.onclick = function () {
                deleteImage(TaskId);
            }
            uploadDiv.appendChild(resetButton);

            taskDataHolder.appendChild(uploadDiv);
            break;
        case "checklist":
            generateCheckOrRadioEditor(taskDataHolder, taskData, "checklist");
            break;
        case "radio":
            generateCheckOrRadioEditor(taskDataHolder, taskData, "radio");
    }

    // Get task assigned users

    let taskMembers = await fetchTaskMembers(TaskId, projectID);
    taskMembers = JSON.parse(taskMembers);


    let taskMembersHolder = document.getElementById("taskMembers");
    taskMembersHolder.innerHTML = "";


    for (let i = 0; i < taskMembers.length; i++) {
        var member = taskMembers[i];

        var option = document.createElement("div");
        option.classList.add("availableMember");
        option.style.cursor = "pointer";
        option.id = member.UserId;
        option.innerHTML = member.lastName + " " + member.firstName;
        option.onclick = function () {
            if (this.classList.contains("selectedMember")) {
                this.classList.remove("selectedMember");
            }
            else {
                this.classList.add("selectedMember");
            }
        }

        if (member.assignedToTask) {
            option.classList.add("selectedMember");
        }

        taskMembersHolder.appendChild(option);
    }


    // Set the task submittable checkbox
    //console.log(task.isInteractable);
    //document.getElementById("taskSubmittable").setAttribute("aria-pressed", task.isInteractable == 1 ? "true" : "false");
    if (task.isInteractable == 1) {
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
    if (deadline) {
        let date = deadline.split(" ")[0];
        let time = deadline.split(" ")[1];

        // strip seconds from time
        time = time.split(':').slice(0, 2).join(':');

        document.getElementById("taskDate").value = date;
        document.getElementById("taskTime").value = time;
    } else {
        document.getElementById("taskDate").value = "";
        document.getElementById("taskTime").value = "";
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
    saveButton.onclick = function () {
        saveTaskSettings(TaskId, task.Task_type, projectID);
    }

    // Display task editor modal
    $('#taskEditorModal').modal('show');
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
            if (task.fillOutText == null) {
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

async function addNewTask(projectID, taskType) {
    console.log("Adding new " + taskType + " task to project: " + projectID);

    let modalTitle = document.getElementById("taskTitle");

    let taskDataHolder = document.getElementById("taskData");
    taskDataHolder.innerHTML = "";

    // Create the task data label
    let taskDataLabel = document.createElement("label");
    taskDataLabel.classList.add("col-form-label");
    taskDataLabel.innerHTML = "Adatok:";
    taskDataHolder.appendChild(taskDataLabel);

    document.getElementById("fillOutText").style.display = "none";
    switch (taskType) {
        case "text":
            modalTitle.innerHTML = "Új feladat hozzáadása";
            document.getElementById("fillOutText").style.display = "block";
            textEditor(taskDataHolder);
            break;

        case "image":
            modalTitle.innerHTML = "Új kép hozzáadása";
            document.getElementById("fillOutText").style.display = "block";
            textEditor(taskDataHolder, "", "100px");

            var imageInput = document.createElement("input");
            imageInput.classList.add("form-control", "mb-2");
            imageInput.id = "imageLink";
            imageInput.placeholder = "Kép URL...";
            taskDataHolder.appendChild(imageInput);

            var uploadDiv = document.createElement("div");
            uploadDiv.classList.add("input-group");

            var uploadImage = document.createElement("input");
            uploadImage.type = "file";
            uploadImage.classList.add("form-control");
            uploadImage.placeholder = "Kép feltöltése";
            uploadImage.name = "fileToUpload";
            uploadImage.id = "imageUpload";
            uploadImage.accept = "image/*";
            uploadDiv.appendChild(uploadImage);

            var resetButton = document.createElement("button");
            resetButton.classList.add("btn", "btn-outline-danger");
            resetButton.type = "button";
            resetButton.innerHTML = "Törlés";
            resetButton.onclick = function () {
                deleteImage(TaskId);
            }
            uploadDiv.appendChild(resetButton);

            taskDataHolder.appendChild(uploadDiv);
            break;

        case "file":
            modalTitle.innerHTML = "Új fájl hozzáadása";

            textEditor(taskDataHolder, "", "100px");

            var uploadDiv = document.createElement("div");
            uploadDiv.classList.add("input-group");

            var uploadFile = document.createElement("input");
            uploadFile.type = "file";
            uploadFile.classList.add("form-control");
            uploadFile.placeholder = "Fájl feltöltése";
            uploadFile.name = "fileToUpload";
            uploadFile.id = "fileUpload";
            uploadFile.accept = "*";
            uploadDiv.appendChild(uploadFile);

            var resetButton = document.createElement("button");
            resetButton.classList.add("btn", "btn-outline-danger");
            resetButton.type = "button";
            resetButton.innerHTML = "Törlés";
            resetButton.onclick = function () {
                deleteFile(TaskId);
            }
            uploadDiv.appendChild(resetButton);

            taskDataHolder.appendChild(uploadDiv);
            break;

        case "checklist":
            modalTitle.innerHTML = "Új lista hozzáadása (checklist)";
            generateNewCheckOrRadioEditor(taskDataHolder, "checklist");
            break;

        case "radio":
            modalTitle.innerHTML = "Új lista hozzáadása (radio)";
            generateNewCheckOrRadioEditor(taskDataHolder, "radio");
    }

    // Get task assigned users

    let taskMembers = await fetchProjectMembers(projectID);
    taskMembers = JSON.parse(taskMembers);


    let taskMembersHolder = document.getElementById("taskMembers");
    taskMembersHolder.innerHTML = "";


    for (let i = 0; i < taskMembers.length; i++) {
        var member = taskMembers[i];

        var option = document.createElement("div");
        option.classList.add("availableMember");
        option.style.cursor = "pointer";
        option.id = member.UserId;
        option.innerHTML = member.lastName + " " + member.firstName;
        option.onclick = function () {
            if (this.classList.contains("selectedMember")) {
                this.classList.remove("selectedMember");
            }
            else {
                this.classList.add("selectedMember");
            }
        }

        if (member.assignedToTask) {
            option.classList.add("selectedMember");
        }

        taskMembersHolder.appendChild(option);
    }

    // Hide delete button if shown
    let deleteButton = document.getElementById("deleteTask");
    deleteButton.style.display = "none";

    // Display task editor modal
    $('#taskEditorModal').modal('show');

    // Get the save button
    let saveButton = document.getElementById('saveNewTask');

    // Add a click event listener to the button
    saveButton.addEventListener('click', async function () {
        saveTaskSettings(null, taskType, projectID);
        this.disabled = true;
    });

}



async function saveTaskSettings(task_id, taskType, projectID = null) {

    // Get the task name
    var taskName = document.getElementById("textTaskName").value;

    // Get the task deadline
    var taskDate = document.getElementById("taskDate").value;
    var taskTime = document.getElementById("taskTime").value;

    // Combine the date and time
    let taskDeadline = "NULL";

    if (taskDate && taskTime) {
        taskDeadline = taskDate + " " + taskTime;
    } else if (taskDate) {
        taskDeadline = taskDate + " 23:59:59";
    }

    // Get the task data
    var taskDataHolder = document.getElementById("taskData");

    // Check if the task is interactable
    var isInteractable = document.getElementById("taskSubmittable");
    isInteractable = isInteractable.classList.contains("active") ? 1 : 0;

    // Check if the task is single answer
    var singleAnswer = document.getElementById("singleAnswer");
    singleAnswer = singleAnswer.classList.contains("active") ? 1 : 0;

    var imageToUpload = null;
    //
    var fillOutText = null;
    switch (taskType) {
        case "text":
            var taskData = taskDataHolder.querySelector("#textTaskData").value;
            fillOutText = document.getElementById('fillOutText').value;
            break;
        case "image":
            fillOutText = document.getElementById('fillOutText').value;
            var caption = taskDataHolder.querySelector("#textTaskData").value;
            var imageLink = taskDataHolder.querySelector("#imageLink").value;
            var uploadImage = taskDataHolder.querySelector("#imageUpload").files[0];
            if (uploadImage) {
                imageToUpload = uploadImage;
            }
            var taskData = {
                text: caption,
                image: imageLink
            }
            break;
        case "checklist":
            // Get caption data
            var caption = taskDataHolder.querySelector("#textTaskData").value;
            var checklistItems = taskDataHolder.getElementsByClassName("checklistItem");
            var checklistData = [];

            for (let i = 0; i < checklistItems.length; i++) {
                var checklistItem = {
                    pos: i,
                    value: checklistItems[i].value,
                }
                checklistData.push(checklistItem);
            }
            var taskData = {
                text: caption,
                checklist: checklistData
            }
            break;
        case "radio":
            var caption = taskDataHolder.querySelector("#textTaskData").value;
            var radioItems = taskDataHolder.getElementsByClassName("radioItem");
            var radioData = [];

            for (let i = 0; i < radioItems.length; i++) {
                var radioItem = {
                    pos: i,
                    value: radioItems[i].value,
                }
                radioData.push(radioItem);
            }
            var taskData = {
                text: caption,
                checklist: radioData
            }
            break;
    }

    // Getting assigned users
    let taskMembers = document.getElementById("taskMembers").getElementsByClassName("availableMember");
    let taskMembersArray = [];
    for (let i = 0; i < taskMembers.length; i++) {
        if (taskMembers[i].classList.contains("selectedMember")) {
            taskMembersArray.push(taskMembers[i].id);
        }
    }
    //console.log(taskMembersArray);

    let task = {
        "ProjectId": projectID,
        "Task_type": taskType,
        "Task_title": taskName,
        "Task_data": taskData,
        "isInteractable": isInteractable,
        "fillOutText": fillOutText,
        "singleAnswer": singleAnswer,
        "Deadline": taskDeadline
    }
    //console.log(task);

    // Save the task settings
    var response = await saveTaskToDB(task, taskMembersArray, imageToUpload, task_id);
    if (response == 500) {
        console.error("Error: 500");
        return;
    }
    if (response == 200) {
        console.log("Task saved successfully");
        location.reload();
    }


}


async function deleteTask(taskId) {
    console.log("Deleting task: " + taskId);

    document.getElementById('deleteTaskSure').innerHTML = "Törlés";
    document.getElementById('deleteTaskSure').classList.remove("btn-warning");
    document.getElementById('deleteTaskSure').classList.add("btn-danger");


    // Create a new Promise that resolves when the button is clicked
    let buttonClicked = new Promise((resolve, reject) => {
        document.getElementById('deleteTaskSure').addEventListener('click', resolve);
        document.getElementById('cancelButton').addEventListener('click', reject);
    });

    buttonClicked.then(async () => {
        // Delete the task
        let res = await deleteTaskFromDB(taskId)
        if (res == 200) {
            console.log("Task deleted successfully");
            location.reload();
        } else if (res == 403) {
            console.error("Ejnye ilyet nem lehet!");
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
    console.log("Submitting task: " + taskId);

    // Get the task data
    let taskDataHolder = document.getElementById("taskFillData");

    let taskData = [];

    switch (taskType) {
        case "text":
            var done = document.getElementById("fillOutCheckbox").checked;
            if (done) {
                taskData.push('done');
            } else {
                var fillOutText = taskDataHolder.querySelector("#fillOutText").innerHTML;
                alert(fillOutText);
                return;
            }
            break;
        case "image":
            var done = document.getElementById("fillOutCheckbox").checked;
            if (done) {
                taskData.push('done');
            } else {
                var fillOutText = taskDataHolder.querySelector("#fillOutText").innerHTML;
                alert(fillOutText);
                return;
            }
            break;
        case "checklist":
            let checklistItems = taskDataHolder.getElementsByClassName("form-check");

            for (let i = 0; i < checklistItems.length; i++) {
                let checklistItem = {
                    pos: i,
                    value: checklistItems[i].querySelector("label").innerHTML,
                    checked: checklistItems[i].querySelector("input").checked
                }
                taskData.push(checklistItem);
            }
            break;
        case "radio":
            let radioItems = taskDataHolder.getElementsByClassName("form-check");

            for (let i = 0; i < radioItems.length; i++) {
                let radioItem = {
                    pos: i,
                    value: radioItems[i].querySelector("label").innerHTML,
                    checked: radioItems[i].querySelector("input").checked
                }
                taskData.push(radioItem);
            }
            break;
    }

    // Save the task settings
    let response = await submitTaskToDB(taskId, taskData);
    if (response == 500) {
        console.error("Error: 500");
        return;
    }
    if (response == 200) {
        location.reload();
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

    for (let i = 0; i < checklistItems.length; i++) {
        let checklistItem = document.createElement("li");
        checklistItem.classList.add("list-group-item");
        checklistItem.innerHTML = "<input type='text' class='form-control " + type + "Item' value='" + checklistItems[i].value + "'>";
        checklist.appendChild(checklistItem);
    }

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
        input.disabled = true;
        input.id = checklistItems[i].pos;
        checklistItem.appendChild(input);

        let selectedCount = 0;
        for (let j = 0; j < UIData.length; j++) {
            if (UIData[j].length <= i) {
                continue;
            }
            if (UIData[j][i].checked && UIData[j][i].value == checklistItems[i].value) {
                selectedCount += 1;
            }
        }

        let label = document.createElement("label");
        label.classList.add("form-check-label");
        label.htmlFor = checklistItems[i].pos;
        label.innerHTML = checklistItems[i].value + " (" + selectedCount + ")";
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
                console.log("Image deleted successfully");
            }
        }
    });
}