<?php
function main() {
    include ('showErrorMsg.php');
    include ('PdoConnection.php');
    $mainConnect = new PdoConnection();
    $mainConnect->connectToMysql();

    include ('DefaultUser.php');
    $connectedUser = new DefaultUser();
    if ($connectedUser->validateEmail($mainConnect->pdoObj, $_COOKIE['id'], $_COOKIE['hash'], $_GET['valid_string'])){
        echo 'All correct <br>';
        header("Location: notes.php");
    } else {
        showErrorMsg($connectedUser->getErrorMsg());
    }
}

function generateHtmlPage($title){
    include ('header.php');
    main();
    include ('footer.php');
}

generateHtmlPage('Validation');