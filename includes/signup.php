<?php
// Include the database connection file
include("conn.php");

$error_message = ""; // Initialize an empty error message
$success_message = ""; // Initialize an empty success message

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user input from the form
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate the input
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match";
    }

    // If there are no errors, proceed with database insertion
    if (empty($error_message)) {
        // Hash the password for security (you should use a stronger hashing method)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL query to insert user data into a table (replace 'users' with your table name)
        $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";

        // Prepare and bind the SQL statement
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

        // Execute the SQL statement
        if ($stmt->execute()) {
            $success_message = "Registration successful!";
            header("Location: ../index.php");
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }
}

// Display the error message
echo $error_message;
