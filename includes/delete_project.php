<?php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
    // Get the project ID from the AJAX request
    $projectId = $_POST["projectId"];

    // Validate the input (you can add more validation here)
    if (!empty($projectId)) {
        // Include the database connection file
        include("conn.php");

        // Prepare and execute the SQL statement to delete the project
        $stmt = $con->prepare("DELETE FROM projects WHERE project_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $projectId, $_SESSION['user_id']);

        if ($stmt->execute()) {
            // Project deleted successfully
            echo "Project deleted successfully";
        } else {
            // Error while deleting the project
            echo "Error deleting project: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();

        // Close the database connection
        $con->close();
    }
} else {
    // Handle session or request method issues
    echo "Unauthorized access";
}
?>
