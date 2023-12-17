<?php
// Include your database connection configuration
require 'config.php';

function saveComment($conn, $username, $postId, $commentText) {
    // Prepare a statement to insert the new comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (Post_Id, Username, Comment, Date) VALUES (?, ?, ?, NOW())");
    
    // Check for errors in preparation
    if (!$stmt) {
        // Handle error - perhaps log to a file or display a user-friendly message
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        return false;
    }
    
    // Bind parameters and execute
    $stmt->bind_param("iss", $postId, $username, $commentText);
    $result = $stmt->execute();
    
    // Check for errors in execution
    if (!$result) {
        // Handle error - perhaps log to a file or display a user-friendly message
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        return false;
    }
    
    $stmt->close();
    return true;
}

$username = $_SESSION['username'] ?? null;
if ($username === null) {
    // Redirect to login or handle the case where the user is not logged in
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Check if a new comment is being posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Call the saveComment function to handle comment saving to the database
    if (!saveComment($conn, $username, $_POST['post_id'], $_POST['comment'])) {
        // Handle error if comment saving fails
        die('Error saving comment.');
    }
}

// Fetch the user's posts
$stmt = $conn->prepare("SELECT Post_Id, PetName FROM petinfo WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$userPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Initialize an array to hold the comments of a selected post
$selectedPostComments = [];

// Check if a specific post was selected to view comments
if (isset($_POST['selected_post_id'])) {
    $selectedPostId = $_POST['selected_post_id'];
    $stmtComments = $conn->prepare("SELECT * FROM comments WHERE Post_Id = ? ORDER BY Date ASC");
    $stmtComments->bind_param("i", $selectedPostId);
    $stmtComments->execute();
    $selectedPostComments = $stmtComments->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtComments->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <style>
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

body {
    background-color: #f4f4f4;
    padding-left: 250px; /* Considering sidebar width */
    background: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), url('Signup_background_img.jpg') no-repeat center center fixed; 
    background-size: cover;
    
}

/* Sidebar Styles */
.sidebar {
    background-color: #333;
    color: white;
    padding: 10px;
    height: 100vh;
    width: 250px;
    position: fixed;
    left: 0;
    top: 0;
    overflow: auto;
    z-index: 1000;
}

.sidebar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    margin: 20px auto;
}

.sidebar h2 {
    color: #fff;
    text-align: center;
    margin-bottom: 20px;
}

.sidebar a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px;
    transition: all 0.3s ease;
}

.sidebar a:hover {
    background-color: #575757; /* Lighten on hover */
    border-left: 3px solid #4CAF50; /* Highlight on hover */
}

/* Form Styles */
form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    margin: 40px auto;
}

form label {
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-size: 16px;
}

form select,
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

form input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #45a049;
}

/* Comment Styles */
.comment {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.comment p {
    color: #333;
    line-height: 1.5;
    font-size: 14px;
}

.comment strong {
    color: #4CAF50;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    body {
        padding-left: 0;
    }
    
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .sidebar a {
        flex: 1 1 auto;
        margin: 5px;
        text-align: center;
    }
    
    form {
        max-width: calc(100% - 40px);
    }
}

    </style>
</head>
<body>
<div class="sidebar">
  <div class="profile-pic">
    <!-- Add your image inside this div as an img tag -->
    <img src="Profile_img.jpg" alt="Profile Picture" style="width:100%; height:100%; border-radius:50%;">
  </div>
  <h2>Customer Dashboard</h2>
  <a href="Client.php" >Back</a>
  <a href="LogOut.php">Logout</a>
</div>
<div id="Feedback">
    <!-- Form to post a new comment -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
     <label for="post">Select Post to Comment:</label>
     <select name="post_id" id="post">
         <?php foreach ($userPosts as $post): ?>
             <option value="<?= $post['Post_Id'] ?>"><?= htmlspecialchars($post['PetName']) ?> (Post ID: <?= $post['Post_Id'] ?>)</option>
         <?php endforeach; ?>
     </select>

     <label for="comment">Comment:</label>
     <textarea name="comment" id="comment" required></textarea>
     <input type="submit" value="Submit Comment">
    </form>

    <!-- Form to select a post to view comments -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
     <label for="selected_post">Select Post to View Comments:</label>
     <select name="selected_post_id" id="selected_post">
         <?php foreach ($userPosts as $post): ?>
             <option value="<?= $post['Post_Id'] ?>"><?= htmlspecialchars($post['PetName']) ?> (Post ID: <?= $post['Post_Id'] ?>)</option>
         <?php endforeach; ?>
     </select>
     <input type="submit" value="View Comments">
    </form>

    <!-- Display comments for the selected post -->
    <?php if (!empty($selectedPostComments)): ?>
        <h2>Comments for Post ID: <?= htmlspecialchars($selectedPostId) ?></h2>
        <?php foreach ($selectedPostComments as $comment): ?>
            <div class="comment">
                <p><strong><?= htmlspecialchars($comment['Username']) ?>:</strong> <?= htmlspecialchars($comment['Comment']) ?></p>
                <p>Date: <?= htmlspecialchars($comment['Date']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>
    function saveComment($conn, $username, $postId, $commentText) {
    // Prepare a statement to insert the new comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (Post_Id, Username, Comment, Date) VALUES (?, ?, ?, NOW())");

    // Check for errors in preparation
    if (!$stmt) {
        // Handle error - perhaps log to a file or display a user-friendly message
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        return false;
    }

    // Bind parameters and execute
    $stmt->bind_param("iss", $postId, $username, $commentText);
    $result = $stmt->execute();

    // Check for errors in execution
    if (!$result) {
        // Handle error - perhaps log to a file or display a user-friendly message
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        return false;
    }

    $stmt->close();

    return true;
}

</script>
</body>
</html>
