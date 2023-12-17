<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assign and sanitize form inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING); // Fetch and sanitize role
    $ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user

    // Prepare SQL statement to insert the new user with IP
    $stmt = $conn->prepare("INSERT INTO Users (role, username, password, name, surname, phone, email, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters to the SQL statement
    $stmt->bind_param("ssssssss", $role, $username, $password, $name, $surname, $phone, $email, $ip);

    // Execute statement and check for success
    if ($stmt->execute()) {
        echo "New account created successfully.";
        // Redirect to login page
        header("Location: LogIn.html");
        exit;
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>
