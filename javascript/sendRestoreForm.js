window.addEventListener("load", main);

function main() {
    addListenerToForm();

    function addListenerToForm() {
        let loginForm = document.forms.forgot_password;
        loginForm.addEventListener("submit", sendRestoreForm);
        let submitButton = document.querySelector("#createAccount");
        submitButton.addEventListener("click", sendRestoreForm);

        function sendRestoreForm(event) {
            loginForm.submit()
            event.preventDefault();
        }
    }
}