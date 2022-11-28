<?php 

    require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";
    require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/account-class.php";

    class Login extends DbHandler {

        private $uid; 
        private $pwd;

        public function __construct($uid, $pwd) {
            $this->uid = $uid;
            $this->pwd = $pwd;
        }

        // form validation
        private function validate() {
            if ($this->emptyInput()) {
                header("Location: ../../login.php?error=emptyInput");
                exit();
            }
        }

        private function emptyInput() {
            if (empty($this->uid) || empty($this->pwd)) {
                return true;
            } else {
                return false;
            }
        }

        public function loginUser() {
            $this->validate();
            $sql = "SELECT clients_pwd FROM clients WHERE clients_uid = :uid or clients_email = :uid;";
            $pdo = $this->connect();

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":uid", $param_uid, PDO::PARAM_STR);

                // Set parameters
                $param_uid = $this->uid;

                // execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $checkPwd = password_verify($this->pwd, $pwdHashed[0]["clients_pwd"]);

                        if ($checkPwd) {
                            $sql = "SELECT * FROM clients WHERE clients_uid = :uid or clients_email = :uid;";
                            
                            if ($stmt = $pdo->prepare($sql)) {
                                // Bind variables to the prepared statement as parameters
                                $stmt->bindParam(":uid", $param_uid, PDO::PARAM_STR);

                                // Set parameters
                                $param_uid = $this->uid;

                                // execute the prepared statement

                                if ($stmt->execute()) {
                                    if ($stmt->rowCount() > 0) {
                                        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $client = new Client($user[0]["clients_id"], $user[0]["clients_firstName"], $user[0]["clients_lastName"], $accounts = array(new Account($user[0]["clients_id"], $user[0]["clients_mainAccountId"])), DateTime::createFromFormat("j-m-y H:i:s", $user[0]["clients_joinedDate"]), $user[0]["clients_email"]);

                                        // save the loggedin boolean and client objectto superglobal $_SESSION
                                        session_start();
                                        $_SESSION["loggedin"] = true;
                                        $_SESSION["client"] = $client;
                                    }
                                } else {
                                    exit("Session data save error.");
                                }
                            } 
                        } else {
                            header("Location: ../../login.php?error=invalidUidOrPwd");
                            exit();
                        }
                    } else {
                        header("Location: ../../login.php?error=invalidUidOrPwd");
                        exit();
                    }
                } else {
                    exit("Prepared statement failed (InvalidUidOrPwd).");
                }
            } 
        }
    }