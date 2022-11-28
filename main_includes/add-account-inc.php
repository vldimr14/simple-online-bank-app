<?php
    include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/account-class.php";
    include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";
   
    session_start();

    $client = $_SESSION["client"];

    if (isset($_POST["submit"])) {
        // Grab the data
        $currency = $_POST["currency"];

        // Add new account
        require_once "../login_system/db_config-class.php";
        require_once "../classes/account-class.php";

        $newAccount = new Account($client->getId(), null);
        $newAccount->createNewAccount($currency);

        // Update client object.
        $client->setAccounts($newAccount);
        $_SESSION["client"] = $client;

        // redirect to user's account page
        header("Location: ../account-page.php?error=none");
    } else {
        header("Location: account-page.php");
    }