<?php include('server.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ekrain Technologies & Solution (Pvt) Ltd - Login</title>
  <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="logo"></div>
        <h2>Login</h2>
        <form method="post" action="login.php">
            <?php include('errors.php'); ?>
            
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password">
            </div>
            <button type="submit" class="btn" name="login_user">Login</button>
            <p class="register-link">
                Not yet a member? <a href="register.php">Sign up</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
