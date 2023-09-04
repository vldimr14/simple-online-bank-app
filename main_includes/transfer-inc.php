<?php
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";

session_start();

$client = $_SESSION["client"];

if (isset($_POST["submit"])) {
    // Grab the data
    $senderAccountNo = $_POST["senderAccount"];
    $recipientAccountNo = $_POST["recipientAccount"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];
    $senderId = $client->getId();

    // Make a transfer
    require_once "../login_system/db_config-class.php";
    require_once "../classes/account-class.php";
    require_once "../classes/transaction-class.php";

    $transaction = new Transaction("", $description, $amount, $senderAccountNo, $recipientAccountNo, $senderId);
    $transaction->transfer();

    // Update transaction history array.
    $transactionHistory = [];
    array_push($transactionHistory, $transaction);
    // $_SESSION["transactionHistory"] = $transactionHistory;

    // redirect to user's account page
    header("Location: ../account-page.php?error=none");
} else {
    header("Location: ../account-page.php");
}
