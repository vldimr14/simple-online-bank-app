<?php 
    
    class Signup extends DbHandler {

        private $firstName;
        private $lastName;
        private $passportId;
        private $email;
        private $uid;
        private $pwd;
        private $pwdRepeat;

        public function __construct($firstName, $lastName, $passportId, $email, $uid, $pwd, $pwdRepeat) {
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->passportId = $passportId;
            $this->email = $email;
            $this->uid = $uid;
            $this->pwd = $pwd;
            $this->pwdRepeat = $pwdRepeat;
        }

        // form validation
        private function validate() {
            if ($this->emptyInput()) {
                header("Location: ../../signup.php?error=emptyInput");
                exit();
            } else if ($this->invalidUid()) {
                header("Location: ../../signup.php?error=invalidUid");
                exit();
            } else if ($this->invalidEmail()) {
                header("Location: ../../signup.php?error=invalidEmail");
                exit();
            } else if (!$this->pwdMatch()) {
                header("Location: ../../signup.php?error=pwdsDontMatch");
                exit();
            } else if ($this->uidTaken()) {
                header("Location: ../../signup.php?error=uidTaken");
                exit();
            }
        }

        private function emptyInput() {
            if (empty($this->firstName) || empty($this->lastName) || empty($this->passportId) || empty($this->email) || empty($this->uid) || empty($this->pwd) || empty($this->pwdRepeat)) {
                return true;
            } else {
                return false;
            }
        }

        private function invalidUid() {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->uid)) {
                return true;
            } else {
                return false;
            }
        }

        private function invalidEmail() {
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
        }

        private function pwdMatch() {
            if ($this->pwd == $this->pwdRepeat) {
                return true;
            } else {
                return false;
            }
        }

        private function uidTaken() {
            if ($this->userExists()) {
                return true; 
            } else {
                return false;
            }
        }

        private function userExists() {
            $sql = "SELECT clients_id FROM clients WHERE clients_uid = :uid OR clients_email = :email;";
            $pdo = $this->connect();

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":uid", $param_uid, PDO::PARAM_STR);
                $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

                // Set parameters
                $param_uid = $this->uid;
                $param_email = $this->email;

                // execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
            } else {
                exit("Prepared statement failed (userExists).");
            }
        }

        public function signupUser() {
            // validate form
            $this->validate();
            
            // sign up user
            $sql = "INSERT INTO clients (clients_firstName, clients_lastName, clients_passportId, clients_email, clients_uid, clients_pwd) VALUES (:firstName, :lastName, :passportId, :email, :uid, :pwd);";
            $pdo = $this->connect();

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":firstName", $param_firstName, PDO::PARAM_STR);
                $stmt->bindParam(":lastName", $param_lastName, PDO::PARAM_STR);
                $stmt->bindParam(":passportId", $param_passportId, PDO::PARAM_STR);
                $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
                $stmt->bindParam(":uid", $param_uid, PDO::PARAM_STR);
                $stmt->bindParam(":pwd", $param_pwd, PDO::PARAM_STR);

                // hash password
                $hashedPassword = password_hash($this->pwd, PASSWORD_DEFAULT);

                // Set parameters
                $param_firstName = $this->firstName;
                $param_lastName = $this->lastName;
                $param_passportId = $this->passportId;
                $param_email = $this->email;
                $param_uid = $this->uid;
                $param_pwd = $hashedPassword;

                if(!$stmt->execute()) {
                    exit("statement failed (signupUser)");
                } 
                
            } else {
                exit("Prepared statement failed (signupUser).");
            }
        }
    }