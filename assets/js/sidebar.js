document.addEventListener("DOMContentLoaded", function () {
  // Your JavaScript code here
  const sbOptions = document.querySelectorAll(".sb-options");
  const ItemContent = document.querySelectorAll(".ItemContent");
  const textarea = document.getElementById("projectDescription");
  const charCount = document.getElementById("charCount");

  textarea.addEventListener("input", function () {
    const currentLength = textarea.value.length;
    charCount.textContent = currentLength;
  });

  sbOptions.forEach((option, index) => {
    option.addEventListener("click", () => {
      sbOptions.forEach((option) => {
        option.classList.remove("active");
      });

      option.classList.add("active");

      ItemContent.forEach((content) => {
        content.classList.remove("current");
      });

      ItemContent[index].classList.add("current");
    });
  });
});

function addTask() {
  // Get the task description from the input field
  var taskDescription = $("#taskDescription").val();

  // Check if the description is not empty
  if (taskDescription.trim() !== "") {
    // Send an AJAX request to add_task.php to add the task
    $.ajax({
      type: "POST",
      url: "includes/add_task.php",
      data: { description: taskDescription },
      success: function (response) {
        // Handle the response from the server (e.g., display a message)
        console.log(response);

        // If the task was added successfully, add it to the task list
        if (response === "Task added successfully") {
          // Create an <li> element for the task
          var li = $("<li>").text(taskDescription);

          // Add a data attribute to store the task ID (if you have one)
          // li.attr("data-task-id", taskId);

          // Append the new task to the task list
          $("#taskList").append(li);

          // Clear the input field
          $("#taskDescription").val("");
        }
      },
    });
  } else {
    // Handle empty task description (e.g., show an error message)
    displayErrorMessage("Please enter a task description");
  }
}

// Function to display an error message
function displayErrorMessage(message) {
  var errorMessage = $(".error-message");
  errorMessage.text(message);
  errorMessage.show();

  // Hide the error message after 3 seconds
  setTimeout(function () {
    errorMessage.hide();
  }, 3000);
}

// Add a click event listener to project list items
$("#projectList").on("click", "li", function () {
  var projectId = $(this).data("project-id"); // Get the project ID from the data attribute
  var completed = $(this).hasClass("done") ? "pending" : "done"; // Toggle the status

  // Send an AJAX request to update_project.php
  $.ajax({
    type: "POST",
    url: "includes/update_project.php",
    data: { projectId: projectId, completed: completed },
    success: function (response) {
      // Handle the response from the server (e.g., display a message)
      console.log(response);
    },
  });

  // Toggle the "done" class on the clicked project
  $(this).toggleClass("done");
});

// Function to toggle project status and update the database
function toggleProjectStatus(projectId, liElement) {
  // Check if the clicked <li> element has a "done" class
  if (liElement.classList.contains("done")) {
    // If it has a "done" class, remove it and update the status to "pending"
    liElement.classList.remove("done");
    updateProjectStatus(projectId, "pending");
  } else {
    // If it doesn't have a "done" class, add it and update the status to "done"
    liElement.classList.add("done");
    updateProjectStatus(projectId, "done");
  }
}

// Function to update project status in the database via AJAX
function updateProjectStatus(projectId, status) {
  // Send an AJAX request to update_project.php
  $.ajax({
    type: "POST",
    url: "includes/update_project.php",
    data: { projectId: projectId, completed: status },
    success: function (response) {
      // Handle the response from the server (e.g., display a message)
      console.log(response);
    },
  });
}

function deleteProject(projectId) {
  if (confirm("Are you sure you want to delete this project?")) {
    // Send an AJAX request to delete_project.php to remove the project
    $.ajax({
      type: "POST",
      url: "includes/delete_project.php",
      data: { projectId: projectId },
      success: function (response) {
        // Handle the response from the server (e.g., display a message)
        console.log(response);
        window.location.reload();
      },
    });
  }
}

const notesContainer = document.getElementById("sticky-note");
const addNoteButton = notesContainer.querySelector(".add-note");

getNotes();

addNoteButton.addEventListener("click", () => addNote());

function getNotes() {
  // Send an AJAX request to get existing notes
  $.ajax({
    type: "POST",
    url: "includes/notes.php",
    data: { action: "getNotes" },
    dataType: "json",
    success: function (data) {
      if (data && data.length > 0) {
        data.forEach((note) => {
          const noteElement = createNoteElement(note.note_id, note.content);
          notesContainer.insertBefore(noteElement, addNoteButton);
        });
      }
    },
    error: function () {
      alert("Error loading notes.");
    },
  });
}

function saveNotes(notes) {
  // Send an AJAX request to save notes
  $.ajax({
    type: "POST",
    url: "includes/notes.php",
    data: { action: "saveNotes", notes: notes },
    dataType: "json",
    success: function (data) {
      if (data.success) {
        // Notes saved successfully
        console.log("Notes saved successfully");
      } else {
        // Handle the case where saving failed
        console.error("Error saving notes: " + data.message);
      }
    },
    error: function () {
      console.error("AJAX error: Error saving notes.");
    },
  });
}

function createNoteElement(noteId, content) {
  const element = document.createElement("textarea");

  element.classList.add("note");
  element.value = content;
  element.placeholder = "Empty Sticky Note";

  element.addEventListener("change", () => {
    updateNote(noteId, element.value);
  });

  element.addEventListener("dblclick", () => {
    const doDelete = confirm(
      "Are you sure you wish to delete this sticky note?"
    );
    if (doDelete) {
      deleteNote(noteId, element);
    }
  });

  return element;
}

function addNote() {
  // Create a new note object for the recently added note
  const noteObject = {
    note_id: generateNoteId(),
    content: "",
  };

  const noteElement = createNoteElement(noteObject.note_id, noteObject.content);
  notesContainer.insertBefore(noteElement, addNoteButton);

  // Save only the recently added note
  saveNotes([noteObject]);
}

function updateNote(noteId, newContent) {
  // Send an AJAX request to update_note.php
  $.ajax({
    type: "POST",
    url: "includes/notes.php",
    data: {
      action: "updateNote", // Specify the action
      noteId: noteId, // Pass the noteId
      newContent: newContent, // Pass the newContent
    },
    dataType: "json",
    success: function (response) {
      // Handle the response from the server (e.g., display a message)
      console.log(response);

      if (response.message === "Note updated successfully") {
        // Update the note content on the client-side if it was updated successfully
        // You can implement this logic if needed
        console.log("Note updated successfully on the client side.");
      } else {
        // Handle the case where updating the note failed
        console.error("Failed to update note on the client side.");
      }
    },
    error: function () {
      // Handle AJAX errors here, if needed
      console.error("AJAX error: Error updating note.");
    },
  });
}

function deleteNote(noteId, element) {
  // Send an AJAX request to delete_note.php
  $.ajax({
    type: "POST",
    url: "includes/notes.php",
    data: {
      action: "deleteNote", // Specify the action
      noteId: noteId, // Pass the noteId
    },
    dataType: "json",
    success: function (response) {
      // Handle the response from the server (e.g., display a message)
      console.log(response);

      if (response.message === "Note deleted successfully") {
        // Remove the note element from the client-side if it was deleted successfully
        element.remove();
        console.log("Note deleted successfully on the client side.");
      } else {
        // Handle the case where deleting the note failed
        console.error("Failed to delete note on the client side.");
      }
    },
    error: function () {
      // Handle AJAX errors here, if needed
      console.error("AJAX error: Error deleting note.");
    },
  });
}

function getNotesArray() {
  const noteElements = notesContainer.querySelectorAll(".note");
  const notes = [];

  noteElements.forEach((element) => {
    const noteId = element.getAttribute("data-note-id");
    const content = element.value;

    notes.push({ note_id: noteId, content: content });
  });

  return notes;
}

function generateNoteId() {
  return Math.floor(Math.random() * 100000);
}

function confirmLogout() {
  if (confirm("Are you sure you want to log out?")) {
      // If the user confirms, redirect to the logout script
      window.location.href = "includes/logout.php";
  }
}