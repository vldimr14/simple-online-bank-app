<?php
    session_start();

    include "templates/header.php";
?>

    <div class="contrainer">
        <form action="login_system/includes/signup-inc.php" method="post" class="col-4 offset-4 signup-form bg-dark">
        
        <div class="text-center text-light">
            <h2>Sign up</h2>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="firstName" id="firstName" placeholder="John">
            <label for="firstName" class="form-label">First name: </label>
        </div>
       
        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Doe">
            <label for="lastName" class="form-label">Last name: </label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="passportId" id="passportNo." placeholder="AQ123400">
            <label for="passportNo." class="form-label">Passport no.: </label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="email" id="email" placeholder="john.doe@mail.com">
            <label for="email" class="form-label">Email: </label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="uid" id="username" placeholder="johndoe">
            <label for="username" class="form-label">Username: </label>
        </div>

        <div class="form-floating mb-3">
            <input type="password" class="form-control" name="pwd" id="password" placeholder="password">
            <label for="password" class="form-label">Password: </label>      
        </div>

        <div class="form-floating mb-3">
            <input type="password" class="form-control" name="pwdRepeat" id="passwordRepeat" placeholder="Repeat password">
            <label for="passwordRepeat" class="form-label">Repeat password: </label>    
        </div>

        <div class="text-center">
            <input type="submit" class="btn btn-outline-light text-center" name="submit" value="Sign up">
        </div>
    </form>
    </div>

    <!-- Error block -->
    <div class="error-block text-center text-danger">
        <?php 
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "emptyInput") {
                    echo "Empty input.";
                } else if ($_GET["error"] == "invalidUid") {
                    echo "Invalid username.";
                } else if ($_GET["error"] == "invalidEmail") {
                    echo "Invalid email.";
                } else if ($_GET["error"] == "pwdsDontMatch") {
                    echo "Passwords don't match.";
                } else if ($_GET["error"] == "uidTaken") {
                    echo "Username already taken.";
                }
            }
        ?>
    </div>
    
<?php 
    include "templates/footer.php";
?>