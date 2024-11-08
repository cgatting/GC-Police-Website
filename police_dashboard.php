<?php
session_start();

// Check if the police officer is logged in
if (!isset($_SESSION['badge_number'])) {
    // Redirect the user to the police login page if they are not logged in
    header("Location: police_login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_');  // Replace with your database credentials
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Initialize filter variables
$manufacturer_number = isset($_GET['manufacturer_number']) ? $_GET['manufacturer_number'] : '';
$case_id = isset($_GET['case_id']) ? $_GET['case_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Construct WHERE clause based on filter options
$where_clause = '';
if (!empty($manufacturer_number)) {
    $where_clause .= "manufacturer_part_number = '$manufacturer_number' AND ";
}
if (!empty($case_id)) {
    $where_clause .= "id = '$case_id' AND ";
}
if (!empty($status)) {
    $where_clause .= "status = '$status' AND ";
}
if (!empty($start_date) && !empty($end_date)) {
    $where_clause .= "report_date BETWEEN '$start_date' AND '$end_date' AND ";
}

// Remove trailing "AND" if present
if (!empty($where_clause)) {
    $where_clause = 'WHERE ' . rtrim($where_clause, 'AND ');
}

// Retrieve list of stolen bikes with filters
$sql = "SELECT * FROM stolen_bikes $where_clause";
$result = $conn->query($sql);

// Check if form for changing status is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bike_id']) && isset($_POST['new_status'])) {
    $bike_id = $_POST['bike_id'];
    $new_status = $_POST['new_status'];

    // Update the status in the database
    $update_sql = "UPDATE stolen_bikes SET status = '$new_status' WHERE id = '$bike_id'";
    if ($conn->query($update_sql) === TRUE) {
        // Redirect back to the same page after updating
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #001f3f;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
        }

        .logo {
            width: 80px;
            height: 80px;
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

        .dashboard {
            padding: 20px;
        }

        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .date-inputs {
            margin-bottom: 10px;
        }

        .date-inputs label {
            margin-right: 10px;
        }

        .date-inputs input {
            padding: 5px;
            font-size: 16px;
        }

        .date-filter {
            display: inline-block;
            margin-bottom: 10px;
        }

        .date-filter select {
            font-size: 16px;
            padding: 5px;
        }

        .date-filter label {
            margin-right: 10px;
        }

        .table-container {
            min-height: 500px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        form {
            display: inline-block;
        }

        select {
            padding: 5px;
            font-size: 16px;
        }

        button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
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
                <li><a href="subpage.html">Register Bike</a></li>
                <li><a href="subpage.html">Report Stolen Bike</a></li>
                <li><a href="index.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    <section class="dashboard">
        <h2>Stolen Bikes</h2>
        <!-- Filtering options -->
        <form method="GET" action="">
            <label for="manufacturer_number">Manufacturer Number:</label>
            <input type="text" name="manufacturer_number" id="manufacturer_number" value="<?php echo $manufacturer_number; ?>">
            
            <label for="case_id">Case ID:</label>
            <input type="text" name="case_id" id="case_id" value="<?php echo $case_id; ?>">
            
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">-- Select Status --</option>
                <option value="Reported" <?php echo ($status == 'Reported') ? 'selected' : ''; ?>>Reported</option>
                <option value="Recovered" <?php echo ($status == 'Recovered') ? 'selected' : ''; ?>>Recovered</option>
                <option value="Not Recovered" <?php echo ($status == 'Not Recovered') ? 'selected' : ''; ?>>Not Recovered</option>
            </select>

            <!-- Date range filter -->
            <div class="date-inputs">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
                
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
            </div>
            
            <button type="submit">Filter</button>
        </form>

        <!-- Display Stolen Bikes Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Manufacturer Part Number</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Color</th>
                        <th>Additional Information</th>
                        <th>Reporter Email</th>
                        <th>Reporter Phone Number</th>
                        <th>Reporter Name</th>
                        <th>Status</th>
                        <th>Report Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['manufacturer_part_number']; ?></td>
                            <td><?php echo $row['brand']; ?></td>
                            <td><?php echo $row['model']; ?></td>
                            <td><?php echo $row['color']; ?></td>
                            <td><?php echo $row['additional_information']; ?></td>
                            <td><?php echo $row['user_email']; ?></td>
                            <td><?php echo $row['user_phone_number']; ?></td>
                            <td><?php echo $row['user_first_name'] . ' ' . $row['user_last_name']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['report_date']; ?></td>
                            <td>
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="bike_id" value="<?php echo $row['id']; ?>">
                                    <select name="new_status">
                                        <option value="Reported" <?php echo ($row['status'] == 'Reported') ? 'selected' : ''; ?>>Reported</option>
                                        <option value="Recovered" <?php echo ($row['status'] == 'Recovered') ? 'selected' : ''; ?>>Recovered</option>
                                        <option value="Not Recovered" <?php echo ($row['status'] == 'Not Recovered') ? 'selected' : ''; ?>>Not Recovered</option>
                                    </select>
                                    <button type="submit">Change Status</button>
                                </form>
                            </td>
                            <td>
                            <form method="GET" action="view_image.php">
                                <input type="hidden" name="manufacturer_part_number" value="<?php echo $row['manufacturer_part_number']; ?>">
                                <button type="submit">View Bike</button>
                            </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</body>
</html>
