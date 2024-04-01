

// Main settings modal 

async function openSettings(proj_id) {

    // Fetch the project settings
    var projectSettings = await fetchProject(proj_id);

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

    // Create archive button
    var archiveButton = document.getElementById("archiveButton");
    archiveButton.onclick = function () {
        archiveProject(proj_id);
    }

    $('#projectSettingsModal').modal('show');

}
