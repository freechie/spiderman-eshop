<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <title>Spiderman: No Way Home Merch Store Registration</title>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

</head>
<body>
    <div class="login">
        <h1>Register</h1>
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
            <input type="submit" value="Register Account">
            <a type="button" href="index.php" class="register-btn">Back</a>
            <div>
            </div>
        </form>
    </div>
</body>
</html>
