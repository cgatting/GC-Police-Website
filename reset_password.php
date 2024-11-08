<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate if new password matches confirm password
    if ($new_password !== $confirm_password) {
        // Passwords don't match, handle error (redirect back to the form or display an error message)
        // For example:
        header("Location: forgot_password.php?error=password_mismatch");
        exit();
    }

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

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Display an alert and redirect to user_login.php
    echo "<script>alert('Your password has been reset. Please log in with your new password.');</script>";
    header("Location: user_login.php");
    exit();
}
?>
