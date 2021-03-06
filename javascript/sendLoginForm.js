window.addEventListener("load", main);

function main() {
    let formValidation = {
        loginInput : document.querySelector('#loginInput'),
        passwordInput : document.querySelector('#passwordInput'),
        warning : '',
        addInputsListeners : function () {
            this.loginInput.addEventListener("blur", this.validateLoginInput.bind(this));
            this.loginInput.addEventListener("input", this.removeLoginMessages.bind(this));
            this.passwordInput.addEventListener("blur", this.validatePasswordInput.bind(this));
            this.passwordInput.addEventListener("input", this.removePasswordMessages.bind(this));
        },
        validateLoginInput : function () {
            let self = this;
            if (self.loginValidation(self.loginInput.value)){
                self.removeWarningMessage(self.loginInput.id);
                return true;
            } else {
                self.removeWarningMessage(self.loginInput.id);
                self.addErrorDiv(self.loginInput, self.warning);
                return false;
            }
        },
        removeLoginMessages : function () {
            this.removeWarningMessage(this.loginInput.id);
        },
        loginValidation : function (string) {
            if (/[^a-zA-Z0-9]/.test(string)) {
                this.warning = "Login should contain only English letter and/or numbers";
            } else if (string.length < 5){
                this.warning = "Login should contain at least 5 characters";
            } else if (string.length > 30){
                this.warning = "Login can't contain more than 30 characters";
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
        let loginForm = document.forms.authorization;
        loginForm.addEventListener("submit", sendLoginForm);
        addButtonsListener();

        function addButtonsListener() {
            let buttons = document.querySelectorAll("button");
            buttons.forEach(button => {
                if (button.id == "loginButton") {
                    button.addEventListener("click", sendLoginForm);
                } else if (button.id == "registrationButton") {
                    button.addEventListener("click", redirectToRegistrationForm);
                }
            })
        }

        function sendLoginForm(event) {
            if (!formValidation.validateLoginInput()){
                event.preventDefault();
                return false;
            }
            if (!formValidation.validatePasswordInput()){
                event.preventDefault();
                return false;
            }
            loginForm.submit();
            event.preventDefault();
        }

        function redirectToRegistrationForm(event) {
            document.location.href = "../php/registration.php";
            event.preventDefault();
        }
    }



}