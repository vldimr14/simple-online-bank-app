<?php 
    session_start();

    include "templates/header.php";
?>

    <div class="contrainer">
        <form action="login_system/includes/login-inc.php" method="post" class="col-4 offset-4 signup-form bg-dark">
        
        <div class="text-center text-light">
            <h2>Log in</h2>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="uid" id="username" placeholder="johndoe">
            <label for="username" class="form-label">Username or email: </label>
        </div>

        <div class="form-floating mb-3">
            <input type="password" class="form-control" name="pwd" id="password" placeholder="password">
            <label for="password" class="form-label">Password: </label>      
        </div>

        <div class="text-center">
            <input type="submit" class="btn btn-outline-light text-center" name="submit" value="Log in">
        </div>
    </form>
    </div>

    <!-- Error block -->
    <div class="error-block text-center text-danger">
        <?php 
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "emptyInput") {
                    echo "Empty input.";
                } else if ($_GET["error"] == "invalidUidOrPwd") {
                    echo "Invalid login or password.";
                }
            }
        ?>
    </div>
    
<?php 
    include "templates/footer.php";
?>