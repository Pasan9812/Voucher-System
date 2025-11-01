<?php include('server.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ekrain Technologies & Solution (Pvt) Ltd - Register</title>
  <link rel="stylesheet" type="text/css" href="register.css">
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <div class="logo"></div>
        <h2>Create Your Account</h2>
        
        <form method="post" action="register.php">
            <?php include('errors.php'); ?>

            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Enter username">
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>" placeholder="Enter email">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password_1" placeholder="Enter password">
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="password_2" placeholder="Re-enter password">
            </div>

            <button type="submit" class="btn" name="reg_user">Register</button>
            
            <p class="login-link">
                Already a member? <a href="login.php">Sign in</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
