<?php

require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/login_system/db_config-class.php";

class Account extends DbHandler
{

    private $accountNo;
    private $balance;
    private $clientId;
    private $currency;

    public function __construct($clientId, $accountNo)
    {
        $this->accountNo = $accountNo;
        $this->clientId = $clientId;

        $this->getAccountInfo();
    }

    // getters and setters
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    // get account information from database.
    private function getAccountInfo()
    {
        $sql = "SELECT * FROM accounts WHERE clients_id = :clients_id AND accounts_id = :accountNo;";
        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":clients_id", $param_clients_id, PDO::PARAM_STR);
            $stmt->bindParam(":accountNo", $param_accountNo, PDO::PARAM_STR);

            // Set parameters
            $param_clients_id = $this->clientId;
            $param_accountNo = $this->accountNo;

            // execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $accountInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // set balance and currency since we already know clientId and acccountNo.
                    $this->balance = $accountInfo[0]["accounts_balance"];
                    $this->currency = $accountInfo[0]["accounts_currency"];
                }
            } else {
                exit("Statement failed. (getAccountInfo)");
            }
        } else {
            exit("Prepared statement failed. (getAccountInfo)");
        }
    }

    // Generate a random string, using a cryptographically secure 
    // pseudorandom number generator (random_int)
    private function generateRandAccountNo(): string
    {
        $length = 26;
        $keyspace = "0123456789";

        // if ($length < 1) {
        //     throw new \RangeException("Length must be a positive integer");
        // }

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        // join array elements with a string.
        $newAccountNo = implode('', $pieces);

        if (!Account::checkAccount($newAccountNo, $this->connect())) {
            return $newAccountNo;
        } else {
            $this->generateRandAccountNo();
        }
    }

    // check if account already exists
    public function checkAccount($accountNo)
    {
        $sql = "SELECT accounts_id FROM accounts WHERE accounts_id = :accountNo;";
        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":accountNo", $param_accountNo, PDO::PARAM_STR);

            // Set parameters
            $param_accountNo = $accountNo;

            // execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                exit("Statement failed. (checkAccount)");
            }
        } else {
            exit("Prepared statement failed. (checkAccount)");
        }
    }

    // check accounts currency
    public function checkAccountCurrency($accountNo)
    {
        $sql = "SELECT accounts_currency FROM accounts WHERE accounts_id = :accountNo;";
        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":accountNo", $param_accountNo, PDO::PARAM_STR);

            // Set parameters
            $param_accountNo = $accountNo;

            // execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $accountCurrency = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return $accountCurrency[0]["accounts_currency"];
                }
            } else {
                exit("Statement failed. (checkAccountCurrency)");
            }
        } else {
            exit("Prepared statement failed. (checkAccount)");
        }
    }

    // check if the user has the account with the same currency
    private function checkExistingAccountCurrency($currency)
    {
        $sql = "SELECT accounts_id FROM accounts WHERE clients_id = :clients_id AND accounts_currency = :currency;";
        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":clients_id", $param_clients_id, PDO::PARAM_STR);
            $stmt->bindParam(":currency", $param_currency, PDO::PARAM_STR);

            // Set parameters
            $param_clients_id = $this->clientId;
            $param_currency = $currency;

            // execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                exit("Statement failed. (checkExistingAccountCurrency)");
            }
        } else {
            exit("Prepared statement failed. (checkExistingAccountCurrency)");
        }
    }

    public function createNewAccount($currency)
    {
        $this->addNewAccountToDb($currency);
        $this->bindAccountToUser();
    }

    private function addNewAccountToDb($currency)
    {
        $sql = "INSERT INTO accounts (accounts_id, accounts_balance, accounts_currency, clients_id) VALUES (:accountNo, :balance, :currency, :clients_id);";
        $pdo = $this->connect();

        if (!$this->checkExistingAccountCurrency($currency)) {
            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":accountNo", $param_accountNo, PDO::PARAM_STR);
                $stmt->bindParam(":balance", $param_balance, PDO::PARAM_STR);
                $stmt->bindParam(":currency", $param_currency, PDO::PARAM_STR);
                $stmt->bindParam(":clients_id", $param_clients_id, PDO::PARAM_STR);

                // Set parameters
                $param_accountNo = $this->generateRandAccountNo();
                $param_balance = 0;
                $param_currency = $currency;
                $param_clients_id = $this->clientId;

                // execute the prepared statement
                if (!$stmt->execute()) {
                    exit("Statement failed. (addNewAccount)");
                }

                $this->accountNo = $param_accountNo;
                $this->balance = $param_balance;
                $this->currency = $currency;
            } else {
                exit("Prepared statement failed. (addNewAccount)");
            }
        } else {
            header("Location: ../../account-page.php?error=accountExists");
            exit();
        }
    }

    // Create a bond between account and user
    private function bindAccountToUser()
    {
        $sql = "UPDATE clients SET clients_mainAccountId = :accountNo WHERE clients_id = :clients_id;";
        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":accountNo", $param_accountNo, PDO::PARAM_STR);
            $stmt->bindParam(":clients_id", $param_clients_id, PDO::PARAM_STR);

            // Set parameters
            $param_accountNo = $this->accountNo;
            $param_clients_id = $this->clientId;

            // execute the prepared statement
            if (!$stmt->execute()) {
                exit("Statement failed. (addNewAccount)");
            }
        } else {
            exit("Prepared statement failed. Error in binding account to user.");
        }
    }
}
