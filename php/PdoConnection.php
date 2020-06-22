<?php

//current columns:
// session_id: authorisations hash (varchar 255)
// login: user's login (varchar 30)
// password: password hash (varchar 255)
// html_text: html tags (mediumtext)
// id: set at registration (integer)
// email: users e-mail (varchar 60)
// validation: is email approve? (boolean)
// validation_string: hash of random string to validate email or reset password(varchar 255)
// time: time of acc creation (time() string) (varchar 255)

class PdoConnection {
    public $host = 'localhost';
    public $db = 'first_php';
    public $user = 'root';
    public $pass = '1162/fire/9991';
    public $charset = 'utf8';
    public $pdoObj = '';
    public $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    private function getDsn() {
        return 'mysql:host='.$this->host.';dbname='.$this->db.';charset='.$this->charset.';';
    }

    public function connectToMysql(){
        try {
            $connection = new PDO($this->getDsn(), $this->user, $this->pass, $this->opt);
        } catch (PDOException $e) {
            die('Connection not success: ' . $e->getMessage() . '<br>' . 'Script interrupt');
        }
        $this->pdoObj = $connection;
        return true;
    }


}