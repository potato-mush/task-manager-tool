<?php
session_start(); // Start the session

require("conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
        echo json_encode(array("error" => $error));
    } else {
        $email = mysqli_real_escape_string($con, $email);

        // Query checks if the email exists in the database.
        $query = "SELECT user_id, email, password FROM users WHERE email='" . $email . "'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));

        if (mysqli_num_rows($result) == 0) {
            // Email doesn't exist
            $error = "The provided email address is not registered.";
            echo json_encode(array("error" => $error));
        } else {
            $row = mysqli_fetch_assoc($result);
            $storedPasswordHash = $row['password'];

            // Use password_verify to check if the provided password matches the stored hash
            if (password_verify($password, $storedPasswordHash)) {
                // Password matches, user is authenticated
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_id'] = $row['user_id'];
                echo json_encode(array("success" => true));
                header("Location: ../dashboard.php");
            } else {
                // Password doesn't match
                echo '<script>alert("Incorrect email or password. Please try again.");</script>';
                echo '<script>window.location.href = "../index.php";</script>';
                exit;
            }
        }
    }
}
