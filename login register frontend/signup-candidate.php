<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Candidates_data";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $aadharno = $_POST['aadharno'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];

    // Handle the file upload
    $target_dir = "uploads/";  // Directory to store uploaded files
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a valid image
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }

    // Ensure directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);  // Create the directory if it doesn't exist
    }

    // Attempt to upload the image
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare the SQL query to insert the candidate data
        $stmt = $conn->prepare("INSERT INTO candidates (full_name, email, password, aadharno, mobile, address, symbol_image) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Check if the prepare() call was successful
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters and execute the query
        $stmt->bind_param("sssssss", $name, $email, $hashed_password, $aadharno, $mobile, $address, $target_file);

        // Execute the query and check if the data was successfully inserted
        if ($stmt->execute()) {
            echo "Account successfully created for " . htmlspecialchars($name) . ".";
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
