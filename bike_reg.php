<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page if they are not logged in
    header("Location: user_login.html");
    exit();
}

// Retrieve user's email from the session
$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $manufacturerPartNumber = $_POST['manufacturerPartNumber'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $bikeType = $_POST['bikeType'];
    $wheelSize = $_POST['wheelSize'];
    $color = $_POST['color'];
    $numOfGears = $_POST['numOfGears'];
    $brakeType = $_POST['brakeType'];
    $suspension = $_POST['bikeSuspension'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    // Check if a file was uploaded
    if(isset($_FILES['bikeImage']) && $_FILES['bikeImage']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['bikeImage']['name'];
        $image_tmp = $_FILES['bikeImage']['tmp_name'];
        $image_size = $_FILES['bikeImage']['size'];

        // Check the file size limit (adjust this as needed)
        if($image_size > 5242880) { // 5MB
            echo "<script>alert('Image size exceeds the limit. Please choose a smaller image.');</script>";
            exit();
        }

        // Database connection (replace with your own database details)
        $conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_'); 

        if ($conn->connect_error) {
            die("Connection Failed : " . $conn->connect_error);
        } else {
            // Insert bike registration data into the database
            var_dump($_POST); // Dump the entire $_POST array to see what data is being sent.

            $stmt = $conn->prepare("INSERT INTO bikes (user_email, manufacturer_part_number, brand, model, bike_type, wheel_size, color, num_of_gears, brake_type, suspension, gender, age_group, image_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssssssssss', $email, $manufacturerPartNumber, $brand, $model, $bikeType, $wheelSize, $color, $numOfGears, $brakeType, $suspension, $gender, $age, $image_name); // Bind the image_name parameter
            $execval = $stmt->execute();

            if ($execval) {
                // Save the uploaded image to a directory
                $upload_dir = "images/"; // Create this directory if it doesn't exist
                $target_path = $upload_dir . $image_name;
                move_uploaded_file($image_tmp, $target_path);

                // Registration successful
                echo "<script>alert('Registered successfully.'); window.location.href = 'user_dashboard.php';</script>";
            } else {
                // Registration failed
                echo "<script>alert('Registration failed. Please try again.');</script>";
            }

            $stmt->close();
            $conn->close();
        }
    } else {
        echo "<script>alert('File upload failed. Please choose a valid image file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Bike</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image.png" alt="Gloucestershire Constabulary Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="bike_reg.html">Register Bike</a></li>
                <li><a href="#">Report Stolen Bike</a></li>
                <li><a href="police_login.php">Police Login</a></li>
            </ul>
        </nav>
    </header>
        <div class="registration-form">
            <h2>Register Your Bike</h2>
            <form action="bike_reg.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="manufacturerPartNumber">Manufacturer Part Number (MPN)</label>
                    <input type="text" id="manufacturerPartNumber" name="manufacturerPartNumber" required>
                </div>
                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" required>
                </div>
                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" id="model" name="model" required>
                </div>
                <div class="form-group">
                    <label for="bikeType">Bike Type</label>
                    <select id="bikeType" name="bikeType" required>
                        <option value="electric">Electric Bike</option>
                        <option value="mountain">Mountain Bike</option>
                        <option value="bmx">BMX</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="wheelSize">Wheel Size (inches)</label>
                    <select id="wheelSize" name="wheelSize" required>
                        <option value="14">14 inches</option>
                        <option value="20">20 inches</option>
                        <option value="24">24 inches</option>
                        <option value="26">26 inches</option>
                        <option value="27.5">27.5 inches</option>
                        <option value="29">29 inches</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="color">Color</label>
                    <select type="text" id="color" name="color" required>
                        <option value="blue">Blue</option>
                        <option value="red">Red</option>
                        <option value="purple">Purple</option>
                        <option value="organge">Organge</option>
                        <option value="green">Green</option>
                        <option value="yellow">Yellow</option>
                        <option value="pink">Pink</option>
                        <option value="brown">Brown</option>
                        <option value="white">White</option>
                        <option value="black">Black</option>

                    </select>
                </div>
                <div class="form-group">
                    <label for="numOfGears">Number of Gears</label>
                    <select id="bike-gears" name="numOfGears">
                        <option value="1">Single-speed bike (1 gear)</option>
                        <option value="3">3-speed bike (3 gears)</option>
                        <option value="7">7-speed bike (7 gears)</option>
                        <option value="21">21-speed bike (21 gears)</option>
                        <option value="24">24-speed bike (24 gears)</option>
                        <option value="27">27-speed bike (27 gears)</option>
                        <option value="30">30-speed bike (30 gears)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brakeType">Brake Type</label>
                    <select id="bike-brakes" name="brakeType">
                        <option value="rim">Rim Brakes</option>
                        <option value="disc">Disc Brakes</option>
                        <option value="v-brake">V-Brakes (Linear Pull Brakes)</option>
                        <option value="cantilever">Cantilever Brakes</option>
                        <option value="coaster">Coaster (Pedal) Brakes</option>
                        <option value="drum">Drum Brakes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="suspension">Suspension</label>
                    <select id="bike-suspension" name="bikeSuspension">
                        <option value="none">No Suspension</option>
                        <option value="front">Front Suspension (Hardtail)</option>
                        <option value="full">Full Suspension (Dual Suspension)</option>
                        <option value="rigid">Rigid (Fixed) Fork</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="age">Age Group</label>
                    <select id="age" name="age" required>
                        <option value="8-12">8-12</option>
                        <option value="Teenager">13-17</option>
                        <option value="Young Adult">18-24</option>
                        <option value="Adult">25 to 65</option>
                        <option value="Senior">65+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bikeImage">Bike Image</label>
                    <input type="file" id="bikeImage" name="bikeImage" accept="image/*" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-button">Register</button>
                </div>
            </form>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2024 Gloucestershire Constabulary - Contact 111 to Report a Bike Stolen</p>
    </footer>
</body>
</html>

