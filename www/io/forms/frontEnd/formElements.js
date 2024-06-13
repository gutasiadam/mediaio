// List which contains the form elements
let formElements = [];

class FormElement {
    id;
    type = '';
    question = '';
    details = {};
    required = false;
    options = [];
    answer = {};

    // Methods:
    constructor(id, type, question, details, required, options) {
        this.id = `${type}-${id}`;
        this.type = type;
        this.question = question;
        this.details = details;
        this.required = required;
        this.options = options;
    }

    createElement(container, state) {

        // Main div containing the form element
        const mainDIV = document.createElement('div');
        mainDIV.id = this.id;
        mainDIV.classList.add("mb-3", "form-member");
        state == "editor" ? mainDIV.classList.add("editor") : mainDIV.classList.add("user");
        mainDIV.setAttribute('data-type', this.type);
        mainDIV.setAttribute('data-required', this.required);

        // Create a div for the ui elements
        const uiDiv = document.createElement('div');
        uiDiv.classList.add("form-control");
        mainDIV.appendChild(uiDiv);

        // Question input
        const questionInput = document.createElement('input');
        questionInput.type = 'text';
        questionInput.classList.add("form-control", "editorLabel");
        questionInput.placeholder = 'Kérdés';
        questionInput.value = this.question ? this.question : '';
        uiDiv.appendChild(questionInput);

        // Based on the type of the form element, we generate the specific editor
        switch (this.type) {
            case "email":
                uiDiv.appendChild(this.generateEmail(state));
                break;
            case "shortText":
                uiDiv.appendChild(this.generateShortText(state));
                break;
            case "longText":
                uiDiv.appendChild(this.generateLongText(state));
                break;
            case "time":
            case "date":
                uiDiv.appendChild(this.generateDate(state));
                break;
            case "radio":
            case "checkbox":
                uiDiv.appendChild(this.generateChoice(state));
                break;

            case "dropdown":
                //uidiv.appendChild(generateDropdown(id, settings, extraOptions, state, answer));
                break;

            case "scaleGrid":
                //uidiv.appendChild(generateScaleGrid(id, settings, extraOptions, state, answer));
                break;

            case "fileUpload":
                uiDiv.appendChild(this.generateFileUpload(state));
                break;
        }
        if (state == "editor") {
            uiDiv.appendChild(this.isRequiredEditor());
            // Drag and drop functionality and delete button
            this.addEditorFunctionality(mainDIV);
        }
        // Add the form element to the container
        container.appendChild(mainDIV);
    }

    // Editor elements
    isRequiredEditor() {
        //Add switch for required
        const switchdiv = document.createElement("div");
        switchdiv.classList.add("form-check", "form-switch");

        //Add switch
        const input = document.createElement("input");
        input.type = "checkbox";
        input.classList.add("form-check-input");
        input.id = `isRequired - ${this.id}`;
        input.checked = this.required;
        switchdiv.appendChild(input);

        //Add label for switch
        const label = document.createElement("label");
        label.classList.add("form-check-label");
        label.for = `isRequired - ${this.id}}`;
        label.innerHTML = "Kötelező";
        switchdiv.appendChild(label);

        return switchdiv;
    }

    addEditorFunctionality(editor) {
        // Holder div
        const holderDiv = document.createElement('div');
        holderDiv.classList.add("nav-bar");
        editor.appendChild(holderDiv);

        // Add delete button
        const deleteButton = document.createElement('button');
        deleteButton.classList.add("btn", "btn-close", "btn-sm", "deleteButton");
        deleteButton.onclick = function () {
            formElements = formElements.filter(element => element.id !== editor.id);
            everythingSaved = false;
            editor.remove();
        };
        holderDiv.appendChild(deleteButton);

        // Add drag and drop handle to the editor
        const dragHandle = document.createElement('span');
        dragHandle.classList.add("dragHandle");
        dragHandle.innerHTML = '<i class="fas fa-grip-vertical"></i>';
        holderDiv.appendChild(dragHandle);
    }

    generateEmail(state) {
        const emailInput = document.createElement('input');
        emailInput.type = 'email';
        emailInput.classList.add("form-control", "mb-3");

        emailInput.placeholder = 'Email cím';

        if (state == "editor") {
            emailInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            emailInput.classList.add("userInput");
            if (this.required) {
                emailInput.required = true;
            }
            if (state == "answer") {
                emailInput.disabled = true;
                emailInput.value = this.answer;
            }
        }
        return emailInput;
    }

    generateShortText(state) {
        const shortTextInput = document.createElement('input');
        shortTextInput.type = 'text';
        shortTextInput.classList.add("form-control", "mb-3");
        shortTextInput.placeholder = 'Rövid szöveg';

        if (state == "editor") {
            shortTextInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            shortTextInput.classList.add("userInput");
            if (this.required) {
                shortTextInput.required = true;
            }
            if (state == "answer") {
                shortTextInput.disabled = true;
                shortTextInput.value = this.answer;
            }
        }
        return shortTextInput;
    }

    generateLongText(state) {
        const longTextInput = document.createElement('textarea');
        longTextInput.classList.add("form-control", "mb-3");
        longTextInput.placeholder = 'Hosszú szöveg';
        longTextInput.disabled = true;

        if (state == "editor") {
            longTextInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            longTextInput.classList.add("userInput");
            if (this.required) {
                longTextInput.required = true;
            }
            if (state == "answer") {
                longTextInput.disabled = true;
                longTextInput.value = this.answer;
            }
        }
        return longTextInput;
    }

    generateDate(state) {
        const dateInput = document.createElement('input');
        dateInput.type = this.type;
        dateInput.classList.add("form-control", "mb-3");
        if (state == "editor") {
            dateInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            dateInput.classList.add("userInput");
            if (this.required) {
                dateInput.required = true;
            }
            if (state == "answer") {
                dateInput.disabled = true;
                dateInput.value = answer;
            }
        }
        return dateInput;
    }

    generateChoice(state) {
        const holderDiv = document.createElement('div');
        holderDiv.classList.add("mb-3", `${this.type}-holder`);

        if (this.details?.length == 0) {
            this.details = ["", ""];
        }
        this.details?.forEach((option, index) => {
            holderDiv.appendChild(this.addNewChoiceOption(option, index));
        });

        if (state == "editor") {
            const addRadio = document.createElement("button");
            addRadio.classList.add("btn", "btn-success", "btn-sm");
            addRadio.innerHTML = "+";
            addRadio.onclick = () => {
                // Create the new choice option element
                var newChoiceOption = this.addNewChoiceOption("", holderDiv.children.length - 1);

                // Insert the new choice option before the last child of holderDiv (the "Add" button)
                holderDiv.insertBefore(newChoiceOption, holderDiv.lastChild);
            };
            holderDiv.appendChild(addRadio);
        }

        return holderDiv;
    }

    addNewChoiceOption(option, index) {
        // Create div for checkbox or radio  
        const div = document.createElement("div");
        div.classList.add("input-group", "mb-2");
        div.setAttribute('data-option', index);

        const CheckHolderDiv = document.createElement("div");
        CheckHolderDiv.classList.add("input-group-text");
        CheckHolderDiv.innerHTML = `<input class="form-check-input" type="${this.type === 'radio' ? 'radio' : 'checkbox'}" id="${this.id}-option-${index}" data-name="${option}" disabled>`;
        div.appendChild(CheckHolderDiv);

        const label = document.createElement("input");
        label.type = "text";
        label.classList.add("form-control", "details-text");
        label.style.marginBottom = "0";
        label.placeholder = "Opció";
        label.value = option;
        div.appendChild(label);

        const deleteHolder = document.createElement("div");
        deleteHolder.classList.add("input-group-text");

        // Create delete button
        const deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-close", "btn-sm");
        deleteButton.onclick = function () {
            div.remove();
        };
        deleteHolder.appendChild(deleteButton);
        div.appendChild(deleteHolder);
        return div;
    }


    // File upload
    generateFileUpload(state) {
        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.classList.add("form-control");
        fileInput.disabled = true;

        if (state == "editor") {
            fileInput.disabled = true;
        } else if (state == "fill") {
            fileInput.classList.add("userInput");
            if (this.required) {
                fileInput.required = true;
            }
        }
        return fileInput;
    }

    // Getters
    getQuestion() {
        return document.getElementById(this.id).querySelector('input[type="text"]').value;
    }

    getRequired() {
        return document.getElementById(`isRequired - ${this.id}`).checked;
    }

    getDetails() {
        if (this.type === 'radio' || this.type === 'checkbox') {
            const details = [];
            document.querySelectorAll(`#${this.id} .details-text`).forEach(input => {
                details.push(input.value);
            });
            return details;
        } else {
            return "";
        }
    }
}


async function loadPage(form, state) {

    const formData = JSON.parse(form.Data);
    const formName = form.Name;
    const formStatus = form.Status;
    const formAccess = form.AccessRestrict;
    const formAnonim = form.Anonim;
    const formSingleAnswer = form.SingleAnswer;
    const formHash = form.LinkHash;


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
    if (state == "editor") {
        formContainer = document.getElementById("editorZone");
    }


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

    console.log(formData);

    formData?.forEach((element, index) => {
        let { type: type, id: id, question: question, details: details, required: required, options: options } = element;

        // Create a new form element
        let formElement = new FormElement(id, type, question, details, required, options);
        formElement.createElement(formContainer, state);
        formElements.push(formElement);
    });


    //Create form elements
    if (state == "fill") {
        //Add submit button
        const submit = document.createElement("button");
        submit.classList.add("btn", "btn-lg", "btn-success");
        submit.type = "submit";
        submit.innerHTML = "Leadás";
        formContainer.appendChild(submit);
    }
    if (state == "answers") {
        return formElements;
    }
}

// Serialize form elements --> Convert form elements to JSON
function serializeFormElements(formElements) {
    return formElements.map(element => {
        return {
            id: element.id.match(/\d+/) ? parseInt(element.id.match(/\d+/)[0], 10) : null, // Strip the id from the type
            type: element.type,
            question: element.getQuestion(),
            details: element.getDetails(),
            required: element.getRequired(),
            options: element.options,
            //answer: element.answer
        };
    });
}



async function saveFormElements(auto) {
    //Get all elements
    //const container = document.getElementById("editorZone");

    const formElementsToSave = serializeFormElements(formElements);
    const formElementsJson = JSON.stringify(formElementsToSave);

    const formHeader = document.getElementById("description").value;

    // Create a URLSearchParams object from the current window location
    const params = new URLSearchParams(window.location.search);

    // Get 'formId' parameter from the URL, default to null if not found
    const formId = params.has('formId') ? params.get('formId') : null;

    // Get 'form' parameter from the URL, default to null if not found
    const formHash = params.has('form') ? params.get('form') : null;

    let response = await $.ajax({
        type: "POST",
        url: "../formManager.php",
        data: {
            mode: "saveFormElements",
            formElements: formElementsJson,
            formId: formId,
            formHash: formHash,
            formHeader: formHeader,
        }
    });

    if (response == 200) {
        successToast(auto ? "Automatikus mentés!" : "Sikeres mentés!");
        everythingSaved = true;
    } else {
        errorToast("Sikertelen mentés!");
    }
}
