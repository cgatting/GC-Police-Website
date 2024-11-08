<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $email = $_POST['email'];
        $new_password = $_POST['new_password'];
        // Hash the new password
        $hashed_password = hash('sha256', $new_password);

        // Update the password in the database for the user with the provided email
        $conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_');  // Update with your database credentials

        if ($conn->connect_error) {
            die("Connection Failed : " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $hashed_password, $email);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            echo "<script>alert('Email not found');</script>";
            echo "<script>window.location.href = 'forgot_password.php';</script>";


        } else {
            // Display success alert
            echo "<script>alert('Your password has been reset. Please log in with your new password.');</script>";
            // Redirect
            echo "<script>window.location.href = 'user_login.php';</script>";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
        
        exit(); // Terminate script after redirect
    }
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure you have the appropriate styling -->
    <style>
        /* Additional styles for the Forgot Password page */
        .password-reset {
            padding: 40px 0;
            text-align: center;
        }

        .password-reset-form {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.2s;
            margin-bottom: 10px;
        }

        .form-group input[type="email"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #333;
        }

        .submit-button {
            display: block; /* Changed to block to occupy full width */
            width: 100%; /* Occupy full width */
            padding: 12px 0; /* Adjust padding */
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }

    </style>
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

    <section class="password-reset">
        <div class="password-reset-form">
            <h2>Forgot Password</h2>
            <p>Please enter your email address and new password to reset your password.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">    
                    <button type="submit" class="submit-button">Reset Password</button>
                </div>
            </form>
            
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</body>
</html>
