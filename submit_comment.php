<?php

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $username = $_SESSION['username']; // Retrieve username from session
    $date = date('Y-m-d H:i:s'); // Get the current date and time

    // Ensure the user is commenting on their own post
    $stmt = $conn->prepare("SELECT * FROM petinfo WHERE Post_Id = ? AND Username = ?");
    $stmt->bind_param("is", $post_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Insert the comment into the database
        $stmt = $conn->prepare("INSERT INTO Comments (Post_Id, Username, Comment, Date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $post_id, $username, $comment, $date);
        $stmt->execute();
        $stmt->close();

        echo "Comment submitted successfully.";
    } else {
        echo "You can only comment on your own posts.";
    }
} else {
    header('Location: index.php'); // Redirect if the form wasn't submitted
    exit;
}
?>
