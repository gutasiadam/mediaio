
function changeProjectSettingsButton(projectID) {
    let settingsButton = document.createElement("button");
    settingsButton.classList.add("btn", "settingsButton");
    settingsButton.innerHTML = "<i class='fas fa-cog'></i>";
    settingsButton.onclick = function () {
        openSettings(projectID);
    }
    return settingsButton;
}



function editDescriptionButton(projectID) {
    let editDescription = document.createElement("button");
    editDescription.classList.add("btn", "editDescription");
    editDescription.innerHTML = "<i class='fas fa-pencil-alt' style='color: #585d65;'></i>";
    editDescription.onclick = function () {
        editProjectDescription(projectID);
    }
    return editDescription;
}


function removeMemberFromProjectButton(projectID, memberID) {
    let removeMemberButton = document.createElement("button");
    removeMemberButton.classList.add("btn", "removeMemberButton");
    removeMemberButton.innerHTML = "<i class='fas fa-user-minus'></i>";
    removeMemberButton.style.color = "#ff0000";
    removeMemberButton.onclick = function () {
        removeMemberFromProject(projectID, memberID);
    }
    return removeMemberButton;
}

function removeMemberFromProject(projectID, memberID) {
    console.log("Removing member from project");
    console.log("Project ID:", projectID);
    console.log("Member ID:", memberID);

    $.ajax({
        type: "POST",
        url: "../projectManager.php",
        data: { mode: "removeMemberFromProject", projectId: projectID, userId: memberID },
        success: function (response) {
            console.log(response);
            if (response == 500) {
                window.location.href = "index.php?serverError";
            }
            if (response == 200) {
                location.reload();
            }
        }
    });
}

async function editProjectMembersButton(projectID) {
    let addMember = document.createElement("button");
    addMember.classList.add("btn", "btn-secondary", "btn-lg");
    addMember.innerHTML = "<i class='far fa-edit'></i>";
    addMember.style.margin = "auto auto";
    addMember.onclick = function () {
        editProjectMembers(projectID);
    }
    return addMember;
}


async function editProjectMembers(projectID) {
    console.log("Adding new member to project: " + projectID);

    $('#addMemberModal').modal('show');

    // Load project members
    var projectMembers = await fetchProjectMembers(projectID);
    projectMembers = JSON.parse(projectMembers);
    projectMembers = projectMembers.map(member => member.UserID);

    var membersList = await getUsers();
    membersList = JSON.parse(membersList);

    var members = document.getElementById("projectMembersSelect");
    members.innerHTML = "";
    for (let i = 0; i < membersList.length; i++) {
        var member = membersList[i];

        var option = document.createElement("div");
        option.classList.add("availableMember");
        option.style.cursor = "pointer";
        option.id = member.idUsers;
        option.innerHTML = member.lastName + " " + member.firstName;
        option.onclick = function () {
            if (this.classList.contains("selectedMember")) {
                this.classList.remove("selectedMember");
            }
            else {
                this.classList.add("selectedMember");
            }
        }

        if (projectMembers && projectMembers.includes(member.idUsers.toString())) {
            option.classList.add("selectedMember");
        }

        members.appendChild(option);
    }

    // Create save button
    var saveButton = document.getElementById("saveProjectMembers");
    saveButton.onclick = function () {
        saveProjectMemberSettings(projectID);
    }
}


async function saveProjectMemberSettings(projectID) {
    var members = document.getElementsByClassName("selectedMember");
    var projectMembers = [];
    for (let i = 0; i < members.length; i++) {
        projectMembers.push(members[i].id);
    }

    console.log(projectMembers);
    var response = await saveProjectMembersToDB(projectID, projectMembers);

    if (response == 500) {
        console.error("Error: 500");
        return;
    } else if (response == 200) {
        location.reload();
    }

    // Close the modal
    $('#addMemberModal').modal('hide');

}