<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Candidates_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to find the candidate by email
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $candidate = $result->fetch_assoc();
        
        // Verify password using password_verify
        if (password_verify($password, $candidate['password'])) {
            echo "Login successful! Welcome, Candidate " . htmlspecialchars($candidate['full_name']) . ".";
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "No candidate account found with that email.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
