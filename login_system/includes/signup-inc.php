<?php 

    if (isset($_POST["submit"])) {
        
        // Grab the data
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $passportId = $_POST["passportId"];
        $email = $_POST["email"];
        $uid = $_POST["uid"];
        $pwd = $_POST["pwd"]; 
        $pwdRepeat = $_POST["pwdRepeat"];

        // Sign up
        require_once "../db_config-class.php";
        require_once "../signup-class.php";
        $signup = new Signup($firstName, $lastName, $passportId, $email, $uid, $pwd, $pwdRepeat);

        $signup->signupUser();

        // redirect to home page
        header("location: ../../index.php?error=none");
    } else {
        header("Location: ../../index.php");
    }