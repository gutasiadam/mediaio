

// Main settings modal 

async function openSettings(proj_id) {

    // Fetch the project settings
    var projectSettings = await fetchProjectSettings(proj_id);

    // Parse the settings
    projectSettings = JSON.parse(projectSettings);

    // Set the project name
    var projectName = projectSettings.Name;
    document.getElementById("projectName").value = projectName;


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

    // Combine the date and time
    let projectDeadline = "NULL";

    if (projectDate && projectTime) {
        projectDeadline = projectDate + " " + projectTime;
    } else if (projectDate) {
        projectDeadline = projectDate + " 23:59:59";
    }

    // Get the project visibility
    var projectVisibility = document.getElementById("projectVisibility").value;

    // Save the project settings
    var response = await saveProjectSettingsToDB(proj_id, projectName, projectDeadline, projectVisibility);

    if (response == 500) {
        console.error("Error: 500");
        return;
    } else if (response == 200) {
        location.reload();
    }

    // Close the modal
    $('#projectSettingsModal').modal('hide');


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
