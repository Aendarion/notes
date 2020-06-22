<?php
function main() {
    if (!$_POST['email']){
        include('registrationForm.php');
    } else {
        include('showErrorMsg.php');
        include('PdoConnection.php');
        $mainConnect = new PdoConnection();
        $mainConnect->connectToMysql();

        include('UserRegistration.php');
        $newRegistration = new UserRegistration();
        if ($newRegistration->addNewUser($mainConnect->pdoObj, $_POST['login'], $_POST['password'], $_POST['email'])) {
            echo 'All correct. Wait a few seconds, please, we are redirecting you.';
            include('DefaultUser.php');
            $connectedUser = new DefaultUser();
            $connectedUser->setCookies($mainConnect->pdoObj, $_POST['login'], $_POST['password']);
            //header("Location: notes.php");
        } else {
            showErrorMsg($newRegistration->getErrorMsg());
        }
    }

}
function generateHtmlPage($title){
    include ('header.php');
    main();
    include ('footer.php');
}

generateHtmlPage('Registration');