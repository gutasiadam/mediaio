

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
    projectHolder.appendChild(projectCard);

    // Create a new project title header
    let projectTitle = document.createElement("div");
    projectTitle.classList.add("card-header", "projectTitle");

    // Add the title to the project title
    let title = document.createTextNode(projectName);
    projectTitle.appendChild(title);

    // Add settings button to project title
    let settingsButton = document.createElement("button");
    settingsButton.classList.add("btn", "settingsButton");
    settingsButton.innerHTML = "<i class='fas fa-cog'></i>";
    settingsButton.onclick = function () {
        openSettings(projectID);
    }
    projectTitle.appendChild(settingsButton);

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
    addTask.classList.add("btn", "btn-success", "noprint", "addTask");
    addTask.innerHTML = "<i class='fas fa-plus'></i>";
    addTask.onclick = function () {
        addTaskToProject(projectID);
    }
    projectBody.appendChild(addTask);


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

    let editDescription = document.createElement("button");
    editDescription.classList.add("btn", "editDescription");
    editDescription.innerHTML = "<i class='fas fa-pencil-alt' style='color: #585d65;'></i>";
    editDescription.onclick = function () {
        editProjectDescription(projectID);
    }
    projectDescriptionHolder.appendChild(editDescription);

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

    let taskTitle = document.createElement("div");
    taskTitle.classList.add("card-header", "taskTitle");
    taskTitle.innerHTML = task.Task_type;
    taskCard.appendChild(taskTitle);

    let taskBody = document.createElement("div");
    taskBody.classList.add("card-body", "taskBody");
    taskBody.innerHTML = task.Task_data;
    taskCard.appendChild(taskBody);

    return taskCard;
}