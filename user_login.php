<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = hash('sha256', $password);
    $conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); 

    if ($conn->connect_error) {
        die("Connection Failed : " . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param('ss', $email, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Login successful, set session variable
            $_SESSION['email'] = $email;
            header("Location: user_dashboard.php");
            exit();
        } else {
            // Login failed
            echo "<script>alert('Login failed. Please check your email and password.');</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <a href="subpage.html">
                    <img src="image.png" alt="Gloucestershire Constabulary Logo">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a id="register-bike-link" href="subpage.html">Register Bike</a></li>
                <li><a href="subpage.html">Report Stolen Bike</a></li>
                <li><a href="police_login.php">Police Login</a></li>
            </ul>
        </nav>
    </header>
    
    <section class="login">
        <div class="login-form">
            <h2>User Login</h2>
            <form action="user_login.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">    
                    <button type="submit" class="submit-button">Login</button>    
                </div>
                <div class="form-group">
                    <button onclick="location.href='forgot_password.php';">Forgot Password?</button>
                </div>

            </form>
        </div>
    </section>
    
    <footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</body>
</html>

