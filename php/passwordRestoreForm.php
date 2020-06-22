<link rel="stylesheet" href="../style/authorization.css">
<form name="forgot_password" action="../php/passwordRestore.php" method="post">
    <input name="loginOrEmail" type="text" placeholder="Your e-mail or login">
    <div id="buttons">
        <button id="createAccount">Restore</button>
    </div>
</form>
<script src="../javascript/sendRestoreForm.js"></script>