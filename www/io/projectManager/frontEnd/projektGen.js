

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
    text.innerHTML = "<i class='fas fa-paragraph fa-sm'></i> Szöveg";
    text.style.cursor = "pointer";
    text.onclick = function () {
        addNewTask(projectID, "text");
    }
    ul.appendChild(text);

    let image = document.createElement("li");
    image.classList.add("dropdown-item");
    image.innerHTML = "<i class='far fa-image fa-sm'></i> Kép";
    image.style.cursor = "pointer";
    image.onclick = function () {
        addNewTask(projectID, "image");
    }
    ul.appendChild(image);

    let checklist = document.createElement("li");
    checklist.classList.add("dropdown-item");
    checklist.innerHTML = "<i class='fas fa-list fa-sm'></i> Lista";
    checklist.style.cursor = "pointer";
    checklist.onclick = function () {
        addNewTask(projectID, "checklist");
    }
    ul.appendChild(checklist);

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
        } else if (diff < 86400000) {   // 24 hours
            projectCard.classList.add("soon");
        } else {
            projectCard.classList.add("future");
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

