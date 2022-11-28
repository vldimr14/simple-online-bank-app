<?php
require_once "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- css links -->
    <link rel="stylesheet" href="styles.css">
    <!-- Bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <style>
        .signup-form {
            margin-top: 30px;
            padding: 15px;
            border-radius: 20px;
        }

        .account-info {
            width: 36%;
            margin-right: 4%;
            border: 1px solid black;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 4%;
        }

        .transaction-history {
            width: 100%;
            border: 1px solid black;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 4%;
        }

        .transfer-content {
            width: 60%;
            margin: 30px auto;
            border: 1px solid black;
            border-radius: 20px;
            padding: 20px;
        }
    </style>

    <title>SBA Bank</title>
</head>

<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg bg-dark sticky-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center">
                <div class="navbar-nav">
                    <a href="index.php" class="navbar-brand text-light">SBA Bank</a>
                    <a href="index.php" class="nav-link text-light">Home</a>
                    <a href="contact.php" class="nav-link text-light">Contact</a>

                    <!-- Show user basic data if user is logged in -->
                    <?php
                    if (isset($_SESSION["loggedin"])) {
                    ?>
                        <a href="account-page.php" class="nav-link text-light">
                            <?php echo htmlspecialchars($_SESSION["client"]->getFirstName()) . " " . htmlspecialchars($_SESSION["client"]->getLastName()); ?>
                        </a>

                        <!-- Log out -->
                        <a href="login_system/includes/logout-inc.php" class="nav-link text-light">Log out</a>
                    <?php
                    } else {
                    ?>
                        <a href="signup.php" class="nav-link text-light">Sign up</a>
                        <a href="login.php" class="nav-link text-light">Log in</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>