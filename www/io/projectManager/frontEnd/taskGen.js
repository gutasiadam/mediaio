
async function generateTasks(projectID) {
    let taskHolder = document.createElement("div");
    taskHolder.classList.add("taskHolder");

    // Fetch the tasks
    let tasks = await fetchTasks(projectID);

    // Parse the tasks
    tasks = JSON.parse(tasks);

    // Append each task to taskHolder
    for (let i = 0; i < tasks.length; i++) {
        taskHolder.appendChild(await createTask(tasks[i], projectID));
    }

    return taskHolder;
}

async function createTask(task, projectID) {

    let taskCard = document.createElement("div");
    taskCard.classList.add("card", "taskCard");
    taskCard.id = "task-" + task.ID;
    taskCard.draggable = false; // Make the taskCard draggable
    taskCard.onclick = function () {
        openTask(task.ID, projectID);
    }

    if (task.Task_title) {
        let taskTitle = document.createElement("div");
        taskTitle.classList.add("card-header", "taskTitle");
        taskTitle.innerHTML = task.Task_title + " - " + await fetchTaskCreator(task.ID);
        taskCard.appendChild(taskTitle);
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
            let image = document.createElement("img");
            image.classList.add("card-img-top", "taskImage");
            image.src = task.Task_data;
            taskBody.appendChild(image);
            break;
        case 'checklist':
            cardCheckOrRadio(taskBody, task, "checklist");
            break;
        case 'radio':
            cardCheckOrRadio(taskBody, task, "radio");
            break;
    }

    taskCard.appendChild(taskBody);

    if (task.isInteractable == 1) { // TODO: Check if user is assigned to project 

        cardFooter = document.createElement("div");
        cardFooter.classList.add("card-footer", "taskFooter");

        // ADD  fill out button to task
        let fillOutButton = document.createElement("button");
        fillOutButton.classList.add("btn", "btn-primary", "btn-sm", "fillOutButton");
        fillOutButton.innerHTML = "Kitöltés";
        fillOutButton.onclick = function () {
            fillOutTask(task.ID);
        }
        cardFooter.appendChild(fillOutButton);

        taskCard.appendChild(cardFooter);
    }

    return taskCard;
}



async function openTask(TaskId, projectID) {
    if (!editorON) {
        return;
    }
    console.log("Opening task: " + TaskId);

    // Fetch task
    let task = await fetchTask(TaskId);
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
            textEditor(taskDataHolder, taskData);
            break;
        case "image":
            let imageInput = document.createElement("input");
            imageInput.classList.add("form-control");
            imageInput.id = "textTaskData";
            imageInput.value = taskData;
            taskDataHolder.appendChild(imageInput);
            break;
        case "checklist":
            generateCheckOrRadioEditor(taskDataHolder, taskData, "checklist");
            break;
        case "radio":
            generateCheckOrRadioEditor(taskDataHolder, taskData, "radio");
    }

    // Get task assigned users

    let projectMembers = await fetchProjectMembers(projectID);
    projectMembers = JSON.parse(projectMembers);
    projectMembers = projectMembers.map(member => member.UserID);

    let projectMemberNames = await fetchMemberNames(projectMembers);


    let taskMembers = await fetchTaskMembers(TaskId);
    taskMembers = JSON.parse(taskMembers);
    taskMembers = taskMembers.map(member => member.UserId);


    let taskMembersHolder = document.getElementById("taskMembers");
    taskMembersHolder.innerHTML = "";

    for (let i = 0; i < projectMembers.length; i++) {
        let member = projectMemberNames[i];
        let memberDiv = document.createElement("div");
        memberDiv.classList.add("form-check");

        let input = document.createElement("input");
        input.classList.add("form-check-input");
        input.type = "checkbox";
        input.id = "member-" + member.idUsers;
        input.checked = taskMembers.includes(member.idUsers);
        memberDiv.appendChild(input);

        let label = document.createElement("label");
        label.classList.add("form-check-label");
        label.htmlFor = "member-" + member.idUsers;
        label.innerHTML = member.lastName + " " + member.firstName;
        memberDiv.appendChild(label);

        taskMembersHolder.appendChild(memberDiv);
    }


    // Set the task submittable checkbox
    document.getElementById("taskSubmittable").checked = task.isInteractable;


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
    fetchTask(TaskId)
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
                    let image = document.createElement("img");
                    image.classList.add("card-img-top", "taskImage");
                    image.src = taskData;
                    taskBody.appendChild(image);

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

            let imageInput = document.createElement("input");
            imageInput.classList.add("form-control");
            imageInput.id = "textTaskData";
            imageInput.placeholder = "Kép URL...";
            taskDataHolder.appendChild(imageInput);
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
    var isInteractable = document.getElementById("taskSubmittable").checked ? 1 : 0;

    switch (taskType) {
        case "text":
            var taskData = taskDataHolder.querySelector("#textTaskData").value;
            break;
        case "image":
            var taskData = taskDataHolder.querySelector("#textTaskData").value;
            break;
        case "checklist":
            var checklistItems = taskDataHolder.getElementsByClassName("checklistItem");
            var taskData = [];

            for (let i = 0; i < checklistItems.length; i++) {
                var checklistItem = {
                    pos: i,
                    value: checklistItems[i].value,
                }
                taskData.push(checklistItem);
            }
            break;
        case "radio":
            var radioItems = taskDataHolder.getElementsByClassName("radioItem");
            var taskData = [];

            for (let i = 0; i < radioItems.length; i++) {
                var radioItem = {
                    pos: i,
                    value: radioItems[i].value,
                }
                taskData.push(radioItem);
            }
            break;
    }

    // Getting assigned users
    let taskMembers = document.getElementById("taskMembers").getElementsByClassName("form-check");
    let taskMembersArray = [];
    for (let i = 0; i < taskMembers.length; i++) {
        if (taskMembers[i].querySelector("input").checked) {
            taskMembersArray.push(taskMembers[i].querySelector("input").id.split("-")[1]);
        }
    }
    console.log(taskMembersArray);

    let task = {
        "ProjectId": projectID,
        "Task_type": taskType,
        "Task_title": taskName,
        "Task_data": taskData,
        "isInteractable": isInteractable,
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

    // Create a new Promise that resolves when the button is clicked
    let buttonClicked = new Promise((resolve, reject) => {
        document.getElementById('deleteTaskSure').addEventListener('click', resolve);
    });

    // Wait for the button to be clicked
    await buttonClicked;

    // Delete the task
    if (await deleteTaskFromDB(taskId) == 200) {
        console.log("Task deleted successfully");
        location.reload();
    }
}

// Task submission

async function submitTask(taskId, taskType) {
    console.log("Submitting task: " + taskId);

    if (taskType != "checklist") {
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

function textEditor(taskDataHolder, taskData = "") {
    let textFormatOptions = document.createElement("div");
    textFormatOptions.classList.add("btn-group", "mb-1");

    let boldButton = document.createElement("button");
    boldButton.classList.add("btn");
    boldButton.innerHTML = "<b>B</b>";
    boldButton.onclick = function () {
        // Get the current selection
        var selection = window.getSelection();

        // Add ** at the start and end
        var boldText = "**" + selection.toString() + "**";

        // Replace the selection with the bold text
        document.execCommand("insertText", false, boldText);
    }

    let italicButton = document.createElement("button");
    italicButton.classList.add("btn");
    italicButton.innerHTML = "<i>I</i>";
    italicButton.onclick = function () {
        // Get the current selection
        var selection = window.getSelection();

        // Add ** at the start and end
        var italicText = "*" + selection.toString() + "*";

        // Replace the selection with the bold text
        document.execCommand("insertText", false, italicText);
    }

    let underlineButton = document.createElement("button");
    underlineButton.classList.add("btn");
    underlineButton.innerHTML = "<u>U</u>";
    underlineButton.onclick = function () {
        // Get the current selection
        var selection = window.getSelection();

        // Add ** at the start and end
        var underlineText = "__" + selection.toString() + "__";

        // Replace the selection with the bold text
        document.execCommand("insertText", false, underlineText);
    }

    let linkButton = document.createElement("button");
    linkButton.classList.add("btn");
    linkButton.innerHTML = "<a href='#'>Link</a>";
    linkButton.onclick = function () {
        // Get the current selection
        var selection = window.getSelection();

        // Add ** at the start and end
        var linkText = "[" + selection.toString() + "]()";

        // Replace the selection with the bold text
        document.execCommand("insertText", false, linkText);
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
    textArea.classList.add("form-control");
    textArea.id = "textTaskData";
    textArea.value = taskData;
    textArea.placeholder = "Szöveg...";
    textArea.style.height = "250px";

    taskDataHolder.appendChild(textArea);
}

function generateNewCheckOrRadioEditor(taskDataHolder, type) {
    taskDataHolder.innerHTML = "";

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
    let checklistItems = JSON.parse(taskData);

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
    let checklist = document.createElement("div");
    checklist.classList.add(type + "Holder");

    let checklistItems = JSON.parse(task.Task_data);
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
    let checklist = document.createElement("div");
    checklist.classList.add(type + "Holder");

    let checklistItems = JSON.parse(task.Task_data);

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