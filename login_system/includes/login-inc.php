<?php

    if (isset($_POST["submit"])) {

        // Grab the data
        $uid = $_POST["uid"];
        $pwd = $_POST["pwd"];

        // Log in
        require_once "../db_config-class.php";
        require_once "../login-class.php";

        $login = new Login($uid, $pwd);

        $login->loginUser();

        // redirect to home page
        header("location: ../../account-page.php?error=none");

    } else {
        header("Location: ../../index.php");
    }