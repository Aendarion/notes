window.addEventListener("load", main);

function main() {
    let formValidation = {
        passwordInput : document.querySelector('#passwordInput'),
        duplicateInput : document.querySelector('#duplicateInput'),
        warning : '',
        addInputsListeners : function () {
            this.passwordInput.addEventListener("blur", this.validatePasswordInput.bind(this));
            this.passwordInput.addEventListener("input", this.removePasswordMessages.bind(this));
            this.duplicateInput.addEventListener("blur", this.validateDuplicateInput.bind(this));
            this.duplicateInput.addEventListener("input", this.removeDuplicateMessages.bind(this));
        },
        validateDuplicateInput : function () {
            let self = this;
            if (self.duplicateValidation(self.duplicateInput.value)){
                self.removeWarningMessage(self.duplicateInput.id);
                return true;
            } else {
                self.removeWarningMessage(self.duplicateInput.id);
                self.addErrorDiv(self.duplicateInput, self.warning);
                return false;
            }
        },
        removeDuplicateMessages : function () {
            this.removeWarningMessage(this.duplicateInput.id);
        },
        duplicateValidation : function (string) {
            if (this.duplicateInput.value !== this.passwordInput.value) {
                this.warning = "Passwords is not equal";
            } else {
                return true;
            }
            return false;
        },
        validatePasswordInput : function () {
            let self = this;
            if (self.passwordValidation(self.passwordInput.value)){
                self.removeWarningMessage(self.passwordInput.id);
                return true;
            } else {
                self.removeWarningMessage(self.passwordInput.id);
                self.addErrorDiv(self.passwordInput, self.warning);
                return false;
            }
        },
        removePasswordMessages : function () {
            this.removeWarningMessage(this.passwordInput.id);
        },
        passwordValidation : function () {
            if (this.passwordInput.value.length < 8){
                this.warning = "Password should contain at least 8 characters";
                return false;
            }
            return true;
        },
        addErrorDiv : function (afterWhatNode, text) {
            this.removeWarningMessage(afterWhatNode);
            let warningDiv = document.createElement('div');
            warningDiv.id = "errorMsg";
            warningDiv.classList = afterWhatNode.id;
            warningDiv.style.width = afterWhatNode.offsetWidth + "px";
            let warningP = document.createElement('p');
            warningP.innerText = text;
            warningDiv.append(warningP.cloneNode(true));
            afterWhatNode.after(warningDiv.cloneNode(true));
        },
        removeWarningMessage : function (inputId) {
            let warningMessages = document.querySelectorAll('#errorMsg');
            for (let i=0; i<warningMessages.length; i++){
                if (warningMessages[i].classList == inputId){
                    warningMessages[i].remove();
                }
            }
        }
    };
    addListenerToForm();
    formValidation.addInputsListeners();

    function addListenerToForm() {
        let loginForm = document.forms.newPass;
        loginForm.addEventListener("submit", sendSaveForm);
        let submitButton = document.querySelector("#save");
        submitButton.addEventListener("click", sendSaveForm);

        function sendSaveForm(event) {
            if (!formValidation.validateDuplicateInput()){
                event.preventDefault();
                return false;
            }
            if (!formValidation.validatePasswordInput()){
                event.preventDefault();
                return false;
            }
            loginForm.submit()
            event.preventDefault();
        }
    }
}