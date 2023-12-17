<?php
require 'config.php'; // This file should contain the $conn variable that holds the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // password_verify will handle the hashing

    // Prepare the SELECT statement to get the role as well
    $query = "SELECT id, role, username, password FROM Users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        // Bind result variables
        mysqli_stmt_bind_result($stmt, $id, $db_role, $username, $hashed_password);
        mysqli_stmt_fetch($stmt);

        // Verify the role and check if the entered password matches the hashed password in the database
        if ($role === $db_role && password_verify($password, $hashed_password)) {
            // Role is correct and password is correct, so start a new session
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['role'] = $db_role;
            $_SESSION['username'] = $username;
            
            
            
            // Redirect user to a different home page based on their role
            switch ($db_role) {
                case 'client':
                    header("Location: Client.php");
                    break;
                case 'sitter':
                    header("Location: Sitter.php");
                    break;
                case 'handler':
                    header("Location: Handler.php");
                    break;
                default:
                    header("Location: LogIn.html");
                    break;
            }
            exit;
        } else {
            // Password is not valid, or role doesn't match
            echo "<script>alert('The role or password you entered was not valid.');</script>";
            header("Location: LogIn.html");
        }
    } else {
        // Username doesn't exist
        echo "<script>alert('No account found with that username.');</script>";
        header("Location: LogIn.html");
    }
}
?>