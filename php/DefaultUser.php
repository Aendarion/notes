<?php
class DefaultUser {
    private $id = false;
    private $sessionId = false;
    private $login = false;
    private $password = false;
    private $errorMsg = false;

    public function setId($linkToMysql, $enteredLogin){
        $stmt = $linkToMysql->prepare('SELECT id FROM users WHERE login = ?');
        $stmt->execute(array($enteredLogin));
        $this->id = $stmt->fetchColumn();
        return true;
    }

    private function setLogin($value){
        $this->login = $value;
    }

    public function getLogin($linkToMysql, $id){
        $stmt = $linkToMysql->prepare('SELECT login FROM users WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetchColumn();
    }

    private function setPassword($value){
        $this->password = $value;
    }

    public function getErrorMsg(){
        return $this->errorMsg;
    }

    private function setErrorMessage($value){
        $this->errorMsg = $value;
    }

    private function identification($linkToMysql, $enteredLogin) {
        $stmt = $linkToMysql->prepare('SELECT password FROM users WHERE login = ?');
        $stmt->execute(array($enteredLogin));
        $result = $stmt->fetchColumn();
        if (!$result){
            return false;
        }
        return isset($result); //true if login exist
    }

    private function checkPassword($linkToMysql, $enteredPassword){
        function getPassword($linkToMysql, $userLogin){
            $stmt = $linkToMysql->prepare('SELECT password FROM users WHERE login = ?');
            $stmt->execute(array($userLogin));
            return $stmt->fetchColumn();
        }
        if (password_verify($enteredPassword, getPassword($linkToMysql, $this->login))) {
            return true;
        }
        return false;
    }

    private function authentication($linkToMysql, $enteredLogin, $enteredPassword){
        if (!$this->identification($linkToMysql, $enteredLogin)){
            $this->errorMsg = 'User with login "'.$enteredLogin.'" not found';
            return false;
        }
        $this->setLogin($enteredLogin);
        if (!$this->checkPassword($linkToMysql, $enteredPassword)){
            $this->errorMsg = 'Password is wrong';
            return false;
        }
        $this->setPassword(getPassword($linkToMysql, $enteredLogin));
        $this->setId($linkToMysql, $enteredLogin);
        return true;
    }

    private function generateSessionId(){
        function generateString($length=30) {
            $availableChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
            $result = "";
            $availableCharsLen = strlen($availableChars) - 1;
            while (strlen($result) < $length) {
                $result .= $availableChars[mt_rand(0,$availableCharsLen)];
            }
            return $result;
        }
        $resultStroke = generateString(random_int(20,40)) . $this->password;

        $this->sessionId = password_hash($resultStroke, PASSWORD_DEFAULT);
        return $this->sessionId;
    }

    public function setCookies($linkToMysql, $enteredLogin, $enteredPassword, $remember=true){
        if (!$this -> authentication($linkToMysql, $enteredLogin, $enteredPassword)){
            return false;
        }
        $time = 0;
        if ($remember){
            $time = time()+60*60*24*30;
        }
        setcookie("id", $this->id, $time, "/");
        setcookie("hash", $this->generateSessionId(), $time, "/", null, null, true); // http only
        $stmt = $linkToMysql->prepare('UPDATE users SET session_id=? WHERE id=?');
        $stmt->execute(array($this->sessionId, $this->id));
        return true;
    }

    public function authorisation($linkToMysql, $id, $hash){
        function removeCookies(){
            setcookie("id", "", time() - 3600*24*30*12, "/");
            setcookie("hash", "", time() - 3600*24*30*12, "/", null, null, true);
        }
        function getAuthorisationHash($link, $userId){
            $stmt = $link->prepare('SELECT session_id FROM users WHERE id = ?');
            $stmt->execute(array($userId));
            $result = $stmt->fetchColumn();

            if (!isset($result)) {
                return false;
            }
            return $result;
        }

        if (!settype($id, "integer")){ //if id not integer
            removeCookies();
            $this->setErrorMessage('Saved cookies is wrong. Try enable cookies in your browser and log in once more.');
            return false;
        }
        if (!isset($id) or !isset($hash)){ //if null
            removeCookies();
            $this->setErrorMessage('Saved cookies is wrong. Try enable cookies in your browser and log in once more.');
            return false;
        }
        if (!$id or !$hash){ //if false
            removeCookies();
            $this->setErrorMessage('Saved cookies is wrong. Try enable cookies in your browser and log in once more.');
            return false;
        }

        if (getAuthorisationHash($linkToMysql, $id) == html_entity_decode($hash)){ //hashes is equal
            return true;
        }
        //else..
        removeCookies();
        $this->setErrorMessage('Saved cookies is wrong. Try enable cookies in your browser and log in once more.');
        return false;
    }

    public function isValid($link, $id){
        $stmt = $link->prepare('SELECT validation FROM users WHERE id = ?');
        $stmt->execute(array($id));
        $result = $stmt->fetchColumn();

        if ($result == 1) {
            return true;
        }
        return false;
    }

    public function validateEmail($linkToMysql, $userId, $urlGet, $sessionHash){
        function makeAccountValid($link, $id){
            $stmt = $link->prepare('UPDATE users SET validation=1 WHERE id=?');
            $stmt->execute(array($id));
            return true;
        }
        function isSessionHashEqual($link, $id, $sessionHash){
            function getAuthorisationHash($link, $id){
                $stmt = $link->prepare('SELECT session_id FROM users WHERE id = ?');
                $stmt->execute(array($id));
                $result = $stmt->fetchColumn();

                if (!isset($result)) {
                    return false;
                }
                return $result;
            }
            if (getAuthorisationHash($link, $id) == $sessionHash) {
                return true;
            }
            return false;
        }
        function isEmailHashEqual($link, $id, $emailHash){
            function getEmailValidationHash($link, $id){
                $stmt = $link->prepare('SELECT validation_string FROM users WHERE id = ?');
                $stmt->execute(array($id));
                $result = $stmt->fetchColumn();

                if (!isset($result)) {
                    return false;
                }
                return $result;
            }
            if (getEmailValidationHash($link, $id) == $emailHash) {
                return true;
            }
            return false;
        }
        if ($this->isValid($linkToMysql, $userId)){
            return true;
        }
        if (!isSessionHashEqual($linkToMysql, $userId, $sessionHash)){
            $this->errorMsg = "Saved cookies is wrong. Try clear cookie and login once more.";
        }
        if (!isEmailHashEqual($linkToMysql, $userId, $urlGet)){
            $this->errorMsg = "Used link is wrong. Try once more.";
        }
        if (!makeAccountValid($linkToMysql, $userId)){
            $this->errorMsg = "Wrong with changing account status in database.";
        }
        return true;
    }
}