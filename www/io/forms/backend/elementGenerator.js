
// MAIN
function generateElement(type, id, place, settings, state) {
    //Parse settings
    if (settings != "") {
        var questionSetting = JSON.parse(settings).question;
        var isRequired = JSON.parse(settings).required;
        var CheckOptions = JSON.parse(settings).options;
    }

    //Create div, which will contain the element
    var div = document.createElement("div");
    div.id = type + "-" + id;
    div.setAttribute('data-position', place);
    div.classList.add("mb-3");
    if (state == "fill") {
        if (isRequired) {
            div.setAttribute('data-required', "true");
        } else {
            div.setAttribute('data-required', "false");
        }
        div.classList.add("question");
    } else if (state == "editor") {
        div.classList.add("form-member");
    }

    //Add settings div
    if (state == "editor") {
        var uidiv = document.createElement("div");
        uidiv.classList.add("form-control");
        uidiv.id = "e-settings";
        div.appendChild(uidiv);
    } else if (state == "fill") {
        var uidiv = div;
    }

    //Add question label
    uidiv.appendChild(generateQuestionLabel(id, isRequired, questionSetting, state));

    console.log("Generating element: " + type);

    switch (type) {
        case "email":
            uidiv.appendChild(generateEmail(id, isRequired, state));
            break;

        case "shortText":
            uidiv.appendChild(generateShortText(id, isRequired, state));
            break;

        case "longText":
            uidiv.appendChild(generateLongText(id, isRequired, state));
            break;

        case "date":
            uidiv.appendChild(generateDate(id, isRequired, state));
            break;

        case "time":
            uidiv.appendChild(generateTime(id, isRequired, state));
            break;

        case "radio":
            uidiv.appendChild(generateCheckRadio(id, settings, CheckOptions, state, "radio"));
            break;

        case "checkbox":
            uidiv.appendChild(generateCheckRadio(id, settings, CheckOptions, state, "checkbox"));
            break;

        case "dropdown":
            uidiv.appendChild(generateDropdown(id, settings, CheckOptions, state));
            break;

        case "fileUpload":
            uidiv.appendChild(generateFileUpload(id, isRequired, state));
            break;
    }

    if (state == "editor") {
        //Add switch for required
        var switchdiv = document.createElement("div");
        switchdiv.classList.add("form-check", "form-switch");

        //Add switch
        var input = document.createElement("input");
        input.type = "checkbox";
        input.classList.add("form-check-input");
        input.id = "flexSwitchCheckDefault";
        input.checked = isRequired;
        switchdiv.appendChild(input);

        //Add label for switch
        var label = document.createElement("label");
        label.classList.add("form-check-label");
        label.for = "flexSwitchCheckDefault";
        label.innerHTML = "Kötelező";
        switchdiv.appendChild(label);

        uidiv.appendChild(switchdiv);


        //Add navigation buttons
        var navdiv = document.createElement("div");
        navdiv.classList.add("element-nav");
        div.appendChild(navdiv);

        //Move up button
        var moveUpButton = document.createElement("button");
        moveUpButton.classList.add("btn", "btn-secondary", "btn-sm");
        moveUpButton.innerHTML = "↑";
        moveUpButton.onclick = function () {
            moveUp(type, id);
        };
        navdiv.appendChild(moveUpButton);

        //Delete button
        var deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-close", "btn-sm");
        deleteButton.onclick = function () {
            removeElement(type, id);
        };
        navdiv.appendChild(deleteButton);

        //Move down button
        var moveDownButton = document.createElement("button");
        moveDownButton.classList.add("btn", "btn-secondary", "btn-sm");
        moveDownButton.innerHTML = "↓";
        moveDownButton.onclick = function () {
            moveDown(type, id);
        };
        navdiv.appendChild(moveDownButton);
    }

    return div;
}

function generateQuestionLabel(id, isRequired, questionSetting, state) {
    var label;
    if (state == "editor") {
        label = document.createElement("input");
        label.classList.add("form-control");
        label.classList.add("editorLabel");
        label.type = "text";
        label.value = questionSetting;
        label.placeholder = "Kérdés...";

    } else if (state == "fill") {
        label = document.createElement("label");
        if (isRequired) {
            label.innerHTML = questionSetting + "<span style='color: red;'> *</span>";
        } else {
            label.innerHTML = questionSetting;
        }
    }
    label.for = id;
    return label;
}

function generateEmail(id, isRequired, state) {
    var input = document.createElement("input");
    input.type = "email";
    input.classList.add("form-control");
    input.id = id;
    input.placeholder = "Email cím";

    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (isRequired) {
            input.required = true;
        }
    }
    return input;
}

function generateShortText(id, isRequired, state) {
    var input = document.createElement("input");
    input.type = "text";
    input.classList.add("form-control");
    input.id = id;
    input.placeholder = "Rövid szöveg";

    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (isRequired) {
            input.required = true;
        }
    }
    return input;
}

function generateLongText(id, isRequired, state) {
    var input = document.createElement("textarea");
    input.classList.add("form-control");
    input.id = id;
    input.placeholder = "Hosszú szöveg";

    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (isRequired) {
            input.required = true;
        }
    }
    return input;
}

function generateDate(id, isRequired, state) {
    var input = document.createElement("input");
    input.type = "date";
    input.classList.add("form-control");
    input.id = id;

    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (isRequired) {
            input.required = true;
        }
    }
    return input;
}

function generateTime(id, isRequired, state) {
    var input = document.createElement("input");
    input.type = "time";
    input.classList.add("form-control");
    input.id = id;

    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (isRequired) {
            input.required = true;
        }
    }
    return input;
}

function generateCheckRadio(id, settings, CheckOptions, state, type) {
    var radioHolder = document.createElement("div");
    radioHolder.classList.add(type + "-holder");

    if (settings == "") {
        radioHolder.append(listCheckOpt(type, id, "", 0, state));
    } else {
        for (var i = 0; i < CheckOptions.length; i++) {
            //Add radio buttons
            radioHolder.append(listCheckOpt(type, id, CheckOptions[i], i, state));
        }
    }


    if (state == "editor") {
        var addRadio = document.createElement("button");
        addRadio.classList.add("btn", "btn-success", "btn-sm");
        addRadio.innerHTML = "+";
        addRadio.onclick = function () {
            radioHolder.append(listCheckOpt(type, id, "", i++, state));
        };
        radioHolder.appendChild(addRadio);
    }
    return radioHolder;
}

function generateDropdown(id, settings, CheckOptions, state) {
    //Create dropdown holder
    var dropdownHolder = document.createElement("div");
    dropdownHolder.classList.add("dropdown-holder");

    //Create dropdown
    if (state == "fill") {
        var select = document.createElement("select");
        select.classList.add("form-select", "userInput");
        select.id = id;
        dropdownHolder.appendChild(select);
    }

    if (settings == "") {
        dropdownHolder.append(listDropdown("", 0, state));
    } else {
        for (var i = 0; i < CheckOptions.length; i++) {
            dropdownHolder.append(listDropdown(CheckOptions[i], i, state));
        }
    }

    if (state == "editor") {
        var addDropdown = document.createElement("button");
        addDropdown.classList.add("btn", "btn-success", "btn-sm");
        addDropdown.innerHTML = "+";
        addDropdown.onclick = function () {
            dropdownHolder.append(listDropdown("", i++, state));
        };
        dropdownHolder.appendChild(addDropdown);
    }

    return dropdownHolder;
}

function generateFileUpload(id, isRequired, state) {
    var input = document.createElement("input");
    input.type = "file";
    input.classList.add("form-control");
    input.id = id;
    input.disabled = true;

    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (isRequired) {
            input.required = true;
        }
    }
    return input;
}

//Generate dropdown options
function listDropdown(value, optionNum, state) {
    if (state == "editor") {
        //Create div for dropdown
        var div = document.createElement("div");
        div.classList.add("form-check");
        div.setAttribute('data-option', optionNum);

        //Create arrow
        var arrow = document.createElement("i");
        arrow.classList.add("fas", "fa-arrow-right", "fa-lg");
        div.appendChild(arrow);

        //Create option for dropdown
        var label = document.createElement("input");
        label.type = "text";
        label.classList.add("form-control");
        label.placeholder = "Opció";
        label.value = settings;
        div.appendChild(label);

        //Create delete button
        var deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-close", "btn-sm");
        deleteButton.onclick = function () {
            div.remove();
        };
        div.appendChild(deleteButton);
        return div;
    } else if (state == "fill") {
        var option = document.createElement("option");
        option.value = value;
        option.innerHTML = value;
        option.classList.add("userInput");
        return option;
    }
}

//Function to generate a checkbox or radio element
function listCheckOpt(type, id, settings, optionNum, state) {
    //Create div for checkbox or radio
    var div = document.createElement("div");
    div.classList.add("form-check");
    div.setAttribute('data-option', optionNum);

    //Create input for checkbox or radio
    var input = document.createElement("input");
    input.type = type;
    input.classList.add("form-check-input");
    if (state == "editor") {
        input.disabled = true;
    } else if (state == "fill") {
        input.classList.add("userInput");
        if (input.type == "radio") {
            input.name = "flexRadioDefault";
        }
    }
    input.id = id;
    div.appendChild(input);

    input.setAttribute('data-name', settings);

    //Create label for checkbox or radio
    if (state == "editor") {
        var label = document.createElement("input");
        label.type = "text";
        label.classList.add("form-control");
        label.placeholder = "Opció";
        label.value = settings;
        div.appendChild(label);
    }
    else if (state == "fill") {
        //Create label
        var label = document.createElement("label");
        label.classList.add("form-check-label");
        label.for = id;
        label.innerHTML = settings;
        div.appendChild(label);
    }

    if (state == "editor") {
        //Create delete button
        var deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-close", "btn-sm");
        deleteButton.onclick = function () {
            div.remove();
        };
        div.appendChild(deleteButton);
    }
    return div;
}