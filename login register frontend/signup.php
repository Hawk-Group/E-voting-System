<?php
// Database connection
$servername = "localhost";  // Change this if needed
$username = "root";         // MySQL username
$password = "";             // MySQL password
$dbname = "voting_system";  // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $aadharno = $_POST['aadharno'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];

    // Hash the password (use bcrypt)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, aadhar_number, mobile_number, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $hashed_password, $aadharno, $mobile, $address);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Account created successfully. You can now <a href='login.html'>login</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
