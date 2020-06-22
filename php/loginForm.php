<link rel="stylesheet" href="../style/authorization.css">
<form name="authorization" id="authorization" action="../php/login.php" method="post">
    <input name="login" id="loginInput" type="text" placeholder="Your login">
    <input name="password" id="passwordInput" type="password" placeholder="Your password">
    <div id="remember_me">
        <input name="remember_me" type="checkbox" checked="true">
        <p>Remember me</p>
    </div>
    <div id="buttons">
        <button id="registrationButton">Registration</button>
        <button id="loginButton">Log in</button>
    </div>
    <div id="forgotPass"><a href="passwordRestore.php">Forgot password?</a></div>
</form>
<script src="../javascript/sendLoginForm.js"></script>