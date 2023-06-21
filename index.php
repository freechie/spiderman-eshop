<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Spiderman: No Way Home Merch Store</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>

<body>
    <div class="intro">
        <h3>Log in to find the best Spiderman: No Way Home Merch below!</h3> <br>
        <img src="./img/intro_fan_art.PNG" class="intro-image" /> <br>
        <h3>More updates to functionality coming soon<br> Please check back again soon 👷🏾‍♀️🚧👷🏽‍♂️</h3>
    </div>
    <div class="login">
        <h1>Login</h1>
        <form action="authenticate.php" method="post">
            <label for="username">
                <i class="fas fa-user"></i>
            </label>
            <input type="text" name="username" placeholder="Username" id="username" required>
            <label for="password">
                <i class="fas fa-lock"></i>
            </label>
            <input type="password" name="password" placeholder="Password" id="password" required>
            <br><br>
            <input type="submit" value="Login">
            <a type="button" href="registration.php" class="register-btn">Register</a>
        </form>
    </div>

</body>

</html>