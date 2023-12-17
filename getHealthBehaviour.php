<?php
require 'config.php'; // Your database configuration

$postId = filter_input(INPUT_GET, 'postId', FILTER_SANITIZE_NUMBER_INT);

if ($postId) {
    $stmt = $conn->prepare("SELECT * FROM HealthBehavior WHERE Post_Id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    $healthDetails = $result->fetch_assoc();

    if ($healthDetails) {
        // Convert health details to HTML
        $html = "<p>Vaccinated: " . htmlspecialchars($healthDetails['Vaccinated']) . "</p>";
        // ... Add more details as needed

        // Send back a JSON response
        echo json_encode(['html' => $html]);
    } else {
        echo json_encode(['html' => 'No details found.']);
    }
} else {
    echo json_encode(['html' => 'Invalid request.']);
}
?>
