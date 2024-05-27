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
        this.id = id;
        this.type = type;
        this.question = question;
        this.details = details;
        this.required = required;
        this.options = options;
    }

    generateEditor(container) {

        // Main div containing the editor
        const editor = document.createElement('div');
        editor.id = this.id;
        editor.classList.add("mb-3", "form-member");
        editor.setAttribute('data-type', this.type);
        editor.setAttribute('data-required', this.required);

        // Question input
        const questionInput = document.createElement('input');
        questionInput.type = 'text';
        questionInput.classList.add("form-control", "editorLabel");
        questionInput.placeholder = 'Kérdés';
        questionInput.value = this.question ? this.question : '';
        editor.appendChild(questionInput);

        // Based on the type of the form element, we generate the specific editor
        switch (this.type) {
            case "email":
                editor.appendChild(this.generateEmailEditor());
                break;
            case "shortText":
                editor.appendChild(this.generateShortTextEditor());
                break;
            case "longText":
                editor.appendChild(this.generateLongTextEditor());
                break;
            case "date":
                editor.appendChild(this.generateDateEditor());
                break;
            case "time":
                editor.appendChild(this.generateTimeEditor());
                break;
            case "radio":
                editor.appendChild(this.generateChoiceEditor());
                break;
            case "checkbox":
                editor.appendChild(this.generateChoiceEditor());
                break;

            case "dropdown":
                //uidiv.appendChild(generateDropdown(id, settings, extraOptions, state, answer));
                break;

            case "scaleGrid":
                //uidiv.appendChild(generateScaleGrid(id, settings, extraOptions, state, answer));
                break;

            case "fileUpload":
                //uidiv.appendChild(generateFileUpload(id, isRequired, state, answer));
                break;
        }
        editor.appendChild(this.isRequiredEditor());

        // Add the editor to the container
        container.appendChild(editor);

        // Drag and drop functionality and delete button
        this.addEditorFunctionality(editor);
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
        input.id = "flexSwitchCheckDefault";
        input.checked = this.required;
        switchdiv.appendChild(input);

        //Add label for switch
        const label = document.createElement("label");
        label.classList.add("form-check-label");
        label.for = "flexSwitchCheckDefault";
        label.innerHTML = "Kötelező";
        switchdiv.appendChild(label);

        return switchdiv;
    }

    addEditorFunctionality(editor) {
        // Holder div
        const holderDiv = document.createElement('div');
        holderDiv.classList.add("editorHolder");
        editor.appendChild(holderDiv);

        // Add drag and drop handle to the editor
        const dragHandle = document.createElement('span');
        dragHandle.classList.add("dragHandle");
        dragHandle.innerHTML = '<i class="fas fa-grip-vertical"></i>';
        holderDiv.appendChild(dragHandle);

        // Add delete button
        const deleteButton = document.createElement('button');
        deleteButton.classList.add("btn", "btn-close", "btn-sm", "deleteButton");
        deleteButton.onclick = function () {
            editor.remove();
        };
        holderDiv.appendChild(deleteButton);
        
    }

    generateEmailEditor() {
        const emailInput = document.createElement('input');
        emailInput.type = 'email';
        emailInput.classList.add("form-control", "mb-3");
        emailInput.placeholder = 'Email';
        emailInput.disabled = true;
        return emailInput;
    }

    generateShortTextEditor() {
        const shortTextInput = document.createElement('input');
        shortTextInput.type = 'text';
        shortTextInput.classList.add("form-control", "mb-3");
        shortTextInput.placeholder = 'Rövid szöveg';
        shortTextInput.disabled = true;
        return shortTextInput;
    }

    generateLongTextEditor() {
        const longTextInput = document.createElement('textarea');
        longTextInput.classList.add("form-control", "mb-3");
        longTextInput.placeholder = 'Hosszú szöveg';
        longTextInput.disabled = true;
        return longTextInput;
    }

    generateDateEditor() {
        const dateInput = document.createElement('input');
        dateInput.type = 'date';
        dateInput.classList.add("form-control", "mb-3");
        dateInput.disabled = true;
        return dateInput;
    }

    generateTimeEditor() {
        const timeInput = document.createElement('input');
        timeInput.type = 'time';
        timeInput.classList.add("form-control", "mb-3");
        timeInput.disabled = true;
        return timeInput;
    }

    generateChoiceEditor() {
        const holderDiv = document.createElement('div');
        holderDiv.classList.add("mb-3");
    
        this.details.forEach((option, index) => {
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
            label.classList.add("form-control");
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
    
            holderDiv.appendChild(div);
        });
    
        return holderDiv;
    }
}


