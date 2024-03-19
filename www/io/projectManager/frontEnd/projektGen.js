

function generateProjects(project) {

    for (let i = 0; i < project.length; i++) {
        generateProjectBody(project[i]);
    }
}


// Function to generate a trello like project

function generateProjectBody(project) {
    console.log("Generating project body");
    console.log(project);
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

    return projectCard;
}