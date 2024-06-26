// Save userInput to cookie

function saveUserInputToCookie() {
    console.log("Saving user input to cookie");
    var form = document.getElementById("form-body"); //Get form container

    var elements = form.getElementsByClassName("question"); //Get all form elements

    var answers = [];
    for (var i = 0; i < elements.length; i++) {
        //Loop through all form elements
        var element = elements[i];

        var elementType = element.id.split("-")[0];
        var inputs = element.getElementsByClassName("userInput");

        var value = [];
        //Get value of form element
        if (elementType == "radio" || elementType == "checkbox") {
            for (var j = 0; j < inputs.length; j++) {
                if (inputs[j].checked) {
                    value.push(1);
                } else {
                    value.push(0);
                }
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
    }

    answers = JSON.stringify(answers);

    //Set cookie expire date to 1 day
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();

    document.cookie = "userInput=" + answers + ";" + expires + ";path=/; ";
}

function reloadUserInput() {
    var userInput = getUserInputFromCookie();
    if (userInput != "") {
        userInput = JSON.parse(userInput);
        var form = document.getElementById("form-body"); //Get form container

        var elements = form.getElementsByClassName("question"); //Get all form elements

        for (var i = 0; i < elements.length; i++) {
            //Loop through all form elements
            var element = elements[i];

            var elementType = element.id.split("-")[0];
            var inputs = element.getElementsByClassName("userInput");

            var value = userInput[i].value;
            //Get value of form element
            if (elementType == "radio" || elementType == "checkbox") {
                /* for (var j = 0; j < inputs.length; j++) {
                    if (value[j] == 1) {
                        inputs[j].checked = true;
                    } else {
                        inputs[j].checked = false;                          //TODO: Fix this!!!!!!!!!!!!!!!!!
                    }
                } */
            }
            else if (elementType == "scaleGrid") {
                /* var scaleGrid = element.getElementsByClassName("grid-holder")[0];
                var rows = scaleGrid.getElementsByClassName("grid-row");
                for (var j = 0; j < rows.length; j++) {
                    var inputs = rows[j].getElementsByClassName("userInput");
                    var rowInputs = value[j].answers;
                    for (var k = 0; k < inputs.length; k++) {
                        if (rowInputs[k] == 1) {
                            inputs[k].checked = true;
                        } else {
                            inputs[k].checked = false;
                        }
                    }
                } */
            }
            else {
                inputs[0].value = value.replace(/"/g, '');
            }
        }
    }
}

//Get userInput from cookie
function getUserInputFromCookie() {
    var name = "userInput=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            var userInput = c.substring(name.length, c.length);
            return userInput;
        }
    }
    return null;
}

//Clear userInput cookie

function clearUserCookie() {
    document.cookie = "userInput=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

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
                if (inputs[j].checked) {
                    value.push(1);
                } else {
                    value.push(0);
                }
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

        var answer = {
            id: element.id,
            value: value
        }
        answers.push(answer);
        console.log(answer);
    }

    //Send answers to server
    answers = JSON.stringify(answers);

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
    formJson = JSON.stringify(formJson);
    $.ajax({
        type: "POST",
        url: "../formManager.php",
        data: { mode: "submitAnswer", uid: uid, userIp: userIp, id: formId, formHash: formHash, answers: answers, form: formJson },
        success: function (data) {
            console.log(data);
            if (data == 500) {
                alert("Nem megengedett karakterek a válaszban!");
            } else if (data == 200) {
                clearUserCookie();
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