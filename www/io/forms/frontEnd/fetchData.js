async function FetchData(formId, formHash) {
    console.log("Fetching form data");

    return new Promise(async (resolve, reject) => {
        try {
            let response;
            if (formId != -1) {
                response = await $.ajax({
                    type: "POST",
                    url: "../formManager.php",
                    data: { mode: "getForm", id: formId }
                });
            } else {
                response = await $.ajax({
                    type: "POST",
                    url: "../formManager.php",
                    data: { mode: "getForm", formHash: formHash }
                });
            }

            if (response == 404) {
                window.location.href = "index.php?invalidID";
            }

            var form = JSON.parse(response);

            resolve(form);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function fetchAnswers(formId, formHash) {
    console.log("Fetching form answers");
    try {
        let data = { mode: "getFormAnswers" };
        if (formId != -1) {
            data.id = formId;
        } else {
            data.formHash = formHash;
        }

        let response = await $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: data
        });

        if (response == 404) {
            window.location.href = "index.php?invalidID";
        }

        let submission = JSON.parse(response);
        submission.forEach(item => formAnswers.push(item));

        let dropdown = document.getElementById("answers_dropdown");

        submission.forEach((item, i) => {
            let id = item.ID;

            let li = document.createElement("li");
            li.classList.add("dropdown-item");
            li.style.cursor = "pointer";

            li.onclick = function () {
                showFormAnswers(id);
            };

            li.innerHTML = `${i + 1}. válasz</a>`;
            dropdown.appendChild(li);
        });
    } catch (error) {
        console.error("Error:", error);
    }
}


async function loadPage(form, state) {

    const formElements = JSON.parse(form.Data);
    const formName = form.Name;
    const formStatus = JSON.parse(form.Status);
    const formAccess = JSON.parse(form.AccessRestrict);
    const formAnonim = form.Anonim;
    const formSingleAnswer = form.SingleAnswer;
    const formHash = form.LinkHash;
    const formId = form.Id;

    if (state == "fill" || state == "answers") {
        //Set form Name and header
        document.getElementById("form_name").innerHTML = formName;
        if (state == "fill") {
            document.getElementById("form_header").innerHTML = form.Header.replace(/\n/g, "<br>");
        }
    }
    if (state == "editor") {
        //Set form Name
        document.getElementById("form_name").innerHTML = formName + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>';
        document.getElementById("description").value = form.Header;

        //Set form state
        document.getElementById("formState").value = formStatus;

        //Set form access
        document.getElementById("accessRestrict").value = formAccess;

        if (form.AccessRestrict == 3) {
            showLink(formHash);
        } else {
            showLink(formHash, false);
        }

        //Set form settings
        if (formAnonim == 1) {
            document.querySelector('[data-setting="Anonim"]').checked = true;
        }
        if (formSingleAnswer == 1) {
            document.querySelector('[data-setting="SingleAnswer"]').checked = true;
        }
    }
    if (state == "success") {
        //Set form Name and header if form is closed
        document.getElementById("form_name").innerHTML = "Sikeres leadás!";
        document.getElementById("form_header").innerHTML = "Köszönjük, hogy kitöltötte a kérdőívet!";
    }
    let formContainer = document.getElementById("form-body");



    // Set background
    const style = document.createElement('style');
    style.innerHTML = `
    body::before {
        content: "";
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url(../forms/backgrounds/${form.Background});
        background-size: cover;
        background-position: center;
        z-index: -1;
    }`;
    document.head.appendChild(style);

    //Create form elements
    if (state == "fill" || state == "editor") {
        if (formElements == null) {
            return;
        }
        if (state == "editor") {
            formContainer = document.getElementById("editorZone");
        }
        // Create a map of form elements by their place
        let formElementsMap = new Map();
        formElements.forEach((element, index) => {
            formElementsMap.set(element.place, element);
        });

        // Load form elements
        for (let pos = 1; pos <= formElements.length; pos++) {
            let element = formElementsMap.get(pos);

            let { type: elementType, id: elementId, place: elementPlace, settings: elementSettings } = element;

            // Add settings, where possible
            // console.log(`Id: ${elementId} Place: ${elementPlace} Type: ${elementType} Settings: ${elementSettings}`);
            formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, state));
        }

        if (state == "fill") {
            //Add submit button
            const submit = document.createElement("button");
            submit.classList.add("btn", "btn-lg", "btn-success");
            submit.type = "submit";
            submit.innerHTML = "Leadás";
            formContainer.appendChild(submit);
        }
    }
    if (state == "answers") {
        return formElements;
    }
}
