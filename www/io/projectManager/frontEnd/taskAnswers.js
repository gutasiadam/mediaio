
// Open task answers


async function openTaskAnswers(taskId, projectId) {

    // Get the task answers
    var uDataResponse = await userTaskData(taskId);
    var uData = Array.isArray(uDataResponse) ? JSON.parse(uDataResponse) : [JSON.parse(uDataResponse)];

    // Get the task
    var task = JSON.parse(await fetchTask(null, taskId));

    // Fetch the task members
    var members = JSON.parse(await fetchTaskMembers(taskId, projectId));

    // Get the working area
    var workingArea = document.getElementById('taskAnswerData');
    workingArea.innerHTML = '';

    switch (task.Task_type) {
        case "text":
        case "image":
            var fillOutText = task.fillOutText;

            // Create a div for answered users
            var answeredUsers = document.createElement('div');
            answeredUsers.classList.add('memberSelect', 'mb-3');
            answeredUsers.innerHTML = `<h5>${fillOutText}:</h5>`;
            workingArea.appendChild(answeredUsers);

            // Create a div for users that haven't answered
            var notAnsweredUsers = document.createElement('div');
            notAnsweredUsers.className = 'memberSelect';
            notAnsweredUsers.innerHTML = `<h5>Nem válaszolt:</h5>`;
            workingArea.appendChild(notAnsweredUsers);

            members.forEach(member => {
                if (member.assignedToTask == 0) return;
                var memberDiv = document.createElement('div');
                memberDiv.classList.add('availableMember');
                memberDiv.innerHTML = `${member.firstName} ${member.lastName}`;
                if (uData.some(item => item.UserId === member.UserId)) {
                    var userAnswer = uData.find(item => item.UserId === member.UserId);
                    memberDiv.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="Leadva: ${userAnswer.submissionTime}">${member.firstName} ${member.lastName}</a>`;
                    memberDiv.classList.add('selectedMember');
                    answeredUsers.appendChild(memberDiv);
                } else {
                    notAnsweredUsers.appendChild(memberDiv);
                }
            });

            break;
        case "checklist":
        case "radio":
            var options = JSON.parse(task.Task_data);
            let checkList = options.checklist;

            // Create a div for every option
            checkList.forEach(option => {
                var optionDiv = document.createElement('div');
                optionDiv.classList.add('memberSelect', 'mb-3');
                optionDiv.setAttribute('data-option', option.value);
                optionDiv.innerHTML = `<h5>${option.value}:</h5>`;
                workingArea.appendChild(optionDiv);
            });

            // Create a div for users that haven't answered
            var notAnsweredUsers = document.createElement('div');
            notAnsweredUsers.className = 'memberSelect';
            notAnsweredUsers.innerHTML = `<h5>Nem válaszolt:</h5>`;
            workingArea.appendChild(notAnsweredUsers);

            members.forEach(member => {
                if (member.assignedToTask == 0) return;
                var memberDiv = document.createElement('div');
                memberDiv.classList.add('availableMember');
                memberDiv.innerHTML = `${member.firstName} ${member.lastName}`;
                if (uData.some(item => item.UserId === member.UserId)) {
                    var userAnswer = uData.find(item => item.UserId === member.UserId);
                    memberDiv.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="Leadva: ${userAnswer.submissionTime}">${member.firstName} ${member.lastName}</a>`;
                    userAnswer = JSON.parse(userAnswer.Data);
                    userAnswer.forEach(answer => {
                        if (answer.checked == false) return;
                        var optionDiv = workingArea.querySelector(`div[data-option="${answer.value}"]`);
                        var clonedMemberDiv = memberDiv.cloneNode(true); // clone the memberDiv
                        optionDiv.appendChild(clonedMemberDiv);
                    });

                } else {
                    notAnsweredUsers.appendChild(memberDiv);
                }
            });

            break;

    }
    toolTipRender();

    $('#taskAnswersModal').modal('show');
}