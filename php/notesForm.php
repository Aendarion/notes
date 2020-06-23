<link rel="stylesheet" href="../style/notes.css">
<?php
  $htmlText = $connectedUser->getHtmlText($mainConnect->pdoObj, $_COOKIE['id']);
  if (!$htmlText) {
      include 'defaultNotes.php';
  } else {
      echo $htmlText;
  }
?>
<script src="../javascript/notes.js"></script>