

function showFormAnswers(id) {

    //Set button class
    setButtonClass("singleAnswer");

    //Set doboz max-width
    var doboz = document.getElementById("doboz");
    doboz.style.maxWidth = "800px";

    //Set table invisible
    var table = document.getElementById("answersTable");
    table.style.display = "none";

    console.log("Showing form answers: " + id);

    var UserInput;
    var formElements;

    for (var i = 0; i < formAnswers.length; i++) {
        if (formAnswers[i].ID == id) {
            UserInput = JSON.parse(formAnswers[i].UserAnswers);
            formElements = JSON.parse(formAnswers[i].FormState);
        }
    }


    var formContainer = document.getElementById("form-body");
    formContainer.innerHTML = "";
    //Load form elements
    for (var pos = 0; pos < formElements.length; pos++) {
        var element = formElements[pos];


        var elementType = element.type;
        var elementId = element.id;
        var elementPlace = element.place;
        var elementSettings = element.settings;
        var elementAnswer;

        for (var i = 0; i < UserInput.length; i++) {
            if (UserInput[i].id == (elementType + "-" + elementId)) {
                elementAnswer = UserInput[i].value;
            }
        }

        formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, "answer", elementAnswer));

    }

    //Set form visible
    formContainer.style.display = "block";
}


function showTable() {

    setButtonClass("table");

    //Set doboz max-width
    var doboz = document.getElementById("doboz");
    doboz.style.maxWidth = "1200px";

    //Empty table
    var headerHolder = document.getElementById("headerHolder");
    headerHolder.innerHTML = "";

    var answerHolder = document.getElementById("answerHolder");
    answerHolder.innerHTML = "";

    // Set form invisible
    var formContainer = document.getElementById("form-body");
    formContainer.style.display = "none";

    // Generate table header
    var idTh = document.createElement("th");
    idTh.innerHTML = "ID";
    idTh.scope = "col";
    headerHolder.appendChild(idTh);

    console.log(currentForm);

    for (var i = 0; i < currentForm.length; i++) {
        var th = document.createElement("th");

        //Getting question name
        var question = JSON.parse(currentForm[i].settings).question;

        th.innerHTML = question;
        th.scope = "col";
        headerHolder.appendChild(th);
    }

    // Generate table body

    function createRow(formAnswers, formElements) {
        var tr = document.createElement("tr");

        var idTd = document.createElement("td");
        idTd.innerHTML = formAnswers.ID;
        tr.appendChild(idTd);

        // Create cells
        for (var j = 0; j < formElements.length; j++) {
            var td = document.createElement("td");
            var elementAnswer = getElementAnswer(formElements[j], JSON.parse(formAnswers.UserAnswers));
            td.innerHTML = elementAnswer;
            tr.appendChild(td);
        }
        return tr;
    }

    function getElementAnswer(element, AnswerData) {
        var elementType = element.type;
        var elementId = element.id;
        var elementAnswer;

        for (var k = 0; k < AnswerData.length; k++) {
            if (AnswerData[k].id == (elementType + "-" + elementId)) {
                if (elementType == "checkbox" || elementType == "radio") {
                    //elementAnswer = getCheckedAnswer(AnswerData[k].value);
                    if (elementAnswer == undefined) {
                        elementAnswer = "<i>In development</i>";
                    }
                } else if (elementType == "scaleGrid") {
                    elementAnswer = getScaleGridAnswer(AnswerData[k].value);
                }
                else {
                    console.log(AnswerData[k].value);
                    elementAnswer = AnswerData[k].value;
                }
            }
        }
        return elementAnswer;
    }

    function getScaleGridAnswer(submission) {
        var answer = "";

        function getGrade(sub) {
            var grade = 0;
            for (var j = 0; j < sub.answers.length; j++) {
                if (sub.answers[j] == 1) {
                    grade = j + 1;
                }
            }
            return grade;
        }

        for (var i = 0; i < submission.length; i++) {
            answer += submission[i].label + ": " + getGrade(submission[i]) + "<br>";
        }
        return answer;
    }

    function getCheckedAnswer(value) {
        var answer = JSON.parse(value);
        console.log(answer);
        var elementAnswer;

        for (var l = 0; l < answer.length; l++) {
            var answerOption = answer[l].split(":")[0];
            var checked = Boolean(Number(answer[l].split(":")[1]));
            if (checked) {
                if (elementAnswer == undefined) {
                    elementAnswer = answerOption;
                } else {
                    elementAnswer = elementAnswer + ", " + answerOption;
                }
            }
        }
        return elementAnswer;
    }

    for (var i = 0; i < formAnswers.length; i++) {
        var tr = createRow(formAnswers[i], currentForm);
        answerHolder.appendChild(tr);
    }

    // Set table visible
    var table = document.getElementById("answersTable");
    table.style.display = "table";

}