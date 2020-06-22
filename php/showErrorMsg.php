<?php
function showErrorMsg($errorText){
    $showString = '<div id="errorMsg"><p>'.$errorText.'</p>';
    $showString.= '<button id="mainPageLink" onclick="document.location.href = \'../index.html\'">';
    $showString.= 'Return to main page</button></div>';
    echo $showString;
}