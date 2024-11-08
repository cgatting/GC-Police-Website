<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page if they are not logged in
    header("Location: user_login.php");
    exit();
}

// Check if the bike_id parameter is set
if (isset($_GET['man_no'])) {
    $bike_id = $_GET['man_no'];

    // Database connection
    $conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); 
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to delete the bike from the bikes table
    $sql_delete = "DELETE FROM stolen_bikes WHERE manufacturer_part_number = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('i', $bike_id);

    // Execute the delete statement
    if ($stmt_delete->execute()) {
        // Bike unregistered successfully
        echo "<script>alert('Bike unregistered successfully.'); window.location='user_dashboard.php';</script>";
    } else {
        // Error occurred while unregistering the bike
        echo "<script>alert('Error: " . $stmt_delete->error . "'); window.location='full_report.php';</script>";
    }

    // Close the statement and database connection
    $stmt_delete->close();
    $conn->close();
} else {
    // Invalid request
    echo "<script>alert('Invalid request.'); window.location='user_dashboard.php';</script>";
}
?>
