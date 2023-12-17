<?php

require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: LogIn.html'); // Redirect to login page if not logged in
    exit;
}

$username = $_SESSION['username'];

// Fetch all pending posts
$stmt = $conn->prepare("SELECT petinfo.*, HealthBehavior.* FROM petinfo LEFT JOIN HealthBehavior ON petinfo.Post_Id = HealthBehavior.Post_Id WHERE petinfo.Status = 'Pending'");
$stmt->execute();
$pendingPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch all assigned posts and user details for the posts taken by the logged-in sitter
$assignedStmt = $conn->prepare("
    SELECT petinfo.*, HealthBehavior.*, users.name AS ownerName, users.surname AS ownerSurname, users.phone AS ownerPhone, users.email AS ownerEmail
    FROM petinfo 
    LEFT JOIN HealthBehavior ON petinfo.Post_Id = HealthBehavior.Post_Id 
    JOIN users ON petinfo.Username = users.username
    WHERE petinfo.Status = 'Assigned' AND petinfo.Taken_by = ?
");
$assignedStmt->bind_param('s', $username);
$assignedStmt->execute();
$assignedPosts = $assignedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$assignedStmt->close();

// Handle completion of a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete'])) {
    $postIdToComplete = $_POST['post_id'];
    $completeStmt = $conn->prepare("UPDATE petinfo SET Status = 'Completed' WHERE Post_Id = ?");
    $completeStmt->bind_param('i', $postIdToComplete);
    $completeStmt->execute();
    $completeStmt->close();
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle assignment of a post to the sitter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $postIdToAssign = $_POST['post_id'];
    $updateStmt = $conn->prepare("UPDATE petinfo SET Status = 'Assigned', Taken_by = ? WHERE Post_Id = ?");
    $updateStmt->bind_param('si', $username, $postIdToAssign);
    $updateStmt->execute();
    $updateStmt->close();
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

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
</style>
</head>
<body>


<div class="sidebar">
    <div class="profile-pic">
        <img src="Profile_img.jpg" alt="Profile Picture" style="width:100%; height:100%; border-radius:50%;">
    </div>
    <h2>Sitter Dashboard</h2>
    <a href="#" id="homeLink">Home</a>
    <a href="#" id="pendin_requests">Pending Requests</a>
    <a href="#" id="acceptedPostsLink">Accepted posts</a>
    <a href="Sitter_Comenting.php" >Comments</a>
    <a href="LogOut.php">Logout</a>
</div>

<div class="content">
    <!-- Quick Tips Section -->
    <div id="quickTips">
        <h1>Quick Tips</h1>
        <div class="tips">
            <p>Use the links on the left to navigate through the platform.</p>
            <p>Review available pet sitting jobs by clicking on "Posts".</p>
            <p>Track your scheduled sitting appointments under "My Reservations".</p>
            <p>Use the "Comments" section to communicate with pet owners and provide updates.</p>
        </div>
    </div>
    <!-- Pending Requests Section -->
    <div id="pendingRequests" class="hidden">
    <?php if (isset($pendingPosts) && !empty($pendingPosts)): ?>
    <?php foreach ($pendingPosts as $post): ?>
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
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" class="assign-form">
                    <input type="hidden" name="post_id" value="<?= $post['Post_Id'] ?>">
                    <input type="submit" name="assign" value="Assign to Me">
                </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
            <p>No pending reservations found.</p>
        <?php endif; ?>
    </div>

    <div id="acceptedPosts" class="hidden">
    <!-- Accepted Posts content -->
    <?php if (!empty($assignedPosts)): ?>
        <?php foreach ($assignedPosts as $post): ?>
            <div class="post-container">
                <div class="post-image">
                    <img src="uploads/<?= htmlspecialchars($post['Photo']) ?>" alt="Pet Photo">
                </div>
                <div class="post-details1">
                    <!-- Display details similar to the pending requests -->
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
                    <!-- Include the HealthBehavior details if needed -->
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
                <div class="post-details1">
                    <h4>Client's Information:</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($post['ownerName']) . ' ' . htmlspecialchars($post['ownerSurname']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($post['ownerPhone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($post['ownerEmail']) ?></p>
                </div>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" class="assign-form">
                    <input type="hidden" name="post_id" value="<?= $post['Post_Id'] ?>">
                    <input type="hidden" name="action" value="complete">
                    <input type="submit" name="complete" value="Mark as Completed">
                </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No accepted reservations found.</p>
    <?php endif; ?>
    </div>
    
    <!-- ... other sections ... -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('homeLink').addEventListener('click', function(e) {
        e.preventDefault();
        toggleVisibility('quickTips');
        toggleVisibility('pendingRequests', true);
        toggleVisibility('acceptedPosts', true);
    });

    document.getElementById('pendin_requests').addEventListener('click', function(e) {
        e.preventDefault();
        toggleVisibility('quickTips', true);
        toggleVisibility('pendingRequests');
        toggleVisibility('acceptedPosts', true);
    });

    document.getElementById('acceptedPostsLink').addEventListener('click', function(e) {
        e.preventDefault();
        toggleVisibility('quickTips', true);
        toggleVisibility('pendingRequests', true);
        toggleVisibility('acceptedPosts');
    });
});

function toggleVisibility(elementId, hide = false) {
    var element = document.getElementById(elementId);
    if (element) {
        if (hide) {
            element.classList.add('hidden');
        } else {
            element.classList.remove('hidden');
        }
    }
}

function toggleHealthDetails(postId) {
    var detailsDiv = document.getElementById('health-details-' + postId);
    if (detailsDiv) {
        detailsDiv.classList.toggle('hidden');
    }
}
</script>

</body>
</html>