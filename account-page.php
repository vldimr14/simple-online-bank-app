<?php
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/account-class.php";
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/transaction-class.php";

session_start();

include "templates/header.php";
require_once "login_system/db_config-class.php";

if (isset($_SESSION["loggedin"])) {
    $client = $_SESSION["client"];
    Client::refreshClientInfo($client);
    $transactionHistory = Transaction::getTransactionHistory($client);
    $_SESSION["transactionHistory"] = $transactionHistory;
?>

    <div class="container">

        <?php
        if (isset($_SESSION["loggedin"])) {
        ?>
            <p>Logged in as <?php echo htmlspecialchars($_SESSION["client"]->getFirstName()) . " " . htmlspecialchars($_SESSION["client"]->getLastName()); ?></p>
        <?php
        } else {
            header("Location: login.php");
            exit();
        }
        ?>

        <?php
        $accounts = $client->getAccounts();

        // Test the accounts
        // echo $accounts[0]->getAccountNo() . " ";
        // echo " balance: " . $accounts[0]->getBalance() . " ";
        // echo " client id: " . $accounts[0]->getClientId() . " ";
        // echo " currency: " . $accounts[0]->getCurrency() . " ";

        // check if accounts exists.
        if ($accounts[0]->getAccountNo() !== NULL) {
        ?>
            <!-- Page data in case if user is logged in and has an account. -->

            <div class="account-info">
                <h2><?php echo htmlspecialchars($_SESSION["client"]->getFirstName()) . " " . htmlspecialchars($_SESSION["client"]->getLastName()); ?></h2>
                <h4>Account No.: <?php echo $accounts[0]->getAccountNo(); ?></h4>
                <h4>Balance: <?php echo $accounts[0]->getBalance() . " " . $accounts[0]->getCurrency(); ?></h4>

                <div class="account-functions">
                    <a class="btn btn-outline-dark" href="transfer.php">Transer</a>
                </div>
            </div>

            <!-- Shows 10 last transactions -->
            <div class="transaction-history">
                <h3>Transaction history (10 Latest)</h3>

                <?php

                if (isset($_SESSION["transactionHistory"])) {
                    $transactionHistory = $_SESSION["transactionHistory"];
                ?>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Date</th>
                                <th scope="col">Description</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Type</th>
                                <th scope="col">Sender Account No.</th>
                                <th scope="col">Recipient Account No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 10 && $i < count($transactionHistory); $i++) { ?>
                                <!-- change row color depend on income or outcome -->
                                <?php

                                $clientAccounts = $client->getAccounts();

                                foreach ($clientAccounts as $account) {
                                    if ($transactionHistory[$i]->getSenderAccountNo() != $account->getAccountNo()) {

                                ?>
                                        <tr class="table-success">
                                        <?php
                                    } else {
                                        ?>
                                        <tr class="table-danger">
                                        <?php
                                    }
                                        ?>

                                        <th scope="row"><?php echo $transactionHistory[$i]->getId(); ?></th>
                                        <td><?php echo $transactionHistory[$i]->getDate(); ?></td>
                                        <td><?php echo htmlspecialchars($transactionHistory[$i]->getDescription()); ?></td>

                                        <!-- change +/- sign depending on income or outcome -->
                                        <?php
                                        if ($transactionHistory[$i]->getSenderAccountNo() != $account->getAccountNo()) {

                                        ?>
                                            <td>+<?php echo $transactionHistory[$i]->getAmount(); ?></td>
                                        <?php
                                        } else {
                                        ?>
                                            <td>-<?php echo $transactionHistory[$i]->getAmount(); ?></td>
                                        <?php
                                        }

                                        ?>

                                        <td><?php echo $transactionHistory[$i]->getType(); ?></td>
                                        <td><?php echo $transactionHistory[$i]->getSenderAccountNo(); ?></td>
                                        <td><?php echo $transactionHistory[$i]->getRecipientAccountNo(); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="text-center">
                        <a href="transaction-history.php" class="btn btn-outline-dark">More</a>
                    </div>
            </div>

        <?php
        } else {
        ?>
            <!-- Page data in case if user is logged in and doesn't have an account. -->
            <div class="newAccount">
                <form action="main_includes/add-account-inc.php" method="post">
                    <select class="form-select" name="currency">
                        <option selected value="PLN">PLN</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>

                    <input type="submit" name="submit" value="Add new account" class="btn btn-outline-dark">
                </form>
            </div>

            <!-- Error block -->
            <div class="error-block text-center text-danger">
                <?php

                if (isset($_GET["error"])) {
                    if ($_GET["error"] == "accountExists") {
                        echo "Account with the chosen currency already exists. Please choose another currency.";
                    }
                }

                ?>
            </div>

        <?php
        }
        ?>

    </div>

    <?php
    include "templates/footer.php";
    ?>

<?php
} else {
    header("Location: login.php");
}

?>