<?php

require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/login_system/db_config-class.php";

class Client extends DbHandler
{

    private $id;
    private $firstName;
    private $lastName;
    private $accounts; // array of accounts
    private $email;

    public function __construct($id, $firstName, $lastName, $accounts, $joinedDate, $email)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->accounts = $accounts;
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getAccounts()
    {
        return $this->accounts;
    }

    public function setAccounts($account)
    {
        if ($this->accounts[0]->getAccountNo() == NULL) {
            $this->accounts[0] = $account;
        } else {
            array_push($this->accounts, $account);
        }
    }

    public function getEmail()
    {
        return $this->email;
    }

    public static function refreshClientInfo($client)
    {
        $sql = "SELECT * FROM clients WHERE clients_id = :id;";
        $pdo = $client->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);

            // Set parameters
            $param_id = $client->getId();

            // execute the prepared statement

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $client = new Client($user[0]["clients_id"], $user[0]["clients_firstName"], $user[0]["clients_lastName"], $accounts = array(new Account($user[0]["clients_id"], $user[0]["clients_mainAccountId"])), DateTime::createFromFormat("j-m-y H:i:s", $user[0]["clients_joinedDate"]), $user[0]["clients_email"]);

                    // save the loggedin boolean and client objectto superglobal $_SESSION
                    $_SESSION["loggedin"] = true;
                    $_SESSION["client"] = $client;
                }
            } else {
                exit("Couldn't refresh the client data.");
            }
        }
    }
}
