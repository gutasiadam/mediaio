

function generateProjects(project, mobile = false) {
    return new Promise(async (resolve, reject) => {
        try {
            if (mobile) {
                // Generating accordion
                let accordion = document.createElement("div");
                accordion.classList.add("accordion");
                accordion.id = "accordion";

                document.getElementsByClassName("container")[0].innerHTML = "";
                document.getElementsByClassName("container")[0].appendChild(accordion);

                for (let i = 0; i < project.length; i++) {
                    await generateMobileProjectBody(project[i], accordion);
                }
            } else {
                for (let i = 0; i < project.length; i++) {
                    await generateProjectBody(project[i]);
                }
            }
            resolve();
        } catch (error) {
            reject(error);
        }
    });
}


async function generateBigView(project) {
    let projectName = project.Name;
    let projectID = project.ID;

    // Create a new project holder card
    let projectCard = document.getElementById("projectHolder")
    projectCard.id = projectID;
    projectCard.style.flexDirection = "column";
    //colorCardBasedOnDeadline(projectCard, project.Deadline);


    // Create nav
    let nav = document.createElement("ul");
    nav.classList.add("nav", "nav-underline", "justify-content-center");

    // Add back button
    let backButton = document.createElement("button");
    backButton.classList.add("btn");
    backButton.innerHTML = '<i class="far fa-arrow-alt-circle-left fa-lg"></i>';
    backButton.onclick = function () {
        window.location.href = "index.php";
    }
    nav.appendChild(backButton);

    // Create li elements
    let tasks = document.createElement("li");
    tasks.classList.add("nav-item");
    tasks.innerHTML = `<button class='nav-link active' id='task-tab' data-bs-toggle='tab' data-bs-target='#task-tab-pane-${projectID}' type='button' role='tab' aria-controls='task-tab-pane' aria-selected='true'>${projectName}</button>`;
    nav.appendChild(tasks);

    let members = document.createElement("li");
    members.classList.add("nav-item");
    members.innerHTML = "<button class='nav-link' id='users-tab' data-bs-toggle='tab' data-bs-target='#users-tab-pane-" + projectID + "' type='button' role='tab' aria-controls='users-tab-pane' aria-selected='false'>Tagok</button>";
    nav.appendChild(members);

    if (project.Deadline) {
        let deadline = document.createElement("li");
        deadline.classList.add("nav-item");
        deadline.innerHTML = "<a class='nav-link disabled' aria-disabled='true'><b>" + await getDeadline(project.Deadline) + "</b></a>";
        nav.appendChild(deadline);
    }

    projectCard.appendChild(nav);

    // Add settings button to project title
    try {
        nav.appendChild(changeProjectSettingsButton(projectID));
    } catch (error) {
        ;
    }

    // Create a nav div
    let navDiv = document.createElement("div");
    navDiv.classList.add("tab-content");
    projectCard.appendChild(navDiv);

    // Create a new project body
    let projectBody = document.createElement("div");
    projectBody.classList.add("projectBody", "tab-pane", "fade", "show", "active");
    projectBody.id = "task-tab-pane-" + projectID;
    projectBody.role = "tabpanel";
    projectBody.ariaLabelledby = "task-tab";
    navDiv.appendChild(projectBody);


    // Create a new project description
    projectBody.appendChild(createDiscription(projectID, project.Description));

    // Generating the project tasks
    projectBody.appendChild(await generateTasks(projectID, project.canEdit));

    if (project.canEdit) {
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

        let file = document.createElement("li");
        file.classList.add("dropdown-item");
        file.innerHTML = "<i class='fas fa-file-alt'></i> Fájl";
        file.style.cursor = "pointer";
        file.onclick = function () {
            addNewTask(projectID, "file");
        }
        ul.appendChild(file);

        let checklist = document.createElement("li");
        checklist.classList.add("dropdown-item");
        checklist.innerHTML = "<i class='fas fa-list fa-sm'></i> Lista";
        checklist.style.cursor = "pointer";
        checklist.onclick = function () {
            addNewTask(projectID, "checklist");
        }
        ul.appendChild(checklist);

        let radio = document.createElement("li");
        radio.classList.add("dropdown-item");
        radio.innerHTML = "<i class='fas fa-dot-circle fa-sm'></i> Választós lista";
        radio.style.cursor = "pointer";
        radio.onclick = function () {
            addNewTask(projectID, "radio");
        }
        ul.appendChild(radio);
    }
    // Create Body for members
    let membersBody = document.createElement("div");
    membersBody.classList.add("card-body", "projectBody", "tab-pane", "fade");
    membersBody.id = "users-tab-pane-" + projectID;
    membersBody.role = "tabpanel";
    membersBody.ariaLabelledby = "users-tab";
    navDiv.appendChild(membersBody);


    try {
        // Add + button to add new members
        membersBody.appendChild(await editProjectMembersButton(projectID));
    } catch (error) {
        ;
    }
    // Adding members to the project body
    membersBody.appendChild(await generateMembers(projectID));

}
// Function to generate a mobile project list

async function generateMobileProjectBody(project, accordion) {
    // Generating item
    let item = document.createElement("div");
    item.classList.add("accordion-item");
    accordion.appendChild(item);

    // Generating header
    let header = document.createElement("h2");
    header.classList.add("accordion-header");
    header.id = "flush-heading" + project.ID;

    // Generating button
    let headerButton = document.createElement("button");
    headerButton.classList.add("accordion-button", "collapsed");
    headerButton.type = "button";
    headerButton.setAttribute("data-bs-toggle", "collapse");
    headerButton.setAttribute("data-bs-target", "#flush-collapse" + project.ID);
    headerButton.setAttribute("aria-expanded", "false");
    headerButton.innerHTML = project.Name;
    if (project.Deadline) {
        colorTaskCard(headerButton, project.Deadline);
    }
    header.appendChild(headerButton);

    item.appendChild(header);

    // Generating collapse
    let collapse = document.createElement("div");
    collapse.classList.add("accordion-collapse", "collapse");
    collapse.id = "flush-collapse" + project.ID;
    collapse.setAttribute("aria-labelledby", "flush-heading" + project.ID);
    collapse.setAttribute("data-bs-parent", "#accordionFlushExample");
    item.appendChild(collapse);

    // Generating body
    let body = document.createElement("div");
    body.classList.add("accordion-body");
    collapse.appendChild(body);

    // Generate tasks inside the project
    let description = document.createElement("p");
    description.innerHTML = project.Description;
    body.appendChild(description);

    // Project deadline
    if (project.Deadline) {
        let deadline = document.createElement("p");
        deadline.innerHTML = "<b>Határidő: </b>" + await getDeadline(project.Deadline);
        body.appendChild(deadline);
    }


    // Generating button to bigview project
    let button = document.createElement("button");
    button.classList.add("btn", "btn-primary");
    button.innerHTML = "Megtekintés";
    button.onclick = function () {
        window.location.href = "bigview.php?projectID=" + project.ID;
    }
    body.appendChild(button);

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
    tasks.innerHTML = `<button class="nav-link active" id="task-tab" data-bs-toggle="tab" data-bs-target="#task-tab-pane-${projectID}" type="button" role='tab' aria-controls='task-tab-pane' aria-selected='true'>
    <a data-bs-toggle="tooltip" data-bs-title="${project.Description}">${projectName}</a></button>`;
    nav.appendChild(tasks);

    let members = document.createElement("li");
    members.classList.add("nav-item");
    members.innerHTML = "<button class='nav-link' id='users-tab' data-bs-toggle='tab' data-bs-target='#users-tab-pane-" + projectID + "' type='button' role='tab' aria-controls='users-tab-pane' aria-selected='false'>Tagok</button>";
    nav.appendChild(members);

    projectHeader.appendChild(nav);

    let infoDiv = document.createElement("div");
    infoDiv.classList.add("infoDiv");

    if (project.Deadline) {
        var deadlineText = await getDeadline(project.Deadline);
        let deadline = document.createElement("span");
        deadline.classList.add("badge", "bg-secondary", "text-white", "ms-2");
        deadline.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="${project.Deadline.slice(0, -3)}">${deadlineText}</a>`;
        if (deadlineText == "Lejárt" || deadlineText.includes("perc") || deadlineText == "Épp most") {
            deadline.classList.add("bg-danger");
        } else if (deadlineText.includes("óra")) {
            deadline.classList.add("bg-warning", "text-dark");
        } else {
            deadline.classList.add("bg-success", "text-dark");
        }
        infoDiv.appendChild(deadline);
    }

    // Add settings button to project title
    try {
        infoDiv.appendChild(changeProjectSettingsButton(projectID));
    } catch (error) {
        ;
    }
    projectHeader.appendChild(infoDiv);

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
    //projectBody.appendChild(createDiscription(projectID, project.Description));

    // Generating the project tasks
    projectBody.appendChild(await generateTasks(projectID, project.canEdit));

    if (project.canEdit) {
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

        let file = document.createElement("li");
        file.classList.add("dropdown-item");
        file.innerHTML = "<i class='fas fa-file-alt'></i> Fájl";
        file.style.cursor = "pointer";
        file.onclick = function () {
            addNewTask(projectID, "file");
        }
        ul.appendChild(file);

        let checklist = document.createElement("li");
        checklist.classList.add("dropdown-item");
        checklist.innerHTML = "<i class='fas fa-list fa-sm'></i> Lista";
        checklist.style.cursor = "pointer";
        checklist.onclick = function () {
            addNewTask(projectID, "checklist");
        }
        ul.appendChild(checklist);

        let radio = document.createElement("li");
        radio.classList.add("dropdown-item");
        radio.innerHTML = "<i class='fas fa-dot-circle fa-sm'></i> Választós lista";
        radio.style.cursor = "pointer";
        radio.onclick = function () {
            addNewTask(projectID, "radio");
        }
        ul.appendChild(radio);
    }
    // Create Body for members
    let membersBody = document.createElement("div");
    membersBody.classList.add("card-body", "projectMembers", "tab-pane", "fade");
    membersBody.id = "users-tab-pane-" + projectID;
    membersBody.role = "tabpanel";
    membersBody.ariaLabelledby = "users-tab";
    CardBody.appendChild(membersBody);


    try {
        // Add + button to add new members
        membersBody.appendChild(await editProjectMembersButton(projectID));
    } catch (error) {
        ;
    }
    // Adding members to the project body
    membersBody.appendChild(await generateMembers(projectID));




    return projectCard;
}

// Archived projects

async function showArchivedProjects() {
    try {
        let archivedProjects = await fetchProjects(1);
        if (archivedProjects.length == 0) {
            alert("Nincs archivált projekt.");
            return;
        }

        // Create a new project holder card
        let projectHolder = document.getElementById("archivedTasks");
        projectHolder.innerHTML = "";

        for (let i = 0; i < archivedProjects.length; i++) {
            projectHolder.appendChild(await generateArchivedProjectBody(archivedProjects[i]));
        }

        $('#taskArchiveModal').modal('show');
    } catch (error) {
        console.error("Error showing archived projects:", error);
    }

}

async function generateArchivedProjectBody(project) {
    console.log(`Generating archived project body: ${project.Name}`);

    // Create a new project card
    let projectCard = document.createElement("div");
    projectCard.classList.add("card", "archivedProjectCard");
    projectCard.id = project.ID;

    // Create a new project body
    let projectBody = document.createElement("div");
    projectBody.classList.add("card-body", "d-flex", "justify-content-between", "align-items-center");
    projectBody.innerHTML = project.Name;
    projectCard.appendChild(projectBody);

    // Create button holder div
    let buttonHolder = document.createElement("div");
    projectBody.appendChild(buttonHolder);

    // Create restore button
    let restoreButton = document.createElement("button");
    restoreButton.classList.add("btn", "btn-success");
    restoreButton.innerHTML = `<i class="fas fa-undo"></i>`;
    restoreButton.style.marginRight = "10px";
    restoreButton.onclick = function () {
        restoreProject(project.ID);
    }
    buttonHolder.appendChild(restoreButton);

    // Create delete button
    let deleteButton = document.createElement("button");
    deleteButton.classList.add("btn", "btn-danger");
    deleteButton.innerHTML = `<i class="fas fa-trash-alt"></i>`;
    deleteButton.onclick = function () {
        deleteProject(project.ID);
    }
    buttonHolder.appendChild(deleteButton);


    return projectCard;
}



// Extra


function colorCardBasedOnDeadline(projectCard, deadline) {
    if (deadline) {
        let now = new Date();
        let projectDeadline = new Date(deadline);

        let diff = projectDeadline - now;

        if (diff < 0) {
            projectCard.classList.add("overdue");
        } else if (diff < (1000 * 60 * 60 * 48)) {   // 48 hours
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

    // Load project members
    fetchProjectMembers(proj_id)
        .then(async response => {
            projectMembers = JSON.parse(response);

            // Append each member to membersHolder
            projectMembers.forEach(async element => {
                membersHolder.appendChild(await createMember(element, proj_id));
            });
        })
        .catch(error => {
            console.error('Error fetching project members:', error);
            //serverErrorToast();
        });

    return membersHolder;
}

async function createMember(member, projectID) {
    let memberCard = document.createElement("div");
    memberCard.classList.add("card", "memberCard", "mb-2");
    if (member.isManager) {
        memberCard.classList.add("manager");
    }

    let memberBody = document.createElement("div");
    memberBody.classList.add("card-body", "memberBody");

    let memberName = document.createElement("span");
    memberName.classList.add("card-text", "memberName");
    memberName.innerHTML = member.lastName + " " + member.firstName;
    memberBody.appendChild(memberName);

    try {
        if (!member.isManager) {
            memberBody.appendChild(removeMemberFromProjectButton(projectID, member.UserID));
        }
        memberCard.oncontextmenu = async function (e) {
            if (this.classList.contains("manager")) {
                return;
            }
            e.preventDefault();
            if (await changeManager(projectID, member.UserID) == 200) {
                this.classList.add("manager");
                this.getElementsByClassName("memberBody")[0].querySelector(".removeMemberButton").remove();
            }
        }

        let touchCount = 0;
        //MOBILE DOUBLE TAP
        memberCard.addEventListener('touchend', function (event) {
            const memberCard = this;
            touchCount++;
            if (touchCount === 1) {
                setTimeout(async function () {
                    if (touchCount === 2) {
                        if (memberCard.classList.contains("manager")) {
                            return;
                        }
                        navigator.vibrate(100);
                        if (await changeManager(projectID, member.UserID) == 200) {
                            memberCard.classList.add("manager");
                            memberCard.getElementsByClassName("memberBody")[0].querySelector(".removeMemberButton").remove();
                        }
                    }
                    touchCount = 0;
                }, 300); // 300 milliseconds = 0.3 seconds
            }
        });
    } catch (error) {
        ;
    }

    memberCard.appendChild(memberBody);

    return memberCard;
}



async function getDeadline(deadline) {
    // If there is no deadline
    if (!deadline) {
        return "";
    }
    // Get the deadline

    let now = new Date();
    let projectDeadline = new Date(deadline);

    let diff = projectDeadline - now;
    var differenceInSeconds = Math.floor(diff / 1000);
    var differenceInMinutes = Math.floor(differenceInSeconds / 60);
    var differenceInHours = Math.floor(differenceInMinutes / 60);
    var differenceInDays = Math.floor(differenceInHours / 24);

    if (differenceInDays > 0) return differenceInDays + " nap";
    if (differenceInHours > 0) return differenceInHours + " óra";
    if (differenceInMinutes > 0) return differenceInMinutes + " perc";
    if (differenceInSeconds >= 0) return "Épp most";
    return "Lejárt";
}