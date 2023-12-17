<?php

require 'config.php';


header('Content-Type: application/json'); // Indicate that we're returning JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'] ?? null;
    $comment = $_POST['comment'] ?? null;
    $username = $_SESSION['username'] ?? null; // Retrieve username from session

    // Ensure that all required fields are filled out
    if (is_null($post_id) || is_null($comment) || is_null($username)) {
        $response['message'] = 'Missing required information.';
        echo json_encode($response);
        exit;
    }

    // Insert the comment into the database without checking the status
    $insertStmt = $conn->prepare("INSERT INTO Comments (Post_Id, Username, Comment, Date) VALUES (?, ?, ?, NOW())");
    $insertStmt->bind_param("iss", $post_id, $username, $comment);

    if ($insertStmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Comment submitted successfully.';
    } else {
        $response['message'] = 'Failed to submit comment: ' . $conn->error;
    }
    $insertStmt->close();
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
