<?php
require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/login_system/db_config-class.php";
require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/account-class.php";

class Transaction extends DbHandler
{

    private $id;
    private $description;
    private $amount;
    private $senderAccountNo;
    private $recipientAccountNo;
    private $senderId;
    private $type;
    private $senderCardNo = "";
    private $date; // in use only when I request the data from the database.
    private $currency;

    private $senderAccount; // Account object.

    public function __construct($id, $description, $amount, $senderAccountNo, $recipientAccountNo, $senderId)
    {
        $this->id = $id;
        $this->description = $description;
        $this->amount = $amount;
        $this->senderAccountNo = $senderAccountNo;
        $this->recipientAccountNo = $recipientAccountNo;
        $this->senderId = $senderId;
        $this->type = "transfer";   // for now i didn't implement card transactions. 
        $this->senderCardNo = 0;    // Only transfers for now.
        $this->currency = null;

        $this->senderAccount = new Account($senderId, $senderAccountNo); // get necessary sender account info to process transfer.
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getSenderAccountNo()
    {
        return $this->senderAccountNo;
    }

    public function getRecipientAccountNo()
    {
        return $this->recipientAccountNo;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    // get transaction information from database.
    public static function getTransactionHistory($client)
    {
        $transactionHistory = [];

        // Using left join to get accounts currency.
        foreach ($client->getAccounts() as $account) {
            $sql = "SELECT transactions_id, transactions_date, transactions_description, transactions_amount, accounts.accounts_currency, transactions_senderAccountId, transactions_recipientAccountId, transactions_senderId FROM transactions 
                    INNER JOIN accounts ON transactions.transactions_senderId = accounts.clients_id 
                    WHERE transactions_senderAccountId = :accountId OR transactions_recipientAccountId = :accountId 
                    ORDER BY transactions_date DESC;";
            $pdo = $client->connect();

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":accountId", $param_accountId, PDO::PARAM_STR);

                // Set parameters
                $param_accountId = $account->getAccountNo();

                // execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        $transactionInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        for ($i = 0; $i < $stmt->rowCount(); $i++) {
                            $transaction = new Transaction(
                                $transactionInfo[$i]["transactions_id"],
                                $transactionInfo[$i]["transactions_description"],
                                $transactionInfo[$i]["transactions_amount"],
                                $transactionInfo[$i]["transactions_senderAccountId"],
                                $transactionInfo[$i]["transactions_recipientAccountId"],
                                $transactionInfo[$i]["transactions_senderId"]
                            );

                            $transaction->setDate($transactionInfo[$i]["transactions_date"]);
                            $transaction->setCurrency($transactionInfo[$i]["accounts_currency"]);

                            array_push($transactionHistory, $transaction);
                        }
                    } else {
                        $transactionHistory = null;
                    }
                } else {
                    exit("Statement failed. (getTransactionHistory)");
                }
            } else {
                exit("Prepared statement failed. (getTransactionInfo)");
            }
        }

        return $transactionHistory;
    }

    // Generate a random string, using a cryptographically secure 
    // pseudorandom number generator (random_int)
    private function generateRandTransactionNo(): string
    {
        $length = 30;
        $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

        // if ($length < 1) {
        //     throw new \RangeException("Length must be a positive integer");
        // }

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        // join array elements with a string.
        $newTransactionNo = implode('', $pieces);

        if (!$this->checkTransaction($newTransactionNo)) {
            return $newTransactionNo;
        } else {
            $this->generateRandTransactionNo();
        }
    }

    public function checkTransaction($transactionNo)
    {
        $sql = "SELECT transactions_id FROM transactions WHERE transactions_id = :transactionNo;";
        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":transactionNo", $param_transactionNo, PDO::PARAM_STR);

            // Set parameters
            $param_transactionNo = $transactionNo;

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

    private function validate()
    {
        if ($this->emptyInput()) {
            header("Location: ../transfer.php?error=emptyInput");
            exit();
        } else if ($this->invalidAmount()) {
            header("Location: ../transfer.php?error=invalidAmount");
            exit();
        }
    }

    private function emptyInput()
    {
        if (empty($this->recipientAccountNo) || empty($this->description) || empty($this->amount)) {
            return true;
        } else {
            return false;
        }
    }

    private function invalidAmount()
    {
        if (!preg_match("/^[0-9]+(?:\.[0-9]+)?$/", $this->amount)) {
            return true;
        } else {
            return false;
        }
    }

    public function transfer()
    {
        // validate form
        $this->validate();

        // prepare a sql query.
        $sql = "INSERT INTO transactions (transactions_id, transactions_description, transactions_amount, transactions_senderAccountId, transactions_senderId, transactions_recipientAccountId, transactions_type, transactions_senderCardNo) VALUES (:transactionsNo, :transactions_description, :transactions_amount, :transactions_senderAccountNo, :transactions_senderId, :transactions_recipientAccountNo, :transactions_type, :transactions_senderCardNo);";
        $pdo = $this->connect();

        // check if sender account has the required funds.
        if (!($this->senderAccount->getBalance() < intval($this->amount))) {

            // check if recipient account exists and it's not the sender account.
            if ($this->senderAccount->checkAccount($this->recipientAccountNo) && $this->senderAccountNo !== $this->recipientAccountNo) {
                // check if recipient account has the same currency as sender account.
                if ($this->senderAccount->getCurrency() == $this->senderAccount->checkAccountCurrency($this->recipientAccountNo)) {
                    if ($stmt = $pdo->prepare($sql)) {
                        // Bind variables to the prepared statement as parameters
                        $stmt->bindParam(":transactionsNo", $param_transactionNo, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_description", $param_description, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_amount", $param_amount, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_senderAccountNo", $param_senderAccountNo, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_senderId", $param_senderId, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_recipientAccountNo", $param_recipientAccountNo, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_type", $param_type, PDO::PARAM_STR);
                        $stmt->bindParam(":transactions_senderCardNo", $param_senderCardNo, PDO::PARAM_STR);

                        // Set parameters
                        $param_transactionNo = $this->generateRandTransactionNo();
                        $param_description = $this->description;
                        $param_amount = $this->amount;
                        $param_senderAccountNo = $this->senderAccountNo;
                        $param_senderId = $this->senderId;
                        $param_recipientAccountNo = $this->recipientAccountNo;
                        $param_type = $this->type;
                        $param_senderCardNo = $this->senderCardNo;

                        // execute the prepared statement
                        if (!$stmt->execute()) {
                            exit("Statement failed. (createNewTransaction)");
                        }

                        // update sender account
                        $this->updateSenderAccount();

                        // update recipient account
                        $this->updateRecipientAccount();

                        // reload current client object
                        $client = $_SESSION["client"];
                        Client::refreshClientInfo($client);
                    } else {
                        exit("Prepared statement failed. (createNewTransaction)");
                    }
                } else {
                    header("Location: ../transfer.php?error=recipientAccountInvalidCurrency");
                    exit();
                }
            } else {
                header("Location: ../transfer.php?error=recipientAccountDoesntExists");
                exit();
            }
        } else {
            header("Location: ../transfer.php?error=insufficientFunds");
            exit();
        }
    }

    private function updateSenderAccount()
    {
        $sql = "UPDATE accounts SET accounts_balance = accounts_balance - :amount WHERE accounts_id = :senderAccountNo";

        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt->bindParam(":senderAccountNo", $param_senderAccountNo, PDO::PARAM_STR);

            // Set parameters
            $param_amount = $this->amount;
            $param_senderAccountNo = $this->senderAccountNo;

            // execute the prepared statement
            if (!$stmt->execute()) {
                exit("Statement failed. (updateSenderAccount)");
            }
        } else {
            exit("Prepared statement failed. (updateSenderAccount)");
        }
    }

    private function updateRecipientAccount()
    {
        $sql = "UPDATE accounts SET accounts_balance = accounts_balance + :amount WHERE accounts_id = :recipientAccountNo";

        $pdo = $this->connect();

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt->bindParam(":recipientAccountNo", $param_recipientAccountNo, PDO::PARAM_STR);

            // Set parameters
            $param_amount = $this->amount;
            $param_recipientAccountNo = $this->recipientAccountNo;

            // execute the prepared statement
            if (!$stmt->execute()) {
                exit("Statement failed. (updateRecipientAccount)");
            }
        } else {
            exit("Prepared statement failed. (updateRecipientAccount)");
        }
    }
}
