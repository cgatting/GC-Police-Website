<?php
// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); 
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if (isset($_GET['bike_id'])) {
    $bike_id = $_GET['bike_id'];

    // Query to fetch bike details based on bike_id
    $sql = "SELECT * FROM stolen_bikes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $bike_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Bike details
        $manufacturerPartNumber = $row['manufacturer_part_number'];
        $brand = $row['brand'];
        $model = $row['model'];
        $color = $row['color'];
        $additionalInformation = $row['additional_information'];
        $bikeImage = $row['bike_image']; // Bike image URL

        // Define HTML and CSS using heredoc syntax
        $content = <<<HTML
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Bike Report</title>
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
            justify-content: space-between;
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
            margin: 90px auto;
        }

        .report-content h1 {
            font-size: 36px;
            color: #333;
            margin-top: 0;
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
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
        }

        /* Footer styles */
        .footer {
            text-align: center;
            background-color: #001f3f;
            color: #fff;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header class='header'>
        <div class='navbar'>
            <div class='logo'>
                <img src='image.png' alt='Gloucestershire Constabulary Logo'>
            </div>
            <ul class='nav-links'>
                <li><a href='index.html'>Home</a></li>
                <li><a href='police_dashboard.php'>Police Dashboard</a></li>
                <li><a href='logout.php'>Logout</a></li>
            </ul>
        </div>
    </header>
    <section class='report-content'>
        <h1>Bike Report</h1>
        <ul>
            <li><strong>Manufacturer Part Number:</strong> $manufacturerPartNumber</li>
            <li><strong>Brand:</strong> $brand</li>
            <li><strong>Model:</strong> $model</li>
            <li><strong>Color:</strong> $color</li>
            <li><strong>Additional Information:</strong> $additionalInformation</li>
        </ul>
        <img src='/images/$bikeImage' alt='Bike Image'>
        <button onclick="location.href='./police_dashboard.php'" type="button">Go to Dashboard</button>    
    </section>
    <footer class='footer'>
        &copy; 2024 Bike Company
    </footer>
</body>
</html>
HTML;

        echo $content;
    } else {
        echo "Bike not found.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
