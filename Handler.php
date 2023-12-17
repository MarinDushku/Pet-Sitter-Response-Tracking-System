<?php
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: LogIn.html'); // Redirect to login page if not logged in
    exit;
}

$username = $_SESSION['username'];

// Fetch all posts
$stmt = $conn->prepare("SELECT petinfo.*, HealthBehavior.* FROM petinfo LEFT JOIN HealthBehavior ON petinfo.Post_Id = HealthBehavior.Post_Id");
if (!$stmt) {
    die("Error in SQL query: " . $conn->error);
}
$stmt->execute();
$allPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetching all users with the role of 'client' or 'sitter'
$userStmt = $conn->prepare("SELECT * FROM users WHERE role = 'client' OR role = 'sitter'");
if (!$userStmt) {
    die("Error in SQL query: " . $conn->error);
}
$userStmt->execute();
$users = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$userStmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Interface</title>
<style>
  body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    background: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), url('Signup_background_img.jpg') no-repeat center center fixed; 
    background-size: cover;
  }
  .sidebar {
    background-color: #333;
    color: white;
    padding: 10px;
    height: 100vh;
    width: 250px;
    position: fixed;
    border-right: 5px solid #fff; /* Adjust the color to match your design */
    border-top-right-radius: 15px; /* Rounded top-right corner */
    border-bottom-right-radius: 15px; /* Rounded bottom-right corner */
  }
  .sidebar a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-left: 3px solid transparent;
  }
  .sidebar a:hover {
    border-left: 3px solid green;
  }
  .profile-pic {
    width: 100px;
    height: 100px;
    background-color: #ddd; /* Placeholder color */
    border-radius: 50%; /* Makes the div round */
    margin: 20px auto; /* Centers the div */
  }
  .content {
    margin-left: 250px;
    padding: 80px;
  }
  .content h1 {
    color: #333;
    border-bottom: 1px solid #ccc;
  }
  .tips {
    background-color: white;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
  }
  .hidden { display: none; }
  textarea {
        resize: none; /* Disables resizing of textareas */
        /* ... other properties ... */
    }
    .form-container {
    width: 100%;
    max-width: 80%; /* Your preferred max-width */
    max-height: auto; /* Set a max-height for the container */
    margin:auto;
    padding: 10px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.container {
    margin-left: 270px; /* Adjust this value to the width of your sidebar + some extra space */
    padding: 20px;
}



.post-container {
  background-color: #333; /* Dark gray background */
  color: white;
  display: flex;
  align-items: center;
  width: 100%; /* Or max-width for larger screens */
  margin-bottom: 20px;
  border-radius: 8px;
  overflow: hidden; /* Ensures the content respects border-radius */
}

.post-image img {
  width: 100px; /* Adjust as needed */
  height: 100px;
  object-fit: cover; /* Ensures the image covers the area */
  border-radius: 50%;
  margin-right: 20px;
}

.post-details1 {
  display: flex;
  flex-direction: column;
  flex-grow: 1; /* Takes up remaining space */
}
.post-details2 {
  display: flex;
  flex-direction: row;
  flex-grow: 1; /* Takes up remaining space */
}
.detail {
  margin: 15px;
}




.assign-form {
    margin-left: auto; /* Align to the right side */
    padding: 10px;
}

.assign-form input[type="submit"] {
    padding: 5px 15px;
    background-color: #4CAF50; /* Or any color you like */
    color: white;
    border: none;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background-color: #333; /* Dark gray background */
    color: white;
}

th, td {
    text-align: left;
    padding: 8px;
}

th {
    text-transform: uppercase;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #ddd; /* Light gray background on hover */
}
</style>
</head>
<body>

<div class="sidebar">
    <div class="profile-pic">
        <img src="Profile_img.jpg" alt="Profile Picture" style="width:100%; height:100%; border-radius:50%;">
    </div>
    <h2>Sitter Dashboard</h2>
    <a href="#" id="homeLink">Home</a>
    <a href="#" id="postsLink">Posts</a>
    <a href="#" id="userInfoLink">User Information</a>
    <a href="Handler_Comenting.php" >Comments</a>
    <a href="LogOut.php">Logout</a>
</div>




<div class="content">
        <div id="quickTips">
        <h1>Quick Tips</h1>
            <div class="tips">
                <p>Use the links at the left to navigate through the system.</p>
                <p>View and manage current posts by clicking on "Posts".</p>
                <p>Access detailed user information under "User Information".</p>
                <p>Engage with comments and feedback in the "Comments" section.</p>
            </div>
        </div>

        <div id="Posts" class="hidden">
            <?php if (!empty($allPosts)): ?>
                <?php foreach ($allPosts as $post): ?>
                <div class="post-container">
                    <div class="post-image">
                        <img src="uploads/<?= htmlspecialchars($post['Photo']) ?>" alt="Pet Photo">
                    </div>
                    <div class="post-details1">
                        <div class="post-details2">
                            <span class="detail"><strong>Username:</strong> <?= htmlspecialchars($post['Username']) ?></span>
                            <span class="detail"><strong>Type:</strong> <?= htmlspecialchars($post['PetType']) ?></span>
                            <span class="detail"><strong>Gender:</strong> <?= htmlspecialchars($post['Gender']) ?></span>
                            <span class="detail"><strong>Status:</strong> <?= htmlspecialchars($post['Status']) ?></span>
                        </div>
                        <div class="post-details2">
                            <span class="detail"><strong>Name:</strong> <?= htmlspecialchars($post['PetName']) ?></span>
                            <span class="detail"><strong>Breed:</strong> <?= htmlspecialchars($post['Breed']) ?></span>
                            <span class="detail"><strong>Age:</strong> <?= htmlspecialchars($post['Age']) ?></span>
                            <span class="detail"><strong>Posted:</strong> <?= htmlspecialchars($post['CreatedAt']) ?></span>
                        </div>
                        <div class="health-details">
                            <div class="post-details2">
                                <p class="detail"><strong>Vaccinated:</strong> <?= htmlspecialchars($post['Vaccinated']) ?></p>
                                <p class="detail"><strong>Health Issues:</strong> <?= htmlspecialchars($post['HealthIssues']) ?></p>
                                <p class="detail"><strong>Temperament:</strong> <?= htmlspecialchars($post['Temperament']) ?></p>
                                <p class="detail"><strong>Diet Type:</strong> <?= htmlspecialchars($post['DietType']) ?></p>
                                <p class="detail"><strong>Feeding Times:</strong> <?= htmlspecialchars($post['FeedingTimes']) ?></p>
                            </div>
                            <div class="post-details2">
                             <p class="detail"><strong>Exercise Needs:</strong> <?= htmlspecialchars($post['ExerciseNeeds']) ?></p>
                             <p class="detail"><strong>Favorite Toys:</strong> <?= htmlspecialchars($post['FavoriteToys']) ?></p>
                             <p class="detail"><strong>Sitting Dates:</strong> <?= htmlspecialchars($post['SittingDates']) ?></p>
                             <p class="detail"><strong>Sitting Time:</strong> <?= htmlspecialchars($post['SittingTime']) ?></p>
                             <p class="detail"><strong>Special Instructions:</strong> <?= htmlspecialchars($post['SpecialInstructions']) ?></p>
                            </div>
                    
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>

        <div id="UserInfoContent" class="hidden">
           
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['surname']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td><?= htmlspecialchars($user['ip']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('homeLink').addEventListener('click', function(e) {
        e.preventDefault();
        toggleVisibility('quickTips');
        toggleVisibility('Posts', true);
        toggleVisibility('UserInfoContent', true);
    });

    document.getElementById('postsLink').addEventListener('click', function(e) {
        e.preventDefault();
        toggleVisibility('quickTips', true);
        toggleVisibility('Posts');
        toggleVisibility('UserInfoContent', true);
    });

    document.getElementById('userInfoLink').addEventListener('click', function(e) {
        e.preventDefault();
        toggleVisibility('quickTips', true); // Hide Quick Tips
        toggleVisibility('Posts', true); // Hide Posts
        toggleVisibility('UserInfoContent'); // Show User Information
    });



    function toggleVisibility(elementId, hide = false) {
        var element = document.getElementById(elementId);
        if (element) {
            if (hide) {
                element.style.display = 'none'; // Hide the element
            } else {
                element.style.display = 'block'; // Show the element
            }
        }
    }
});


</script>

</body>
</html>