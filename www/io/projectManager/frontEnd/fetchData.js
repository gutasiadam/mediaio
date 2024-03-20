async function fetchProjects() {
    console.log("Fetching projects");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "listProjects" }
            });


            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            var projects = JSON.parse(response);

            console.log(projects);

            resolve(projects);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function createNewProject() {
    console.log("Creating project");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "createNewProject" }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            location.reload();

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}


// FETCH PROJECT SETTINGS

async function fetchProjectSettings(proj_id) {
    console.info("Loading project settings...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "getProjectSettings", id: proj_id }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

// FETCH PROJECT TASKS

async function fetchTasks(proj_id) {
    console.info("Loading project tasks...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "getProjectTasks", id: proj_id }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

// SAVE PROJECT SETTINGS

async function saveProjectSettingsToDB(proj_id, projectName, projectDeadline, projectVisibility) {
    console.info("Saving project settings...");

    var settings = {
        "Name": projectName,
        "Members": "",
        "Deadline": projectDeadline,
        "Visibility_group": projectVisibility
    };

    console.log(settings);

    var settingsJson = JSON.stringify(settings);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "saveProjectSettings", id: proj_id, settings: settingsJson }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}


async function saveProjectDescriptionToDB(proj_id, projectDescription) {
    console.info("Saving project description...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "saveDescription", id: proj_id, description: projectDescription }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });

}


// DELETE PROJECT

async function deleteProjectFromDB(proj_id) {
    console.info("Deleting project...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "deleteProject", id: proj_id }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            location.reload();

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}