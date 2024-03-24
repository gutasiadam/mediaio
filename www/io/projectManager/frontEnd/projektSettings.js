

// Main settings modal 

async function openSettings(proj_id) {

    // Fetch the project settings
    var projectSettings = await fetchProjectSettings(proj_id);

    // Parse the settings
    projectSettings = JSON.parse(projectSettings);

    // Set the project name
    var projectName = projectSettings.Name;
    document.getElementById("projectName").value = projectName;

    // Load project members
    var projectMembers = projectSettings.Members;

    var membersList = await getUsers();
    membersList = JSON.parse(membersList);

    var members = document.getElementById("projectMembersSelect");
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

        if (projectMembers.includes(member.idUsers)) {
            option.classList.add("selectedMember");
        }

        members.appendChild(option);
    }


    // Load deadline
    if (projectSettings.Deadline == null) {
        document.getElementById("projectDate").value = "";
        document.getElementById("projectTime").value = "";
    } else {
        var deadline = projectSettings.Deadline;
        var parts = deadline.split(' ');

        // Stripping seconds from the time
        parts[1] = parts[1].split(':').slice(0, 2).join(':');

        document.getElementById("projectDate").value = parts[0];
        document.getElementById("projectTime").value = parts[1];
    }

    // Load project visibility
    var projectVisibility = projectSettings.Visibility_group;
    document.getElementById("projectVisibility").value = projectVisibility;

    // Create save button
    var saveButton = document.getElementById("saveButton");
    saveButton.onclick = function () {
        saveProjectSettings(proj_id);
    }

    // Create delete button
    var deleteText = document.getElementById("deleteText");
    deleteText.placeholder = projectName;

    var deleteButton = document.getElementById("deleteButton");
    deleteButton.onclick = function () {
        deleteProject(proj_id);
    }

    $('#projectSettingsModal').modal('show');

}

async function editProjectDescription(proj_id) {

    // Fetch the project settings
    var projectSettings = await fetchProjectSettings(proj_id);

    // Parse the settings
    projectSettings = JSON.parse(projectSettings);

    // Set the project description
    var projectDescription = projectSettings.Description;
    document.getElementById("projectDescription").value = projectDescription;

    // Create save button
    var saveButton = document.getElementById("saveDescButton");
    saveButton.onclick = function () {
        saveProjectDescription(proj_id);
    }

    $('#projectDescModal').modal('show');
}


// Save project settings

async function saveProjectSettings(proj_id) {

    // Get the project name
    var projectName = document.getElementById("projectName").value;

    // Get the project deadline
    var projectDate = document.getElementById("projectDate").value;
    var projectTime = document.getElementById("projectTime").value;

    // Getting added users
    var members = document.getElementsByClassName("selectedMember");
    var projectMembers = [];
    for (let i = 0; i < members.length; i++) {
        projectMembers.push(members[i].id);
    }

    // Combine the date and time
    let projectDeadline = "NULL";

    if (projectDate && projectTime) {
        projectDeadline = projectDate + " " + projectTime;
    }

    // Get the project visibility
    var projectVisibility = document.getElementById("projectVisibility").value;

    // Save the project settings
    var response = await saveProjectSettingsToDB(proj_id, projectName, projectDeadline, projectVisibility, projectMembers);

    if (response == 500) {
        console.error("Error: 500");
        return;
    }

    // Close the modal
    $('#projectSettingsModal').modal('hide');

    // Reload the page
    location.reload();

}

async function saveProjectDescription(proj_id) {

    // Get the project description
    var projectDescription = document.getElementById("projectDescription").value;

    // Save the project description
    var response = await saveProjectDescriptionToDB(proj_id, projectDescription);

    if (response == 500) {
        console.error("Error: 500");
        return;
    }

    // Close the modal
    $('#projectDescModal').modal('hide');

    // Reload the page
    location.reload();

}



// Delete project

async function deleteProject(proj_id) {

    // Get the project name
    var projectName = document.getElementById("deleteText").placeholder;

    var typedName = document.getElementById("deleteText").value;

    if (projectName == typedName) {

        // Delete the project
        await deleteProjectFromDB(proj_id);

        // Close the modal
        $('#projectSettingsModal').modal('hide');

        // Reload the page
        location.reload();

    } else {
        console.error("Error: Project name does not match");
        return;
    }

}