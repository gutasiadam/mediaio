//Submit form
async function submitAnswer(formId, formHash, isAnonim) {
    var form = document.getElementById("form-body"); //Get form container

    var elements = form.getElementsByClassName("question"); //Get all form elements

    //console.log(elements);
    var answers = [];
    for (var i = 0; i < elements.length; i++) {
        //Loop through all form elements
        var element = elements[i];

        //Check if element is required
        var isRequired = element.getAttribute("data-required");
        if (isRequired == "true") {
            var inputs = element.getElementsByClassName("userInput");
            if (inputs[0].value == "") {
                alert("Kérlek töltsd ki az összes kötelező mezőt!");
                return;
            }
        }
        var elementType = element.id.split("-")[0];
        var inputs = element.getElementsByClassName("userInput");

        var value = [];
        //Get value of form element
        if (elementType == "radio" || elementType == "checkbox") {
            for (var j = 0; j < inputs.length; j++) {
                var checked = 0;
                if (inputs[j].checked) {
                    checked = 1
                }
                var input = inputs[j].getAttribute("data-name") + ":" + checked;
                value.push(input);
            }
        }
        else if (elementType == "scaleGrid") {
            var scaleGrid = element.getElementsByClassName("grid-holder")[0];
            var rows = scaleGrid.getElementsByClassName("grid-row");
            var rowAnswers = [];
            for (var j = 0; j < rows.length; j++) {
                var rowLabel = rows[j].getElementsByClassName("row-label")[0].innerText;

                var inputs = rows[j].getElementsByClassName("userInput");
                var rowInputs = [];
                for (var k = 0; k < inputs.length; k++) {
                    rowInputs.push(inputs[k].checked ? 1 : 0);
                }

                var rowAnswer = {
                    label: rowLabel,
                    answers: rowInputs
                }
                rowAnswers.push(rowAnswer);
            }
            value = rowAnswers;
        }
        else {
            value = inputs[0].value;
        }

        value = JSON.stringify(value);

        var answer = {
            id: element.id,
            value: value
        }
        answers.push(answer);
        console.log(answer);
    }

    //Send answers to server
    answers = JSON.stringify(answers).replace(/"/g, '\\"');

    //Set UID to 0 if user is not logged in
    var uid;
    var userIp;
    if (isAnonim == 0) {
        uid = await getUid();
        userIp = await getIp();
        //console.log("User: " + userIp);
    } else {
        console.log("Anonim");
        uid = 0;
        userIp = '0.0.0.0';
    }

    var formJson = await getFormJson(formId, formHash);
    formJson = JSON.stringify(formJson).replace(/"/g, '\\"');
    $.ajax({
        type: "POST",
        url: "../formManager.php",
        data: { mode: "submitAnswer", uid: uid, userIp: userIp, id: formId, formHash: formHash, answers: answers, form: formJson },
        success: function (data) {
            console.log(data);
            if (data == 500) {
                alert("Nem megengedett karakterek a válaszban!");
            } else if (data == 200) {
                if (formId != -1) {
                    window.location.href = "viewform.php?formId=" + formId + "&success";
                } else {
                    window.location.href = "viewform.php?form=" + formHash + "&success";
                }
            } else {
                alert("Sikertelen leadás");
            }
        }
    })
}

//Get form JSON

async function getFormJson(formId, formHash) {
    var formJson;
    if (formId != -1) {
        await $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "getForm", id: formId },
            success: function (data) {
                formJson = JSON.parse(data);
                formData = JSON.parse(formJson.Data);
            }
        });
    } else {
        await $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "getForm", formHash: formHash },
            success: function (data) {
                formJson = JSON.parse(data);
                formData = JSON.parse(formJson.Data);
            }
        });
    }
    return formData;
}