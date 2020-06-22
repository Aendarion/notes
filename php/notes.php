<?php
function main(){
    include ('showErrorMsg.php');
    include ('PdoConnection.php');
    $mainConnect = new PdoConnection();
    $mainConnect->connectToMysql();

    include ('DefaultUser.php');
    $connectedUser = new DefaultUser();

    if ($connectedUser->authorisation($mainConnect->pdoObj, $_COOKIE['id'], $_COOKIE['hash'])){
        if ($connectedUser->isValid($mainConnect->pdoObj, $_COOKIE['id'])){
            echo 'All correct.';
        } else {
            showErrorMsg('Validation link was send to your e-mail. Please, follow the instructions in the letter.');
        }
    } else {
        showErrorMsg($connectedUser->getErrorMsg());
    }
}

function generateHtmlPage($title){
    include ('header.php');
    main();
    include ('footer.php');
}

generateHtmlPage('Notes');
?>