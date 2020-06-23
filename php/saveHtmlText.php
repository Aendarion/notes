<?php
function main(){
    include ('PdoConnection.php');
    $mainConnect = new PdoConnection();
    $mainConnect->connectToMysql();

    include ('DefaultUser.php');
    $connectedUser = new DefaultUser();

    if ($connectedUser->authorisation($mainConnect->pdoObj, $_COOKIE['id'], $_COOKIE['hash'])){
        if ($connectedUser->isValid($mainConnect->pdoObj, $_COOKIE['id'])){
            $html_text = file_get_contents('php://input');
            $html_text = '<div id="main">' . $html_text . '</div>';
            $connectedUser->saveHtmlText($mainConnect->pdoObj, $_COOKIE['id'], $html_text);
            echo 'Changes saved successfully';
        } else {
            echo 'Validation link was send to your e-mail. Please, follow the instructions in the letter.';
        }
    } else {
        echo $connectedUser->getErrorMsg();
    }
}

main();

