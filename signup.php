<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Create an Account</h2>
    <form action="register.php" method="POST">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required placeholder="John Doe">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="********">

        <label for="user_type">User Type</label>
        <select id="user_type" name="user_type" required>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
        </select>

        <input type="submit" value="Sign Up">
    </form>
    <p>Already have an account? <a href="signin.php">Sign In</a></p>
</div>
<script src="js/main.js"></script>
</body>
</html>
