<?php
// Database connection
$conn = new mysqli('localhost:3306', 'Admin9', 'Adminpassword123.', 's4302339_');  // Replace with your database credentials
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Check if manufacturer_part_number is provided in the URL
if(isset($_GET['manufacturer_part_number'])) {
    // Sanitize the input
    $manufacturer_part_number = htmlspecialchars($_GET['manufacturer_part_number']);
    
    // Retrieve image_name associated with the manufacturer_part_number
    $sql = "SELECT image_name FROM bikes WHERE manufacturer_part_number = '$manufacturer_part_number'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_name = $row['image_name'];
        
        // Path to the directory containing images
        $image_directory = 'images/';
        
        // Construct the full path to the image file
        $image_path = $image_directory . $image_name;
        
        // Check if the image file exists
        if(file_exists($image_path)) {
            // Output appropriate headers
            header("Content-Type: image/jpeg"); // Adjust content type based on your image format
            
            // Output the image
            readfile($image_path);
            exit();
        } else {
            // Image file not found
            echo "Image not found.";
            echo $image_path;
        }
    } else {
        // No record found for the provided manufacturer_part_number
        echo "No image found for the provided manufacturer part number.";
    }
} else {
    // If manufacturer_part_number is not provided in the URL
    echo "Invalid request.";
}

// Close database connection
$conn->close();
?>
