<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <title>Task Manager Tool</title>
</head>

<body>
    <a onclick="confirmLogout()" class="logoutBtn"><i class="fa-solid fa-right-from-bracket"></i></a>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="sb-options active"><i class="fas fa-list-check"></i><span>Tasks</span></a>
        <a href="#" class="sb-options"><i class="fas fa-folder"></i><span>Projects</span></a>
        <a href="#" class="sb-options"><i class="fas fa-note-sticky"></i><span>Reminders</span></a>
    </div>

    <!-- Blank space on the right -->
    <div class="ItemContent current">
        <div class="ItemContentTitle">Tasks</div>
        <div class="lines"></div>
        <ul class="list" id="projectList">
            <li class="projectHeader">
                <div>Project Title</div>
                <div class='right-align'>
                    <div>Deadline</div>
                    <div>Priority</div>
                    <div>Action</div>
                </div>
            </li>
            <?php
            session_start(); // Start the session

            // Priority classes mapping
            $priorityClasses = [
                'low' => 'priority-low',
                'medium' => 'priority-medium',
                'high' => 'priority-high',
            ];

            // Fetch projects for the logged-in user
            if (isset($_SESSION['user_id'])) {
                // Include the database connection file
                include("includes/conn.php");

                // Query to count the total number of projects
                $sql = "SELECT COUNT(*) as total_projects FROM projects WHERE user_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $totalProjects = $row['total_projects'];
                } else {
                    $totalProjects = 0; // Set to 0 if there's an error
                }

                // Number of projects to display per page
                $projectsPerPage = 10;

                // Calculate the total number of pages
                $totalPages = ceil($totalProjects / $projectsPerPage);

                // Current page (from the query string)
                $currentpage = isset($_GET['page']) ? $_GET['page'] : 1;

                // Calculate the offset for the SQL query
                $offset = ($currentpage - 1) * $projectsPerPage;

                // Prepare and execute the SQL statement to fetch projects for the current page
                $stmt = $con->prepare("SELECT * FROM projects WHERE user_id = ? LIMIT ? OFFSET ?");
                $stmt->bind_param("iii", $_SESSION['user_id'], $projectsPerPage, $offset);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Output the project list
                    while ($row = $result->fetch_assoc()) {
                        $projectId = $row['project_id'];
                        $projectName = $row['project_name'];
                        $projectDescription = $row['project_description'];
                        $projectPriority = $row['project_priority'];
                        $projectDeadline = $row['project_deadline'];

                        // Determine the status class based on the project status (assuming "project_status" is the field in your database)
                        $statusClass = ($row['project_status'] === 'done') ? 'done' : '';

                        // Check if the projectPriority exists in the $priorityClasses array
                        $priorityClass = '';
                        if (array_key_exists($projectPriority, $priorityClasses)) {
                            $priorityClass = $priorityClasses[$projectPriority];
                        }

                        // Output the project as an <li> element
                        echo "<li data-project-id='$projectId' class='$statusClass $priorityClass'>
                    <span>$projectName - $projectDescription</span> 
                        <div class='right-align'>
                        <div>$projectDeadline</div>
                            <div class='$projectPriority'>$projectPriority</div>
                            <button class='deleteProjectBtn' onclick='deleteProject($projectId)'><i class='fas fa-trash'></i></button>
                        </div>
                    </li>";
                    }
                } else {
                    echo "<li>No projects found.</li>";
                }

                $stmt->close();
                $con->close();
            }
            ?>

        </ul>

        <!-- Display pagination if totalProjects exceeds projectsPerPage -->
        <?php if ($totalProjects > $projectsPerPage) : ?>
            <div class="pagination-container">
                <div class="pagination">
                    <?php
                    // Calculate the range of visible page links
                    $range = 1;

                    // Show "Prev" link if not on the first page
                    if ($currentpage > 1) {
                        echo "<a href='?page=" . ($currentpage - 1) . "' class='prevBtn'>Prev</a>";
                    }

                    for ($page = 1; $page <= $totalPages; $page++) {
                        if ($page == $currentpage) {
                            echo "<a class='active' href='?page=$page'>$page</a>";
                        } elseif ($page <= $range || $page > $totalPages - $range || ($page >= $currentpage - $range && $page <= $currentpage + $range)) {
                            echo "<a href='?page=$page'>$page</a>";
                        } elseif ($page == $range + 1 || $page == $totalPages - $range - 1) {
                            echo "<span class='ellipsis'>&hellip;</span>";
                        }
                    }

                    // Show "Next" link if not on the last page
                    if ($currentpage < $totalPages) {
                        echo "<a href='?page=" . ($currentpage + 1) . "' class='nextBtn'>Next</a>";
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>


    <div class="ItemContent ">
        <div class="ItemContentTitle">Projects</div>
        <div class="container">
            <form action="submit_project.php" method="post">
                <div class="form-group">
                    <label for="projectName">Project Name:</label>
                    <input type="text" class="form-control" id="projectName" name="projectName" required>
                </div>
                <div class="form-group">
                    <label for="projectDescription">Project Description:</label>
                    <textarea class="form-control" id="projectDescription" name="projectDescription" rows="4" maxlength="30" required></textarea>
                    <p><span id="charCount">0</span> / 30</p>
                </div>
                <div class="form-group">
                    <label for="priority">Priority:</label>
                    <select class="form-control" id="priority" name="priority" required>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="deadline">Deadline:</label>
                    <input type="date" class="form-control" id="deadline" name="deadline" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="registerBtn">Register Project</button>
            </form>
        </div>
    </div>
    <div class="ItemContent ">
        <div class="ItemContentTitle">Reminders</div>
        <span>Note: Double-click to delete note.</span>
        <div id="sticky-note">
            <button class="add-note" type="button">+</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/sidebar.js"></script>
</body>

</html>