<?php
function main() {
    if (!$_POST['login']){
        include ('loginForm.php');
    } else {
        include('showErrorMsg.php');
        include('PdoConnection.php');
        $mainConnect = new PdoConnection();
        $mainConnect->connectToMysql();

        include('DefaultUser.php');
        $connectedUser = new DefaultUser();
        if ($connectedUser->setCookies($mainConnect->pdoObj, $_POST['login'], $_POST['password'], $_POST['remember_me'])) {
            echo 'All correct <br>';
            header("Location: notes.php");
            //change html to php
            //include "check" to every javascript
        } else {
            showErrorMsg($connectedUser->getErrorMsg());
        }
    }
}

function generateHtmlPage($title){
    include ('header.php');
    main();
    include ('footer.php');
}

generateHtmlPage('Login');
