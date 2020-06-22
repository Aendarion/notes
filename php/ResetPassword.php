<?php


class ResetPassword {
    private $errorMsg = false;

    public function getErrorMsg(){
        return $this->errorMsg;
    }

    private function isValid($link, $email){
        $stmt = $link->prepare('SELECT validation FROM users WHERE email = ?');
        $stmt->execute(array($email));
        $result = $stmt->fetchColumn();

        if ($result == 1) {
            return true;
        }
        return false;
    }


    private function getUserEmail($link, $emailOrLogin){
        function isEmail($string) {
            if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
            return false;
        }
        function checkEmail($link, $email) {
            $stmt = $link->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute(array($email));
            $result = $stmt->fetchColumn();
            if ($result) {
                return true;
            }
            return false;
        }
        function getEmail($link, $login) {
            $stmt = $link->prepare('SELECT email FROM users WHERE login = ?');
            $stmt->execute(array($login));
            $result = $stmt->fetchColumn();
            if ($result) {
                return $result;
            }
            return false;
        }
        if (isEmail($emailOrLogin)){
            if (checkEmail($link, $emailOrLogin)){
                return $emailOrLogin;
            }
            $this->errorMsg = "Account with this e-mail doesn't exist.";
            return false;
        }
        $userEmail = getEmail($link, $emailOrLogin);
        if (!$userEmail){
            $this->errorMsg = "Account with this login doesn't exist.";
            return false;
        }
        return $userEmail;
    }

    private function generateRandomHash($length=35){
        $availableChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $result = "";
        $availableCharsLen = strlen($availableChars) - 1;
        while (strlen($result) < $length) {
            $result .= $availableChars[mt_rand(0,$availableCharsLen)];
        }
        return password_hash(password_hash($result, PASSWORD_DEFAULT), PASSWORD_DEFAULT);
    }

    private function saveResetHashToMysql($link, $hash, $email){
        $stmt = $link->prepare('UPDATE users SET validation_string=? WHERE email =?');
        $stmt->execute(array($hash, $email));
        return true;
    }

    public function sendResetLetter($linkToMysql, $emailOrLogin){
        function getUserLogin($link, $email){
            $stmt = $link->prepare('SELECT login FROM users WHERE email = ?');
            $stmt->execute(array($email));
            return $stmt->fetchColumn();
        }
        function getUserId($link, $email){
            echo 'cho';
            $stmt = $link->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute(array($email));
            return $stmt->fetchColumn();
        }
        function sendResetLetter($email, $hash, $login, $id){
            $message = "Hello, \r\n";
            $message.= "Someone request password reset at account '". $login."'\r\n";
            $message.= "To approve reset and set a new password please follow the link: \r\n";
            $message.= "http://localhost/first/php/newPassword.php?valid_string=".$hash."&id=".$id."\r\n";
            $message.= "If you don't ask for password reset, please, ignore this letter.\r\n";
            if (mail($email, "Password_reset", $message)){
                return true;
            }
            return false;
        }
        if (strlen($emailOrLogin) > 60){
            $this->errorMsg = "E-mail or login can't contain more than 60 characters.";
            return false;
        }
        $userEmail = $this->getUserEmail($linkToMysql, $emailOrLogin);
        if (!$userEmail) {
            return false;
        }
        if (!$this->isValid($linkToMysql, $userEmail)){
            $this->errorMsg = "Can't reset password of non-valid account. Please, verify your e-mail first.";
            return false;
        }
        $resetHash = $this->generateRandomHash(random_int(25,35));
        $this->saveResetHashToMysql($linkToMysql, $resetHash, $userEmail);
        $userId = getUserId($linkToMysql, $userEmail);

        $userLogin = getUserLogin($linkToMysql, $userEmail);
//        if (sendResetLetter($userEmail, $resetHash, $userLogin, $userId)){
//            $this->errorMsg = "Can't send preset letter. Please, check your e-mail settings and try again.";
//              return false;
//        }
        $message = "To approve reset and set a new password please follow the link: \r\n";
        $message.= "http://localhost/first/php/newPassword.php?valid_string=".$resetHash."&id=".$userId."\r\n";
        echo $message;
        return true;
    }

    public function checkResetLink($linkToMysql, $id, $validString){
        function getSavedValidationString($link, $id){
            $stmt = $link->prepare('SELECT validation_string FROM users WHERE id = ?');
            $stmt->execute(array($id));
            $result = $stmt->fetchColumn();
            if (!$result){
                return false;
            }
            return $result;
        }

        $savedHash = getSavedValidationString($linkToMysql, $id);
        if (!$savedHash){
            $this->errorMsg = "Reset link is timeout";
            return false;
        }
        if ($savedHash == $validString) {
            return true;
        }
        $this->errorMsg = "Verification link is wrong";
        return false;
    }

    public function setNewPassword ($linkToMysql, $id, $password, $passDuplicate){
        if ($password != $passDuplicate){
            $this->errorMsg = 'Password are different';
            return false;
        }
        if (strlen($password) < 8){
            $this->errorMsg = 'Password should contain at least 8 characters';
            return false;
        }
        function savePassToMysql($link, $id, $hash){
            $stmt = $link->prepare('UPDATE users SET password=? WHERE id =?');
            $stmt->execute(array($hash, $id));
            return true;
        }
        function resetSavedHash($link, $id){
            $stmt = $link->prepare('UPDATE users SET validation_string=0 WHERE id =?');
            $stmt->execute(array($id));
            return true;
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        savePassToMysql($linkToMysql, $id, $passwordHash);
        resetSavedHash($linkToMysql, $id);
        return true;
    }
}