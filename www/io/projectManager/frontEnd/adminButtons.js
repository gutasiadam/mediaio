
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