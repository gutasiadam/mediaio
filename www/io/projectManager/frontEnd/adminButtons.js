
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