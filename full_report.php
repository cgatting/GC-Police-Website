<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page if they are not logged in
    header("Location: user_login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); 
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Check if the bike_id parameter is set
if (isset($_GET['bike_id'])) {
    $bike_id = $_GET['bike_id'];
    
    // Retrieve bike details from bikes table
    $sql = "SELECT * FROM bikes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $bike_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch bike details
    if ($row = $result->fetch_assoc()) {
        $manufacturerPartNumber = $row['manufacturer_part_number'];
        $brand = $row['brand'];
        $model = $row['model'];
        $bikeType = $row['bike_type'];
        $wheelSize = $row['wheel_size'];
        $color = $row['color'];
        $numOfGears = $row['num_of_gears'];
        $brakeType = $row['brake_type'];
        $suspension = $row['suspension'];
        $gender = $row['gender'];
        $age = $row['age_group'];
        $image_name = $row['image_name'];
    } else {
        // Bike not found
        echo "Bike not found.";
        exit();
    }
    
    // Retrieve status from stolen_bikes table
    $sql_stolen = "SELECT status FROM stolen_bikes WHERE manufacturer_part_number = ?";
    $stmt_stolen = $conn->prepare($sql_stolen);
    $stmt_stolen->bind_param('i', $manufacturerPartNumber);
    $stmt_stolen->execute();
    $result_stolen = $stmt_stolen->get_result();

    // Fetch bike status
    if ($row_stolen = $result_stolen->fetch_assoc()) {
        $status = $row_stolen['status'];
    } else {
        $status = "Unreported"; // Default status if not found in stolen_bikes table
    }

    // Close the stolen bikes query
    $stmt_stolen->close();
    
    // Close the bikes query
    $stmt->close();
} else {
    // Invalid request
    echo "Invalid request.";
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bike Full Report</title>
    <style>
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

        /* Report content styles */
        .report-content {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .report-content h1 {
            font-size: 36px;
            color: #333;
            margin-top: 0;
        }

        .report-content h2 {
            color: #333;
        }

        .report-content ul {
            list-style-type: none;
            padding: 0;
        }

        .report-content li {
            font-size: 18px;
            margin-bottom: 10px;
            color: #444;
        }

        .report-content img {
            width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 20px;
        }

        /* Button styles */
        .cta-button {
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

        .cta-button:hover {
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
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image.png" alt="Gloucestershire Constabulary Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="./bike_reg.php">Register Bike</a></li>
                <li><a href="./user_dashboard.php">Report Stolen Bike</a></li>
                <li><a href="police_login.php">Police Login</a></li>
            </ul>
        </nav>
    </header>
    <section class="report-content">
        <h1>Full Report for Bike: <?php echo $manufacturerPartNumber; ?></h1>
        <ul>
            <li><strong>Manufacturer Part Number:</strong> <?php echo $manufacturerPartNumber; ?></li>
            <li><strong>Brand:</strong> <?php echo $brand; ?></li>
            <li><strong>Model:</strong> <?php echo $model; ?></li>
            <li><strong>Bike Type:</strong> <?php echo $bikeType; ?></li>
            <li><strong>Wheel Size:</strong> <?php echo $wheelSize; ?> inches</li>
            <li><strong>Color:</strong> <?php echo $color; ?></li>
            <li><strong>Number of Gears:</strong> <?php echo $numOfGears; ?></li>
            <li><strong>Brake Type:</strong> <?php echo $brakeType; ?></li>
            <li><strong>Suspension:</strong> <?php echo $suspension; ?></li>
            <li><strong>Gender:</strong> <?php echo $gender; ?></li>
            <li><strong>Age Group:</strong> <?php echo $age; ?></li>
            <li><strong>Report Status:</strong><?php echo $status;?></li>

        </ul>
        <img src="images/<?php echo $image_name; ?>" alt="<?php echo $manufacturerPartNumber; ?>">
        <!-- Report bike button -->
        <!-- Hide "Report Bike as Stolen" button if status is "Reported" or "Not Recovered" -->
        <?php if ($status != "Reported" && $status != "Not Recovered"): ?>
            <a href="stolen_bike.php?bike_id=<?php echo $bike_id; ?>" class="cta-button">Report Bike as Stolen</a>
        <?php endif; ?>

        <!-- Always display "Unregister Bike" button -->
        <a href="unregister_bike.php?man_no=<?php echo $manufacturerPartNumber; ?>" class="cta-button">Unregister Bike</a>

    </section>
    <footer class="footer">
        &copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen
    </footer>
</body>
</html>
