<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['badge_number']) || $_SESSION['admin'] !== 1) {
    // Redirect the user to the login page if they are not logged in or not an admin
    header("Location: police_login.php");
    exit();
}
// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); // Replace with your database credentials
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Initialize variables for user management
$badge_number = "";
$password = "";
$edit_mode = false;

// Handle form submissions for creating and updating police accounts
if (isset($_POST['create'])) {
    $badge_number = $_POST['badge_number'];
    $password = $_POST['password'];

    // Check if the badge number already exists
    $check_sql = "SELECT * FROM police_login WHERE badge_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('s', $badge_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Badge Number Already Exists');</script>";
    } else {
        // Hash the password using SHA-256
        $hashed_password = hash('sha256', $password);

        // Insert the new police account into the database
        $insert_sql = "INSERT INTO police_login (badge_number, password, admin) VALUES (?, ?, 0)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param('ss', $badge_number, $hashed_password);

        if ($insert_stmt->execute()) {
            echo "<script>alert('Police Account Created Successfully');</script>";
        } else {
            echo "<script>alert('Error Creating Account');</script>";
        }
    }
}

if (isset($_POST['edit'])) {
    $edit_mode = true;
    $badge_number = $_POST['edit_badge_number'];
}

if (isset($_POST['update'])) {
    $badge_number = $_POST['edit_badge_number'];
    $new_password = $_POST['new_password'];

    // Hash the new password using SHA-256
    $hashed_password = hash('sha256', $new_password);

    // Update the password for the selected police account
    $update_sql = "UPDATE police_login SET password = ? WHERE badge_number = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ss', $hashed_password, $badge_number);

    if ($update_stmt->execute()) {
        echo "<script>alert('Account Updated Successfully');</script>";
        $edit_mode = false;
    } else {
        echo "<script>alert('Error Updating Account');</script>";
    }
}

if (isset($_POST['delete'])) {
    $badge_number = $_POST['delete'];

    // Delete the selected police account
    $delete_sql = "DELETE FROM police_login WHERE badge_number = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('s', $badge_number);

    if ($delete_stmt->execute()) {
        echo "<script>alert('Successfully Deleted Account');</script>";
    } else {
        echo "<script>alert('Error Deleting Account');</script>";
    }
}

// Retrieve all police accounts (excluding the admin account)
$sql = "SELECT badge_number FROM police_login WHERE admin = 0";
$result = $conn->query($sql);
$police_accounts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $police_accounts[] = $row['badge_number'];
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Police Dashboard</title>
    <style>
        /* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Global styles */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f0f0f0; /* Light gray background */
    font-size: 16px;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Header styles */
.header {
    background-color: #001f3f; /* Dark blue header background */
    color: #fff;
    padding: 20px 0;
    text-align: center;
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.logo img {
    width: 80px;
    height: 80px;
}

.nav-links {
    list-style: none;
    display: flex;
}

.nav-links li {
    margin-right: 20px;
}

.nav-links a {
    text-decoration: none;
    color: #fff;
    font-weight: 600;
    transition: color 0.3s ease;
}

.nav-links a:hover {
    color: #ff5722; /* Hover color */
}

/* Container styles */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Form styles */
.admin-section {
    background-color: #fff; /* White background */
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.admin-form input[type="text"],
.admin-form input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.admin-form button {
    background: linear-gradient(to bottom, #007BFF, #0056b3); /* Gradient button background */
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.admin-form button:hover {
    background: linear-gradient(to bottom, #0056b3, #003c80); /* Hover gradient effect */
}

/* Table styles */
.admin-table {
    border-collapse: collapse;
    width: 100%;
}

.admin-table th,
.admin-table td {
    border: 1px solid #ddd; /* Light gray border */
    padding: 12px;
    text-align: left;
}

.admin-table th {
    background-color: #007BFF; /* Table header background color */
    color: #fff;
}

/* Messages styles */
.error-message {
    color: #ff0000; /* Red error message */
    font-weight: bold;
}

.success-message {
    color: #00aa00; /* Green success message */
    font-weight: bold;
}

/* Footer styles */
.footer {
    background-color: #001f3f; /* Dark blue footer background */
    color: #fff;
    text-align: center;
    padding: 20px 0;
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
                <li><a href="index.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <section class="admin-section">
            <h2>Create a New Police Account</h2>
            <form method="POST" class="admin-form">
                <input type="text" name="badge_number" placeholder="Badge Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="create">Create</button>
            </form>

            <h2>Manage Police Accounts</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Badge Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($police_accounts as $badge) : ?>
                        <tr>
                            <td><?php echo $badge; ?></td>
                            <td>
                                <form method="POST" class="admin-form">
                                    <input type="hidden" name="edit_badge_number" value="<?php echo $badge; ?>">
                                    <button type="submit" name="edit">Edit Password</button>
                                    <button type="submit" name="delete" value="<?php echo $badge; ?>">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($edit_mode) : ?>
                <h2>Edit Password</h2>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="edit_badge_number" value="<?php echo $badge_number; ?>">
                    <input type="password" name="new_password" placeholder="New Password" required>
                    <button type="submit" name="update">Update Password</button>
                </form>
            <?php endif; ?>
        </section>
    </div>

    <footer class="footer">
        <!-- Footer content here -->
    </footer>
</body>
</html>
