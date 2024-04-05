
// Open task answers


async function openTaskAnswers(taskId, projectId) {

    // Get the task answers
    var uData = JSON.parse(await userTaskData(taskId));

    // Get the task
    var task = JSON.parse(await fetchTask(null, taskId));

    // Fetch the task members
    var members = JSON.parse(await fetchTaskMembers(taskId, projectId));

    console.log(uData);
    console.log(task);
    console.log(members);

    // Get the working area
    var workingArea = document.getElementById('taskAnswerData');

    switch (task.Task_type) {
        case "text":
        case "image":
            var fillOutText = task.fillOutText;
        break;
        case "checkbox":
        case "radio":
            var fillOutText = task.fillOutText;
            var options = task.options;
        break;

    }

    $('#taskAnswersModal').modal('show');
}