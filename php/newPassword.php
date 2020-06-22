<?php
function main() {
    include ('showErrorMsg.php');
    include('PdoConnection.php');
    $mainConnect = new PdoConnection();
    $mainConnect->connectToMysql();

    include('ResetPassword.php');
    $reset = new ResetPassword();

    if (!isset($_POST['password'])) {
        include 'newPassForm.php';
    } else if ($reset->checkResetLink($mainConnect->pdoObj, $_GET['id'], $_GET['valid_string'])) {
        if ($reset->setNewPassword($mainConnect->pdoObj, $_GET['id'], $_POST['password'], $_POST['duplicate'])){
            echo 'All correct';
            include('DefaultUser.php');
            $newUser = new DefaultUser();
            $userLogin = $newUser->getLogin($mainConnect->pdoObj, $_GET['id']);
            if ($newUser->setCookies($mainConnect->pdoObj, $userLogin, $_POST['password'])){
                header("Location: notes.php");
            }
            showErrorMsg('Wrong cookies. Try login with new password.');
        } else {
            showErrorMsg($reset->getErrorMsg());
        }
        //header("Location: notes.php");
    } else {
        showErrorMsg($reset->getErrorMsg());
    }
}

function generateHtmlPage($title){
    include ('header.php');
    main();
    include ('footer.php');
}

generateHtmlPage('New password');

