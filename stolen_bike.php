<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page if they are not logged in
    header("Location: user_login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_');  // Replace with your database credentials
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Retrieve bike details based on the bike_id parameter in the URL
if (isset($_GET['bike_id'])) {
    $bike_id = $_GET['bike_id'];
    
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
        $color = $row['color'];
        $bikeimage = $row['image_name'];
    } else {
        // Bike not found
        echo "Bike not found.";
        exit();
    }
} else {
    // Invalid request
    echo "Invalid request.";
    exit();
}

// Handle the form submission to report the bike as stolen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['additionalDetails'])) {
    $additionalDetails = $_POST['additionalDetails'];
    $email = $_SESSION['email'];
    
    // Get user's information from the users table using the session email
    $sql_user = "SELECT first_name, last_name, phone_number FROM users WHERE email = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param('s', $email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    
    if ($userDetails = $result_user->fetch_assoc()) {
        $firstName = $userDetails['first_name'];
        $lastName = $userDetails['last_name'];
        $phoneNumber = $userDetails['phone_number'];
        $status = "Reported"; // Set the status as "Reported"

        // Check if a bike with the same manufacturer part number already exists
        $sql_check = "SELECT id FROM stolen_bikes WHERE manufacturer_part_number = ? LIMIT 1";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param('s', $manufacturerPartNumber);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Bike with the same manufacturer part number already exists, update the existing entry
            $sql_update = "UPDATE stolen_bikes SET additional_information = ?, status = ? WHERE manufacturer_part_number = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('sss', $additionalDetails, $status, $manufacturerPartNumber);

            if ($stmt_update->execute()) {
                // Stolen bike updated successfully
                echo "<script>alert('Stolen bike information updated successfully.'); window.location.href = 'user_dashboard.php';</script>";
            } else {
                // Error updating stolen bike information
                echo "<script>alert('Error updating stolen bike information. Please try again later.');</script>";
            }
            $stmt_update->close();
        } else {
            // Bike with the given manufacturer part number does not exist, insert a new entry
            $sql_insert = "INSERT INTO stolen_bikes (manufacturer_part_number, brand, model, color, additional_information, user_email, status, user_phone_number, user_first_name, user_last_name, bike_image) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param('sssssssssss', $manufacturerPartNumber, $brand, $model, $color, $additionalDetails, $email, $status, $phoneNumber, $firstName, $lastName, $bikeimage);

            if ($stmt_insert->execute()) {
                // Stolen bike reported successfully
                echo "<script>alert('Bike reported as stolen successfully.'); window.location.href = 'user_dashboard.php';</script>";
            } else {
                // Error in reporting stolen bike
                echo "<script>alert('Error reporting stolen bike. Please try again later.');</script>";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    } else {
        // User details not found
        echo "User details not found.";
    }
    $stmt_user->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Stolen Bike</title>
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
            display: flex;
            justify-content: space-between; /* Logo on left, navigation menu on right */
            align-items: center;
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

        .report-content textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .report-content img {
            display: block;
            margin: 0 auto;
            max-width: 100%; /* Ensure the image doesn't exceed its container's width */
            height: auto; /* Maintain the aspect ratio of the image */
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
        <div class="navbar">
            <div class="logo">
                <img src="image.png" alt="Gloucestershire Constabulary Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="user_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>
    <section class="report-content">
        <h1>Report Stolen Bike</h1>
        <form method="POST">
            <ul>
                <li><strong>Manufacturer Part Number:</strong> <?php echo $manufacturerPartNumber; ?></li>
                <li><strong>Brand:</strong> <?php echo $brand; ?></li>
                <li><strong>Model:</strong> <?php echo $model; ?></li>
                <li><strong>Bike Type:</strong> <?php echo $bikeType; ?></li>
                <li><strong>Color:</strong> <?php echo $color; ?></li>
                <li><strong>Additional Details:</strong></li>
                <img src="/images/<?php echo $bikeimage; ?>" alt="Bike Image">

                <li>
                    <textarea name="additionalDetails" rows="4" cols="50" placeholder="Enter additional details here"></textarea>
                </li>
            </ul>
            <button type="submit" class="cta-button" name="reportStolen">Report Stolen</button>
        </form>
    </section>
    <footer class="footer">
        &copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen
    </footer>
</body>
</html>
