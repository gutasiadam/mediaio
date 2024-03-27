

function generateProjects(project) {

    for (let i = 0; i < project.length; i++) {
        generateProjectBody(project[i]);
    }
}


// Function to generate a trello like project

async function generateProjectBody(project) {
    //console.log("Generating project body");
    //console.log(project);
    let projectName = project.Name;
    let projectID = project.ID;

    // Div to add projects to
    let projectHolder = document.getElementById("projectHolder");

    // Create a new project holder card
    let projectCard = document.createElement("div");
    projectCard.classList.add("card", "projectCard");
    projectCard.id = projectID;
    colorCardBasedOnDeadline(projectCard, project.Deadline);
    //projectCard.draggable = true; // Make the projectCard draggable
    projectHolder.appendChild(projectCard);

    // Create a new project header
    let projectHeader = document.createElement("div");
    projectHeader.classList.add("card-header", "projectTitle");

    // Create nav
    let nav = document.createElement("ul");
    nav.classList.add("nav", "nav-tabs", "card-header-tabs");

    // Create li elements
    let tasks = document.createElement("li");
    tasks.classList.add("nav-item");
    tasks.innerHTML = "<button class='nav-link active' id='task-tab' data-bs-toggle='tab' data-bs-target='#task-tab-pane-" + projectID + "' type='button' role='tab' aria-controls='task-tab-pane' aria-selected='true'>" + projectName + "</button>";
    nav.appendChild(tasks);

    let members = document.createElement("li");
    members.classList.add("nav-item");
    members.innerHTML = "<button class='nav-link' id='users-tab' data-bs-toggle='tab' data-bs-target='#users-tab-pane-" + projectID + "' type='button' role='tab' aria-controls='users-tab-pane' aria-selected='false'>Tagok</button>";
    nav.appendChild(members);


    projectHeader.appendChild(nav);

    // Add settings button to project title
    try {
        projectHeader.appendChild(changeProjectSettingsButton(projectID));
    } catch (error) {
        ;
    }

    projectCard.appendChild(projectHeader);

    // Create a card project body
    let CardBody = document.createElement("div");
    CardBody.classList.add("card-body", "tab-content");
    projectCard.appendChild(CardBody);


    // Create a new project body
    let projectBody = document.createElement("div");
    projectBody.classList.add("projectBody", "tab-pane", "fade", "show", "active");
    projectBody.id = "task-tab-pane-" + projectID;
    projectBody.role = "tabpanel";
    projectBody.ariaLabelledby = "task-tab";
    CardBody.appendChild(projectBody);


    // Create a new project description
    projectBody.appendChild(createDiscription(projectID, project.Description));

    // Generating the project tasks
    projectBody.appendChild(await generateTasks(projectID));

    let addTask = document.createElement("button");
    addTask.classList.add("btn", "btn-success", "dropdown-toggle", "addTask");
    addTask.innerHTML = "Új hozzáadása";
    addTask.setAttribute("data-bs-toggle", "dropdown");
    projectBody.appendChild(addTask);

    // Creat ul dropdown
    let ul = document.createElement("ul");
    ul.classList.add("dropdown-menu");
    projectBody.appendChild(ul);

    // create li elements
    let text = document.createElement("li");
    text.classList.add("dropdown-item");
    text.innerHTML = "Szöveg";
    text.style.cursor = "pointer";
    text.onclick = function () {
        addNewTask(projectID, "text");
    }
    ul.appendChild(text);

    let image = document.createElement("li");
    image.classList.add("dropdown-item");
    image.innerHTML = "Kép";
    image.style.cursor = "pointer";
    image.onclick = function () {
        addNewTask(projectID, "image");
    }
    ul.appendChild(image);


    // Create Body for members
    let membersBody = document.createElement("div");
    membersBody.classList.add("card-body", "projectBody", "tab-pane", "fade");
    membersBody.id = "users-tab-pane-" + projectID;
    membersBody.role = "tabpanel";
    membersBody.ariaLabelledby = "users-tab";
    CardBody.appendChild(membersBody);


    // Adding members to the project body
    membersBody.appendChild(await generateMembers(projectID));

    // Add + button to add new members
    membersBody.appendChild(await editProjectMembersButton(projectID));


    return projectCard;
}

function colorCardBasedOnDeadline(projectCard, deadline) {
    if (deadline) {
        let now = new Date();
        let projectDeadline = new Date(deadline);

        let diff = projectDeadline - now;

        if (diff < 0) {
            projectCard.classList.add("overdue");
        } else if (diff < 86400000) {
            projectCard.classList.add("soon");
        }
    }
}


function createDiscription(projectID, Description) {
    // Create a new project description
    let projectDescriptionHolder = document.createElement("div");
    projectDescriptionHolder.classList.add("projectDescriptionHolder");

    let projectDescription = document.createElement("span");
    projectDescription.classList.add("card-text", "projectDescription");
    projectDescription.innerHTML = Description;
    projectDescriptionHolder.appendChild(projectDescription);

    try {
        projectDescriptionHolder.appendChild(editDescriptionButton(projectID));
    } catch (error) {
        ;
    }

    return projectDescriptionHolder;
}


async function generateTasks(projectID) {
    let taskHolder = document.createElement("div");
    taskHolder.classList.add("taskHolder");

    // Fetch the tasks
    let tasks = await fetchTasks(projectID);

    // Parse the tasks
    tasks = JSON.parse(tasks);

    // Append each task to taskHolder
    for (let i = 0; i < tasks.length; i++) {
        taskHolder.appendChild(await createTask(tasks[i]));
    }

    return taskHolder;
}

async function generateMembers(proj_id) {
    let membersHolder = document.createElement("div");
    membersHolder.classList.add("membersHolder");

    // Fetch the members
    let userList = await getUsers();

    // Parse the members
    userList = JSON.parse(userList);

    // Load project members
    fetchProjectMembers(proj_id)
        .then(async response => {
            projectMembers = JSON.parse(response);
            projectMembers = projectMembers.map(member => member.UserID);
            console.log(projectMembers);

            // Append each member to membersHolder
            for (let i = 0; i < userList.length; i++) {
                let member = userList[i];

                if (projectMembers.includes(member.idUsers.toString())) {
                    membersHolder.appendChild(await createMember(member, proj_id, member.idUsers));
                }
            }
        })
        .catch(error => console.error('Error fetching project members:', error));

    return membersHolder;
}

async function createMember(member, projectID, memberID) {
    let memberCard = document.createElement("div");
    memberCard.classList.add("card", "memberCard", "mb-2");

    let memberBody = document.createElement("div");
    memberBody.classList.add("card-body", "memberBody");

    let memberName = document.createElement("span");
    memberName.classList.add("card-text", "memberName");
    memberName.innerHTML = member.lastName + " " + member.firstName;
    memberBody.appendChild(memberName);

    try {
        memberBody.appendChild(removeMemberFromProjectButton(projectID, memberID));
    } catch (error) {
        ;
    }

    memberCard.appendChild(memberBody);

    return memberCard;
}

async function createTask(task) {

    let taskCard = document.createElement("div");
    taskCard.classList.add("card", "taskCard");
    taskCard.id = "task-" + task.ID;
    taskCard.draggable = false; // Make the taskCard draggable
    taskCard.onclick = function () {
        openTask(task.ID);
    }

    if (task.Task_title) {
        let taskTitle = document.createElement("div");
        taskTitle.classList.add("card-header", "taskTitle");
        taskTitle.innerHTML = task.Task_title;
        taskCard.appendChild(taskTitle);
    }

    let taskBody = document.createElement("div");
    taskBody.classList.add("card-body", "taskBody");

    // Generate certain task elements

    switch (task.Task_type) {

        case "text":
            let text = document.createElement("p");
            text.classList.add("card-text", "taskText");
            text.innerHTML = task.Task_data;
            taskBody.appendChild(text);
            break;

        case 'image':
            let image = document.createElement("img");
            image.classList.add("card-img-top", "taskImage");
            image.src = task.Task_Image;
            taskBody.appendChild(image);
            break;
    }

    taskCard.appendChild(taskBody);

    return taskCard;
}


function addNewTask(projectID, taskType) {
    console.log("Adding new " + taskType + " task to project: " + projectID);

    let modalTitle = document.getElementById("newTaskTitle");


    switch (taskType) {
        case "text":
            document.getElementById("taskData").placeholder = "Szöveg...";
            modalTitle.innerHTML = "Új feladat hozzáadása (szöveg)";
            break;

        case "image":
            document.getElementById("taskData").placeholder = "Kép URL...";
            modalTitle.innerHTML = "Új feladat hozzáadása (kép)";
            break;
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
        // Get task title-name
        let taskTitle = document.getElementById('taskName').value;

        // Get the task data
        let taskData = document.getElementById('taskData').value;

        let deadline = "NULL";


        let date = document.getElementById('taskDate').value;
        let time = document.getElementById('taskTime').value;

        if (date && time) {
            // Combine the date and time
            deadline = date + " " + time;
        }

        task = {
            ProjectId: projectID,
            Task_type: taskType,
            Task_title: taskTitle,
            Task_data: taskData,
            Deadline: deadline
        }

        // Save the task
        if (await createNewTaskDB(task) == 200) {
            console.log("Task saved successfully");
            location.reload();
        };
    });

}



async function openTask(TaskId) {
    if (!editorON) {
        return;
    }
    console.log("Opening task: " + TaskId);

    // Fetch task
    let task = await fetchTask(TaskId);
    task = JSON.parse(task);

    let modalTitle = document.getElementById("newTaskTitle");
    modalTitle.innerHTML = "Feladat szerkesztése";

    // Get the task title
    let taskTitle = task.Task_title;
    document.getElementById("taskName").value = taskTitle;

    // Get the task data
    let taskData = task.Task_data;
    document.getElementById("taskData").value = taskData;


    // Get the task deadline
    let deadline = task.Deadline;
    if (deadline) {
        let date = deadline.split(" ")[0];
        let time = deadline.split(" ")[1];
        document.getElementById("taskDate").value = date;
        document.getElementById("taskTime").value = time;
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
        saveTaskSettings(TaskId);
    }

    // Display task editor modal
    $('#taskEditorModal').modal('show');
}