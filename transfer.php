<?php
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/client-class.php";
include "/Applications/XAMPP/xamppfiles/htdocs/online-bank-app/classes/account-class.php";

session_start();

include "templates/header.php";
require_once "login_system/db_config-class.php";

$client = $_SESSION["client"];
?>

<div class="container">
    <?php
    if (isset($_SESSION["loggedin"])) {
    ?>
        <form action="main_includes/transfer-inc.php" method="post" class="transfer-content">

            <div class="text-center">
                <h2>Transfer</h2>
            </div>

            <!-- Sender account select field -->
            <div class="form-floating mb-3">
                <select class="form-select" name="senderAccount">
                    <?php
                    $accounts = $client->getAccounts();
                    foreach ($accounts as $account) {
                    ?>
                        <option value="<?php echo $account->getAccountNo(); ?>"><?php echo $account->getAccountNo() . " " . $account->getCurrency(); ?></option>
                    <?php
                    };
                    ?>
                </select>

                <label for="senderAccount" class="form-label">From account: </label>
            </div>

            <!-- Recipient account input -->

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="recipientAccount" id="recipientAccount">
                <label for="recipientAccount" class="form-label">To account: </label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="description" id="description">
                <label for="description" class="form-label">Description: </label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="amount" id="amount">
                <label for="amount" class="form-label">Amount: </label>
            </div>

            <div class="text-center">
                <input type="submit" class="btn btn-outline-dark text-center" name="submit">
            </div>
        </form>

        <!-- Error block -->
        <div class="error-block text-center text-danger">
            <?php
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "emptyInput") {
                    echo "Empty input.";
                } else if ($_GET["error"] == "recipientAccountInvalidCurrency") {
                    echo "Recipient account currency doesn't match with sender account.";
                } else if ($_GET["error"] == "recipientAccountDoesntExists") {
                    echo "Recipient account doesn't exist.";
                } else if ($_GET["error"] == "insufficientFunds") {
                    echo "Insufficient funds.";
                } else if ($_GET["error"] == "invalidAmount") {
                    echo "Invalid amount.";
                }
            }
            ?>
        </div>
    <?php
    } else {
        header("Location: login.php");
    }
    ?>
</div>

<?php
include "templates/footer.php";
?>