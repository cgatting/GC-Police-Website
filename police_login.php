<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_');  // Replace with your database credentials
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $badge_number = $_POST['badge_number'];
    $password = $_POST['password'];
    $hashed_password = hash('sha256', $password); // Hash the input password using SHA-256
    // Query to fetch the stored password hash and admin status for the given badge number
    $sql = "SELECT password, admin FROM police_login WHERE badge_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $badge_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password_hash = $row['password']; // Fetch the stored hashed password
        $hashed_password = trim($hashed_password);
        $stored_password_hash = trim($stored_password_hash);
        // Direct comparison of hashed passwords
        if ($hashed_password === $stored_password_hash) {
            // Password is correct, log in the user
            session_start();
            $_SESSION['badge_number'] = $badge_number;
            $_SESSION['admin'] = $row['admin']; // Store admin status in the session
            if ($_SESSION['admin'] == 1) {
                header("Location: admin_police_dashboard.php"); // Redirect to admin dashboard
            } else {
                header("Location: police_dashboard.php"); // Redirect to normal police dashboard
            }
            exit();
        } else {
            echo "<script>alert('Incorrect Password');</script>";
        }
    } else {
        echo "<script>alert('Badge number not found.');</script>";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image.png" alt="Gloucestershire Constabulary Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="subpage.html">Register Bike</a></li>
                <li><a href="subpage.html">Report Stolen Bike</a></li>
            </ul>
        </nav>
    </header>
    <section class="login">
        <div class="login-form">
            <h2>Police Login</h2>
            <form action="police_login.php" method="post">
                <div class="form-group">
                    <label for="badge_number">Badge Number</label>
                    <input type="text" id="badge_number" name="badge_number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-button" name="login">Log In</button>
                </div>
            </form>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</body>
</html>

