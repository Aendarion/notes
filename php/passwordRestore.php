<?php

function main() {
    if (!$_POST['loginOrEmail']){
        include('passwordRestoreForm.php');
    } else {
        include('showErrorMsg.php');
        include('PdoConnection.php');
        $mainConnect = new PdoConnection();
        $mainConnect->connectToMysql();

        include('ResetPassword.php');
        $reset = new ResetPassword();
        if ($reset->sendResetLetter($mainConnect->pdoObj, $_POST['loginOrEmail'])) {
            echo 'All correct <br>';
            //header("Location: notes.php");
            //change html to php
            //include "check" to every javascript
        } else {
            showErrorMsg($reset->getErrorMsg());
        }
    }
}
function generateHtmlPage($title){
    include ('header.php');
    main();
    include ('footer.php');
}

generateHtmlPage('Restore password');