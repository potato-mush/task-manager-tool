<?php
session_start();

// Include your database connection file here
require_once("conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
    $action = $_POST["action"];

    if ($action === "getNotes") {
        echo json_encode(getNotes($_SESSION['user_id']));
    } elseif ($action === "saveNotes") {
        $notes = $_POST["notes"];
        if (saveNoteToDatabase($_SESSION['user_id'], $notes)) {
            echo json_encode(["success" => true, "message" => "Notes saved successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to save notes"]);
        }
    } elseif ($action === "updateNote") {
        $noteId = $_POST["noteId"];
        $newContent = $_POST["newContent"];

        if (updateNoteInDatabase($_SESSION['user_id'], $noteId, $newContent)) {
            echo json_encode(["message" => "Note updated successfully"]);
        } else {
            echo json_encode(["message" => "Failed to update note"]);
        }
    } elseif ($action === "deleteNote") {
        $noteId = $_POST["noteId"];
        deleteNoteFromDatabase($_SESSION['user_id'], $noteId);
        echo json_encode(["message" => "Note deleted successfully"]);
    }
}

function getNotes($userId)
{
    global $con; // Use the global database connection

    // Prepare an SQL statement to fetch notes for the given user
    $stmt = $con->prepare("SELECT note_id, content FROM sticky_notes WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $notes = [];

    while ($row = $result->fetch_assoc()) {
        $notes[] = [
            "note_id" => $row["note_id"],
            "content" => $row["content"],
        ];
    }

    // Close the prepared statement (do not close the database connection here)
    $stmt->close();

    return $notes;
}

function saveNoteToDatabase($userId, $note)
{
    global $con; // Use the global database connection

    $content = $note["content"];

    // Prepare an SQL statement to insert a new note
    $stmt = $con->prepare("INSERT INTO sticky_notes (user_id, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $userId, $content);

    if ($stmt->execute()) {
        // Note saved successfully, return the inserted note ID
        $noteId = $con->insert_id;
        $stmt->close();
        return $noteId;
    } else {
        // Error while saving the note
        return false;
    }
}


function deleteNoteFromDatabase($userId, $noteId)
{
    global $con; // Use the global database connection

    // Prepare an SQL statement to delete a note
    $stmt = $con->prepare("DELETE FROM sticky_notes WHERE user_id = ? AND note_id = ?");
    $stmt->bind_param("ii", $userId, $noteId);

    if ($stmt->execute()) {
        // Note deleted successfully
        $stmt->close();
        return true;
    } else {
        // Error while deleting the note
        $stmt->close();
        return false;
    }
}

function updateNoteInDatabase($userId, $noteId, $newContent)
{
    global $con; // Use the global database connection

    // Prepare an SQL statement to update the note's content
    $stmt = $con->prepare("UPDATE sticky_notes SET content = ? WHERE user_id = ? AND note_id = ?");
    $stmt->bind_param("sii", $newContent, $userId, $noteId);

    if ($stmt->execute()) {
        // Note updated successfully
        $stmt->close();
        return true;
    } else {
        // Error while updating the note
        $stmt->close();
        return false;
    }
}


// Close the database connection when done with all database operations
$con->close();
