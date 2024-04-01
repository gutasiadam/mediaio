
async function generateTasks(projectID) {
    let taskHolder = document.createElement("div");
    taskHolder.classList.add("taskHolder");

    // Fetch the tasks
    let tasks = await fetchTask(projectID);
    // Parse the tasks
    tasks = JSON.parse(tasks);

    // Append each task to taskHolder
    for (let i = 0; i < tasks.length; i++) {
        taskHolder.appendChild(await createTask(tasks[i], projectID));
    }

    return taskHolder;
}

async function createTask(task, projectID) {
    var uData = await userTaskData(task.ID, projectID);

    let taskCard = document.createElement("div");
    taskCard.classList.add("card", "taskCard");
    taskCard.id = "task-" + task.ID;
    taskCard.draggable = false; // Make the taskCard draggable
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


    // Add task creator tooltip
    var creatorfirstName = task.CreatorFirstName;
    var creatorlastName = task.CreatorLastName;
    var creatorUsername = task.CreatorUsername;


    var creatorTooltip = '<a data-bs-toggle="tooltip" data-bs-title="' + creatorlastName + " " + creatorfirstName + " (" + creatorUsername + ")" + '">' + creatorfirstName + '</a>';

    if (task.Task_title) {
        var taskTitle = document.createElement("div");
        taskTitle.classList.add("card-header", "taskTitle");
        taskTitle.innerHTML = task.Task_title + " - " + creatorTooltip;
        taskCard.appendChild(taskTitle);
    }

    // check deadline and color the card accordingly
    if (task.Deadline) {
        if (task.isInteractable && uData == 100) {
            colorTaskCard(taskTitle, task.Deadline);
        } else if (!task.isInteractable) {
            colorTaskCard(taskTitle, task.Deadline);
        }
    }

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
            expandButton.innerHTML = "<i class='fas fa-expand-alt'></i>";
            expandButton.onclick = function () {
                document.getElementById('expandedImage').src = taskData.image;
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

    // Check if task is filled out

    if (task.isInteractable == 1 && uData != 404) {

        cardFooter = document.createElement("div");
        cardFooter.classList.add("card-footer", "taskFooter");
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
            console.log("Task not filled out yet" + task.ID);
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
        console.error("Szoptad a jogosultságot, bohóc!");
        return;
    }
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

    switch (task.Task_type) {
        case "text":
            textEditor(taskDataHolder, taskData, "150px");
            break;
        case "image":
            taskData = JSON.parse(taskData);
            textEditor(taskDataHolder, taskData.text, "100px");


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
                changeImage(TaskId, true);
            }
            uploadDiv.appendChild(resetButton);

            let uploadButton = document.createElement("button");
            uploadButton.classList.add("btn", "btn-outline-success");
            uploadButton.type = "button";
            uploadButton.innerHTML = "Feltöltés";
            uploadButton.onclick = function () {
                changeImage(TaskId);
            }
            uploadDiv.appendChild(uploadButton);
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
    let deleteButton = document.getElementById("deleteTask");
    deleteButton.style.display = "block";
    deleteButton.onclick = function () {
        deleteTask(TaskId);
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

            switch (task.Task_type) {
                case "text":
                    let text = document.createElement("p");
                    text.classList.add("card-text", "taskText");
                    text.innerHTML = makeFormatting(taskData);
                    taskBody.appendChild(text);

                    break;
                case "image":
                    taskData = JSON.parse(taskData);
                    let image = document.createElement("img");
                    image.classList.add("card-img-top", "taskImage", "mb-2");
                    image.src = taskData.image;
                    taskBody.appendChild(image);

                    let caption = document.createElement("p");
                    caption.classList.add("card-text", "taskText");
                    caption.innerHTML = taskData.text;
                    taskBody.appendChild(caption);
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
            if (deadline) {
                let date = deadline.split(" ")[0];
                let time = deadline.split(" ")[1];

                deadlineHolder.innerHTML = "Határidő: " + date + " " + time;
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


// Task settings modal

function addNewTask(projectID, taskType) {
    console.log("Adding new " + taskType + " task to project: " + projectID);

    let modalTitle = document.getElementById("taskTitle");

    let taskDataHolder = document.getElementById("taskData");
    taskDataHolder.innerHTML = "";

    // Create the task data label
    let taskDataLabel = document.createElement("label");
    taskDataLabel.classList.add("col-form-label");
    taskDataLabel.innerHTML = "Adatok:";
    taskDataHolder.appendChild(taskDataLabel);

    switch (taskType) {
        case "text":
            modalTitle.innerHTML = "Új feladat hozzáadása (szöveg)";

            textEditor(taskDataHolder);
            break;

        case "image":
            modalTitle.innerHTML = "Új feladat hozzáadása (kép)";

            textEditor(taskDataHolder, "", "100px");

            let imageInput = document.createElement("input");
            imageInput.classList.add("form-control", "mb-2");
            imageInput.id = "imageLink";
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
                changeImage(TaskId, true);
            }
            uploadDiv.appendChild(resetButton);

            let uploadButton = document.createElement("button");
            uploadButton.classList.add("btn", "btn-outline-success");
            uploadButton.type = "button";
            uploadButton.innerHTML = "Feltöltés";
            uploadButton.onclick = function () {
                changeImage(TaskId);
            }
            uploadDiv.appendChild(uploadButton);
            taskDataHolder.appendChild(uploadDiv);
            break;

        case "checklist":
            modalTitle.innerHTML = "Új feladat hozzáadása (checklist)";
            generateNewCheckOrRadioEditor(taskDataHolder, "checklist");
            break;

        case "radio":
            modalTitle.innerHTML = "Új feladat hozzáadása (radio)";
            generateNewCheckOrRadioEditor(taskDataHolder, "radio");
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

    switch (taskType) {
        case "text":
            var taskData = taskDataHolder.querySelector("#textTaskData").value;
            break;
        case "image":
            var caption = taskDataHolder.querySelector("#textTaskData").value;
            var imageLink = taskDataHolder.querySelector("#imageLink").value;
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
    console.log(taskMembersArray);

    let task = {
        "ProjectId": projectID,
        "Task_type": taskType,
        "Task_title": taskName,
        "Task_data": taskData,
        "isInteractable": isInteractable,
        "singleAnswer": singleAnswer,
        "Deadline": taskDeadline
    }
    console.log(task);

    if (task_id == null) {
        // Save the task
        if (await createNewTaskDB(task) == 200) {
            console.log("Task saved successfully");
            location.reload();
        };
    } else {

        // Save the task settings
        var response = await saveTaskToDB(task_id, task, taskMembersArray);
        if (response == 500) {
            console.error("Error: 500");
            return;
        }
        if (response == 200) {
            location.reload();
        }
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
        if (await deleteTaskFromDB(taskId) == 200) {
            console.log("Task deleted successfully");
            location.reload();
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

    if (!(taskType == "checklist" || taskType == "radio")) {
        console.error("Task type not supported");
        return;
    }

    // Get the task data
    let taskDataHolder = document.getElementById("taskFillData");

    let taskData = [];

    switch (taskType) {
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

        // Add ** at the start and end
        var boldText = "**" + textarea.value.substring(start, end) + "**";

        // Replace the selection with the bold text
        textarea.value = textarea.value.substring(0, start) + boldText + textarea.value.substring(end);

        // Adjust the selection to include the added **
        textarea.selectionStart = start;
        textarea.selectionEnd = end + 4; // 4 is the total length of the added **
    }

    let italicButton = document.createElement("button");
    italicButton.classList.add("btn");
    italicButton.innerHTML = "<i>I</i>";
    italicButton.onclick = function () {
        // Assume textarea is the textarea or input element where you want to bold the text
        var textarea = document.getElementById('textTaskData');

        // Get the current selection
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Add * at the start and end
        var boldText = "*" + textarea.value.substring(start, end) + "*";

        // Replace the selection with the italic text
        textarea.value = textarea.value.substring(0, start) + boldText + textarea.value.substring(end);

        // Adjust the selection to include the added **
        textarea.selectionStart = start;
        textarea.selectionEnd = end + 2; // 2 is the total length of the added *
    }

    let underlineButton = document.createElement("button");
    underlineButton.classList.add("btn");
    underlineButton.innerHTML = "<u>U</u>";
    underlineButton.onclick = function () {
        // Assume textarea is the textarea or input element where you want to bold the text
        var textarea = document.getElementById('textTaskData');

        // Get the current selection
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Add __ at the start and end
        var boldText = "__" + textarea.value.substring(start, end) + "__";

        // Replace the selection with the underline text
        textarea.value = textarea.value.substring(0, start) + boldText + textarea.value.substring(end);

        // Adjust the selection to include the added __
        textarea.selectionStart = start;
        textarea.selectionEnd = end + 4; // 4 is the total length of the added __
    }

    let linkButton = document.createElement("button");
    linkButton.classList.add("btn");
    linkButton.innerHTML = "<a href='#'>Link</a>";
    linkButton.onclick = function () {
        // Assume textarea is the textarea or input element where you want to insert the link
        var textarea = document.getElementById('textTaskData');

        // Get the current selection
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Add []() around the selected text
        var linkText = "[" + textarea.value.substring(start, end) + "]()";

        // Replace the selection with the link text
        textarea.value = textarea.value.substring(0, start) + linkText + textarea.value.substring(end);

        // Adjust the selection to include the added []()
        textarea.selectionStart = start;
        textarea.selectionEnd = end + 4; // 4 is the total length of the added []()
    };

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