<?php

require 'config.php';
header('Content-Type: application/json'); // Indicate that we're returning JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start(); // Ensure session start for using session variables
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $username = $_SESSION['username']; // Retrieve username from session

    // Ensure the user is assigned to the post with 'Assigned' status
    $stmt = $conn->prepare("SELECT * FROM petinfo WHERE Post_Id = ? AND Taken_by = ? AND Status = 'Assigned'");
    $stmt->bind_param("is", $post_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Insert the comment into the database
        $stmt = $conn->prepare("INSERT INTO Comments (Post_Id, Username, Comment, Date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $post_id, $username, $comment);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Comment submitted successfully.';
        } else {
            $response['message'] = 'Failed to submit comment.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'You can only comment on assigned posts.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
