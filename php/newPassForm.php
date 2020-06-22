<form name="newPass" action=<?php echo "../php/newPassword.php?valid_string=".$_GET['valid_string']."&id=".$_GET['id']?> method="post">
    <input name="password" id="passwordInput" type="text" placeholder="New password">
    <input name="duplicate" id="duplicateInput" type="text" placeholder="Duplicate password">
    <button id="save">Save</button>
</form>
<link rel="stylesheet" href="../style/authorization.css">
<script src="../javascript/sendSavePassForm.js"></script>
