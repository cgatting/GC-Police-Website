<?php
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname'], $_POST['phone'])) {
        $password = $_POST['password'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $phone = $_POST['phone'];

        // Hash the password
        $hashed_password = hash('sha256', $password);

        // Database connection
        $conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); 
        if ($conn->connect_error) {
            die("Connection Failed : " . $conn->connect_error);
        } else {
            // Check if email already exists in the database
            $check_email_query = "SELECT email FROM users WHERE email = ?";
            $check_email_stmt = $conn->prepare($check_email_query);
            $check_email_stmt->bind_param('s', $email);
            $check_email_stmt->execute();
            $check_email_stmt->store_result();

            if ($check_email_stmt->num_rows > 0) {
                echo "<script>alert('Email already exists. Please use a different email.');</script>";
            } else {
                // Prepare and bind SQL statement
                $stmt = $conn->prepare("INSERT INTO users (password, email, first_name, last_name, phone_number) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $hashed_password, $email, $firstname, $lastname, $phone); 

                // Execute SQL statement
                if ($stmt->execute()) {
                    $_SESSION['email'] = $email;
                    header("Location: user_dashboard.php");
                    exit();
                } else {
                    echo "Registration failed. Please try again.";
                }

                // Close statement
                $stmt->close();
            }
            
            // Close connection
            $conn->close();
        }
    } else {
        echo "All fields are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
    
    <section class="registration">
        <h1>User Registration</h1>
        
        <!-- Registration Form -->
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="postcode">Postcode:</label>
                <input type="text" id="postcode" name="postcode" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
                <button type="submit">Register</button>
        </form>
    </section>
    
    <footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</body>
</html>

