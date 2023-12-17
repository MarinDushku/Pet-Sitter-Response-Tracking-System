<?php

require 'config.php'; // Ensure this file contains the $conn variable with the PDO connection

// Define the handleFileUpload function
function handleFileUpload($file) {
    $target_dir = "uploads/";
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'png', 'gif', 'jpeg']; // Allowed file types

    // Check if file type is allowed
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }

    // Sanitize file name and create unique file path
    $sanitizedFilename = uniqid() . '.' . $fileType;
    $targetFilePath = $target_dir . $sanitizedFilename;

    // Check if the uploads directory exists, if not, create it
    if (!file_exists($target_dir) && !mkdir($target_dir, 0777, true) && !is_dir($target_dir)) {
        throw new RuntimeException('Failed to create upload directory.');
    }

    // Attempt to move the uploaded file to its new destination
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        return $sanitizedFilename; // Return the new filename on success
    }

    return false; // Return false if the upload failed
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $petName = filter_input(INPUT_POST, 'petName', FILTER_SANITIZE_STRING);
    $petType = filter_input(INPUT_POST, 'petType', FILTER_SANITIZE_STRING);
    $breed = filter_input(INPUT_POST, 'breed', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $vaccinated = filter_input(INPUT_POST, 'vaccinated', FILTER_SANITIZE_STRING) === 'Yes' ? 'Yes' : 'No';
    $healthIssues = filter_input(INPUT_POST, 'healthIssues', FILTER_SANITIZE_STRING);
    $temperament = filter_input(INPUT_POST, 'temperament', FILTER_SANITIZE_STRING);
    $foodType = filter_input(INPUT_POST, 'foodType', FILTER_SANITIZE_STRING);
    $feedingTimes = filter_input(INPUT_POST, 'feedingTimes', FILTER_SANITIZE_STRING);
    $exerciseNeeds = filter_input(INPUT_POST, 'exerciseNeeds', FILTER_SANITIZE_STRING);
    $favoriteToys = filter_input(INPUT_POST, 'favoriteToys', FILTER_SANITIZE_STRING);
    $sittingDates = filter_input(INPUT_POST, 'sittingDates', FILTER_SANITIZE_STRING);
    $sittingTime = filter_input(INPUT_POST, 'sittingTime', FILTER_SANITIZE_STRING);
    $specialInstructions = filter_input(INPUT_POST, 'specialInstructions', FILTER_SANITIZE_STRING);

    $username = $_SESSION['username'] ?? null;
    if ($username === null) {
        // Handle the case where the session does not have a username
        die("User is not logged in.");
    }

    // Check for the presence of a file in the upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoFilename = handleFileUpload($_FILES['photo']);
        if (!$photoFilename) {
            die("Error uploading file.");
        }
    } else {
        die("File upload error.");
    }

    // Disable autocommit for transaction
    $conn->autocommit(FALSE);

    try {
        // Insert into PetInfo
        $stmtPetInfo = $conn->prepare("INSERT INTO PetInfo (Username, PetName, PetType, Breed, Age, Gender, Photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtPetInfo->bind_param("ssssiss", $username, $petName, $petType, $breed, $age, $gender, $photoFilename);
        $stmtPetInfo->execute();
        
        // Get the last inserted ID to use as the foreign key
        $petInfoId = $conn->insert_id;

        // Insert into HealthBehavior
        $stmtHealthBehavior = $conn->prepare("INSERT INTO HealthBehavior (Post_Id, Vaccinated, HealthIssues, Temperament, DietType, FeedingTimes, ExerciseNeeds, FavoriteToys, SittingDates, SittingTime, SpecialInstructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtHealthBehavior->bind_param("issssssssss", $petInfoId, $vaccinated, $healthIssues, $temperament, $foodType, $feedingTimes, $exerciseNeeds, $favoriteToys, $sittingDates, $sittingTime, $specialInstructions);
        $stmtHealthBehavior->execute();

        // Commit the transaction
        $conn->commit();
        
        // Redirect or send a success message
        header("Location: Client.php");
        exit();

    } catch (Exception $e) {
        // Something went wrong, roll back the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Re-enable autocommit
    $conn->autocommit(TRUE);
}
?>