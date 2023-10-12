<?php
// Include your database connection code here
include('includes/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $user_id = $_SESSION['user_id'];
    $projectName = $_POST['projectName'];
    $description = $_POST['projectDescription'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];

    // Set the project status as "pending"
    $status = "pending";

    // Insert data into the database
    $sql = "INSERT INTO projects (project_name, project_description, project_priority, project_deadline, project_status, user_id) 
        VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssi", $projectName, $description, $priority, $deadline, $status, $user_id);
        if ($stmt->execute()) {
            echo '<script>alert("Project submitted successfully.");</script>';
            echo '<script>window.location.href = "dashboard.php";</script>';
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $con->error;
    }
    $con->close();
}
?>
