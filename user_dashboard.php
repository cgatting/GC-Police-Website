<?php
session_start();

// Check if the user is not logged in, then redirect to the login page
if (!isset($_SESSION['email'])) {
    header("Location: user_login.php");
    exit();
}

// Retrieve user's email from the session
$email = $_SESSION['email'];

// Database connection
$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$dbName = 'Bikes';

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Fetch user-specific bikes from the database
$sql = "SELECT * FROM bikes WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* Your main CSS styles for header, logo, and navbar */
        /* Reset some default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Header styles */
        .header {
            background-color: #001f3f;
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
            color: #ff5722;
        }

        /* Dashboard styles */
        .dashboard {
            padding: 20px;
            text-align: center;
        }

        .dashboard-content {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .dashboard h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }

        .dashboard h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #555;
        }

        .dashboard ul {
            list-style: none;
            padding: 0;
        }

        .dashboard ul li {
            font-size: 18px;
            margin-bottom: 10px;
            color: #444;
        }

        .dashboard img {
            width: 200px;
            height: 200px;
        }

        /* Button styles */
        .cta-button, .logout-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 20px;
        }

        .cta-button:hover, .logout-button:hover {
            background-color: #0056b3;
        }

        /* Footer styles */
        .footer {
            text-align: center;
            background-color: #001f3f;
            color: #fff;
            padding: 20px 0;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="navbar">
        <div class="logo">
            <img src="image.png" alt="Logo">
        </div>

        <ul class="nav-links">
            <li><a href="index.html">Home Page</a></li>
            <li><a href="bike_reg.php">Register Bike</a></li>
            <li><a href="stolen_bike.php">Report Stolen Bike</a></li>
            <li><a href="police_login.php">Police Login</a></li>
        </ul>
    </div>
</div>
<section class="dashboard">
    <div class="dashboard-content">
        <h2>Your Registered Bikes:</h2>
        <ul>
            <?php foreach ($result as $bike): ?>
                <li>
                    <h3><?= htmlspecialchars($bike['manufacturer_part_number']); ?> - <?= htmlspecialchars($bike['brand']); ?></h3>
                    <a id="images" href="full_report.php?bike_id=<?= $bike['id']; ?>">
                        <img src="images/<?= htmlspecialchars($bike['image_name']); ?>" alt="<?= htmlspecialchars($bike['manufacturer_part_number']); ?>">
                    </a>
                    <!-- Display other bike information as needed -->
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="bike_reg.php" class="cta-button">Register New Bike</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</section>
</body>
<footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</html>
