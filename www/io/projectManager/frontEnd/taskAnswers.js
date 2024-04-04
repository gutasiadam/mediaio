
// Open task answers


async function openTaskAnswers(taskId) {

    // Get the task answers
    var uData = await userTaskData(taskId);

    $('#taskAnswersModal').modal('show');
}