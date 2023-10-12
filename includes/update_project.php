<?php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
    // Get user input from the AJAX request
    $projectId = $_POST["projectId"];
    $completed = $_POST["completed"];

    // Validate the input (you can add more validation here)
    if (!empty($projectId) && ($completed === 'pending' || $completed === 'done')) {
        // Include the database connection file
        include("conn.php");

        // Prepare and execute the SQL statement to update the project status
        $stmt = $con->prepare("UPDATE projects SET project_status = ? WHERE project_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $completed, $projectId, $_SESSION['user_id']);

        if ($stmt->execute()) {
            // Project status updated successfully
            echo "Status updated successfully";
        } else {
            // Error while updating the project status
            echo "Error updating project status: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();

        // Close the database connection
        $con->close();
    } else {
        // Handle invalid or missing POST data
        echo "Invalid POST data";
    }
} else {
    // Handle session or request method issues
    echo "Unauthorized access";
}
