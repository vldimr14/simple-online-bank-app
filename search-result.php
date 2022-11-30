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

        // check if accounts exists.
        if ($accounts[0]->getAccountNo() !== NULL) {
        ?>
            <!-- Page data in case if user is logged in and has an account. -->

            <!-- Shows all transaction history -->
            <div class="transaction-history">
                <h3 class="text-center"><?php echo htmlspecialchars($client->getFirstName()) . " " . htmlspecialchars($client->getLastName()) . "'s " ?>Transaction history</h3>

                <!-- search form -->
                <?php include "search-form.php" ?>

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
                                <th scope="col">Currency</th>
                                <th scope="col">Type</th>
                                <th scope="col">Sender Account No.</th>
                                <th scope="col">Recipient Account No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < count($transactionHistory); $i++) { ?>
                                <!-- change row color depend on income or outcome -->
                                <?php

                                $clientAccounts = $client->getAccounts();

                                if (isset($_GET["search"]) && $_GET["search"] !== "") {
                                    $searchQuery = $_GET["search"];

                                    // strpos() function checks if string contains another string inside. Use === or !== operators because of return value of this method.
                                    if (
                                        $transactionHistory[$i]->getId() == $searchQuery ||
                                        strpos($transactionHistory[$i]->getDescription(), $searchQuery) !== false ||
                                        $transactionHistory[$i]->getAmount() == $searchQuery ||
                                        $transactionHistory[$i]->getSenderAccountNo() == $searchQuery ||
                                        $transactionHistory[$i]->getRecipientAccountNo() == $searchQuery ||
                                        $transactionHistory[$i]->getType() == $searchQuery ||
                                        strpos($transactionHistory[$i]->getDate(), $searchQuery) !== false ||
                                        $transactionHistory[$i]->getCurrency() == $searchQuery
                                    ) {
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

                                                <td><?php echo $transactionHistory[$i]->getCurrency(); ?></td>
                                                <td><?php echo $transactionHistory[$i]->getType(); ?></td>
                                                <td><?php echo $transactionHistory[$i]->getSenderAccountNo(); ?></td>
                                                <td><?php echo $transactionHistory[$i]->getRecipientAccountNo(); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="text-center">
                        <a href="account-page.php" class="btn btn-outline-dark">Back</a>
                    </div>
            </div>

        <?php
        } else {
        ?>
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