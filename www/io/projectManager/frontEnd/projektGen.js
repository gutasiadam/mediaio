

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
    projectCard.draggable = true; // Make the projectCard draggable
    projectHolder.appendChild(projectCard);

    // Add event listeners for the drag events
    projectCard.addEventListener('dragstart', function (event) {
        event.dataTransfer.setData('text/plain', projectCard.id);
    });

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

    // Add event listeners for the dragover and drop events
    projectBody.addEventListener('dragover', function (event) {
        event.preventDefault(); // Prevent the default to allow drop
    });

    projectBody.addEventListener('drop', function (event) {
        event.preventDefault(); // Prevent the default action (open as link for some elements)

        // Get the id of the dragged projectCard from the drag data
        let id = event.dataTransfer.getData('text/plain');

        // Get the dragged projectCard
        let draggedProjectCard = document.getElementById(id);

        // Remove the dragged projectCard from its current parent node
        draggedProjectCard.parentNode.removeChild(draggedProjectCard);

        // Append the dragged projectCard to the projectBody
        projectBody.appendChild(draggedProjectCard);
    });


    // Create a new project description
    projectBody.appendChild(createDiscription(projectID, project.Description));

    // Generating the project tasks
    projectBody.appendChild(await generateTasks(projectID));

    let addTask = document.createElement("button");
    addTask.classList.add("btn", "btn-success", "dropdown-toggle", "addTask");
    projectBody.appendChild(addTask);

    // Creat ul dropdown
    let ul = document.createElement("ul");
    ul.classList.add("dropdown-menu");
    projectBody.appendChild(ul);

    // create li elements
    let text = document.createElement("li");
    text.classList.add("dropdown-item");
    text.innerHTML = "Add text";
    text.onclick = function () {
        addNewTask(projectID, "text");
    }
    ul.appendChild(text);

    let image = document.createElement("li");
    image.classList.add("dropdown-item");
    image.innerHTML = "Add image";
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
    taskCard.draggable = true; // Make the taskCard draggable

    // Add event listeners for the drag events
    taskCard.addEventListener('dragstart', function (event) {
        event.dataTransfer.setData('text/plain', taskCard.id);
    });

    if (task.Task_Title) {
        let taskTitle = document.createElement("div");
        taskTitle.classList.add("card-header", "taskTitle");
        taskTitle.innerHTML = task.Task_Title;
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