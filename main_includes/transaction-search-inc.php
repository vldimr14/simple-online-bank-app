<?php
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/account-class.php";
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/transaction-class.php";

session_start();

$client = $_SESSION["client"];
$transactionHistory = Transaction::getTransactionHistory($client);
$_SESSION["transactionHistory"] = $transactionHistory;

if (isset($_POST["submit"])) {
    // Grab the data
    $searchQuery = $_POST["searchQuery"];

    // Find a transaction
    require_once "../login_system/db_config-class.php";
    require_once "../classes/account-class.php";
    require_once "../classes/transaction-class.php";

    // $searchResult = Transaction::findTransaction($client, $searchQuery);

    // Create new superglobal which will store search result.
    // $_SESSION["searchQuery"] = $searchQuery;

    // redirect to transaction-history page
    header("Location: ../search-result.php?search=$searchQuery");
} else {
    header("Location: ../account-page.php");
}
