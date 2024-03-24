

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
    //projectCard.draggable = true; // Make the projectCard draggable
    projectHolder.appendChild(projectCard);

    // Create a new project title header
    let projectTitle = document.createElement("div");
    projectTitle.classList.add("card-header", "projectTitle");

    // Add the title to the project title
    let title = document.createTextNode(projectName);
    projectTitle.appendChild(title);

    // Add settings button to project title
    try {
        projectTitle.appendChild(changeProjectSettingsButton(projectID));
    } catch (error) {
        ;
    }

    projectCard.appendChild(projectTitle);


    // Create a new project body
    let projectBody = document.createElement("div");
    projectBody.classList.add("card-body", "projectBody");
    projectCard.appendChild(projectBody);

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



    return projectCard;
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



async function createTask(task) {

    let taskCard = document.createElement("div");
    taskCard.classList.add("card", "taskCard");
    taskCard.id = task.ID;
    taskCard.draggable = true; // Make the taskCard draggable
    taskCard.onclick = function () {
        openTask(task.ID);
    }
    taskCard.style.cursor = "pointer";

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
    let projectName = document.getElementById(projectID).querySelector(".projectTitle").innerText;


    switch (taskType) {
        case "text":
            document.getElementById("taskData").placeholder = "Szöveg...";
            modalTitle.innerHTML = projectName + " - szöveg hozzáadása";
            break;

        case "image":
            document.getElementById("taskData").placeholder = "Kép URL...";
            modalTitle.innerHTML = projectName + " - kép hozzáadása";
            break;
    }

    // Display task editor modal
    $('#taskEditorModal').modal('show');

    // Get the saveNewProject button
    let saveNewProjectButton = document.getElementById('saveNewProject');

    // Add a click event listener to the button
    saveNewProjectButton.addEventListener('click', async function () {
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



function openTask(TaskId) {
    console.log("Opening task: " + TaskId);

    let modalTitle = document.getElementById("newTaskTitle");
    modalTitle.innerHTML = "Feladat szerkesztése";

    // Get the task
    let task = document.getElementById(TaskId);

    // Get the task title
    let taskTitle = task.querySelector(".taskTitle").innerText;
    document.getElementById("taskName").value = taskTitle;

    // Get the task data
    let taskData = task.querySelector(".tasktext").innerText;

    document.getElementById("taskData").value = taskData;

    // Get the task deadline
    //let deadline = task.querySelector(".taskDeadline").innerText;

    // Display task editor modal
    $('#taskEditorModal').modal('show');
}