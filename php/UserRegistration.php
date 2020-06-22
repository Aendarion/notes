<?php


class UserRegistration {

    private $errorMsg = false;

    public function getErrorMsg(){
        return $this->errorMsg;
    }

    private function checkStringSymbols($anyString) { //only English letter or numbers; 5<=length<=30
        if (!preg_match("/^[a-zA-Z0-9]+$/", $anyString)){
            $this->errorMsg = "Login should contain only English letter and/or numbers";
            return false;
        } else if (strlen($anyString) < 5){
            $this->errorMsg = "Login should contain at least 5 characters";
            return false;
        } else if (strlen($anyString) > 30){
            $this->errorMsg = "Login can't contain more than 30 characters";
            return false;
        }
        return true;
    }

    private function isLoginAvailable($linkToMysql, $enteredLogin) {
        $stmt = $linkToMysql->prepare('SELECT id FROM users WHERE login = ?');
        $stmt->execute(array($enteredLogin));
        $result = $stmt->fetchColumn();
        if (!$result){
            return true;
        }
        $this->errorMsg = "Login is used by another user";
        return false; //false if login exist
    }

    private function isEmailCorrect($link, $emailString){
        function isEmailUnique($link, $email){
            $stmt = $link->prepare("SELECT validation FROM users WHERE email = ?");
            $stmt->execute(array($email));
            while ($row = $stmt->fetch(PDO::FETCH_LAZY)){
                if ($row[0] == 1){
                    return false; //if there already exist account with same valid email
                }
            }
            return true;
        }
        if (!filter_var($emailString, FILTER_VALIDATE_EMAIL)) {
            $this->errorMsg = "E-mail format is wrong";
            return false;
        }
        if (strlen($emailString) > 60){
            $this->errorMsg = "E-mail can't contain more than 60 characters. Please, use another e-mail.";
            return false;
        }
        if (!isEmailUnique($link, $emailString)){
            $this->errorMsg = "E-mail already used by another user. You may try restore access at 'Forgot password' page";
            return false;
        }
        return true;
    }

    public function addNewUser($linkToMysql, $login, $password, $email){
        function getId($link){
            $stmt = $link->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
            $result = $stmt->fetchColumn();

            if (!$result){
                return 1;
            }
            return $result+1;
        }
        function generateRandomHash($length=35){
            $availableChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
            $result = "";
            $availableCharsLen = strlen($availableChars) - 1;
            while (strlen($result) < $length) {
                $result .= $availableChars[mt_rand(0,$availableCharsLen)];
            }
            return password_hash(password_hash($result, PASSWORD_DEFAULT), PASSWORD_DEFAULT);
        }
        function sendValidationEmail($email, $validation_string){
            $message = "Hello, \r\n";
            $message.= "Your e-mail was used to register on the site 'EasyNotes'. \r\n";
            $message.= "To approve e-mail and complete registration please follow the link: \r\n";
            $message.= "http://localhost/first/php/validation.php?valid_string=" . $validation_string . "\r\n";
            $message.= "If you don't register there, please, ignore this letter.\r\n";
            if (mail($email, "E-mail_validation", $message)){
                return true;
            }
            return false;
        }
        function saveToMysql($linkToMysql, $login, $password, $email, $update=false){
            $validation_string = generateRandomHash(random_int(25, 45));
//            if (!sendValidationEmail($email, $validation_string)) { //letter dont send
//                $this->errorMsg = "Error sending validation letter to your email. Please, check your e-mail settings and try again";
//                return false;
//            }
            $message= "To approve e-mail and complete registration please follow the link: \r\n";
            $message.= "http://localhost/first/php/validation.php?valid_string=" . $validation_string . "\r\n";
            echo $message;
            $passwordHash = password_hash(trim($password), PASSWORD_DEFAULT);
            $htmlText = '<p>Example</p>';
            $session_id = password_hash(password_hash(random_int(0, 99999999), PASSWORD_DEFAULT), PASSWORD_DEFAULT);
            $validate = 0;
            if ($update){
                $sql = 'UPDATE users SET password=?, email=?, id=?, html_text=?, validation=?, validation_string=?, session_id=?, time=? WHERE login=?';
            } else {
                $sql = 'INSERT INTO users SET password=?, email=?, id=?, html_text=?, validation=?, validation_string=?, session_id=?, time=?, login=?';
            }

            $stmt = $linkToMysql->prepare($sql);
            $stmt->execute([$passwordHash, $email, getId($linkToMysql), $htmlText, $validate, $validation_string, $session_id, time(), $login]);
            return true;
        }
        function isLoginValid($link, $login){
            $timeout = 60 * 60 * 24; // one day
            function isEmailValid($link, $login){
                $stmt = $link->prepare("SELECT validation FROM users WHERE login = ?");
                $stmt->execute(array($login));
                $result = $stmt->fetchColumn();
                if ($result == 1) {
                    return true;
                }
            }
            function isLoginTimeout($link, $login, $timeout){
                $stmt = $link->prepare("SELECT time FROM users WHERE login = ?");
                $stmt->execute(array($login));
                $result = $stmt->fetchColumn();
                if ((time() - $result) < $timeout) {
                    return false;
                }
                return true;
            }
            if (isEmailValid($link, $login)){
                return true;
            }
            if (!isLoginTimeout($link, $login, $timeout)){
                return true;
            }
            return false;
        }
        if (!$this->isEmailCorrect($linkToMysql, $email)) {
            return false;
        }
        if (strlen($password) < 8){
            $this->errorMsg = "Password should contain at least 8 characters";
            return false;
        }
        if ($this->checkStringSymbols($login)){ //login correct
            if ($this->isLoginAvailable($linkToMysql, $login)) { //login free
                saveToMysql($linkToMysql, $login, $password, $email); //save it
                return true;
            }
            if (!isLoginValid($linkToMysql, $login)) { //non-active non-valid for more than 24hrs
                saveToMysql($linkToMysql, $login, $password, $email, true); //rewrite non-active login
                return true;
            }
        }
        return false;
    }
}