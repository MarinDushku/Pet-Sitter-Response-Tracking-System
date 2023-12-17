<?php
require 'config.php';

// Check if the session variable for the username is set
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare the SQL statement using the correct table and column names
    $stmt = $conn->prepare("SELECT * FROM petinfo WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all posts as an associative array
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Check for the showDetails request
    $showDetailsForPostId = filter_input(INPUT_GET, 'showDetailsForPostId', FILTER_VALIDATE_INT);
    
    // If we need to show details, fetch them now
    $healthDetails = [];
    if ($showDetailsForPostId) {
        $stmtHealth = $conn->prepare("SELECT * FROM HealthBehavior WHERE Post_Id = ?");
        $stmtHealth->bind_param("i", $showDetailsForPostId);
        $stmtHealth->execute();
        $healthResult = $stmtHealth->get_result();
        $healthDetails = $healthResult->fetch_assoc();
        $stmtHealth->close();
    }
} else {
    // If the session variable is not set, redirect to the login page
    header('Location: login.php');
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
        height: 50px; /* Default height to match other inputs */
      
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

button {
  padding: 10px 20px;
  background-color: #4CAF50; /* Green background */
  color: white;
  border: none;
  cursor: pointer;
  margin: 10px;
}

.health-details {
  background-color: #f9f9f9;
  padding: 10px;
  border-radius: 5px;
  display: none; /* Hidden by default */
}

.read-more-btn{
    color: #0984e3;
}

.read-more-text{
    display: none;
}

.read-more-text--show{
    display: inline;
}
.read-more-container{
    display: flex;
    flex-direction: column;
    color: #111;
    gap: 1rem;
}






.form-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
form {
    display: grid;
    grid-gap: 1rem;
    padding: 1rem;
}

/* Form field styles */
.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: .5rem;
    color: #34495e;
    font-weight: bold;
}

input[type="text"],
input[type="number"],
input[type="email"],
input[type="tel"],
select,
textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    resize: vertical; /* Allows only vertical resizing */
}

input[type="text"]:required,
input[type="number"]:required,
textarea:required {
    background: #ecf0f1;
}

input[type="file"] {
    border: none;
}

/* Radio button group */
.radio-group {
    display: flex;
    align-items: center;
}

.radio-group label {
    margin-right: 1rem;
    font-weight: normal;
}

input[type="radio"] {
    margin-right: .5rem;
}

/* Buttons */
.submit-btn, button {
    width: 100%;
    padding: 1rem;
    border: none;
    background: #3498db;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.4rem;
    text-transform: uppercase;
    transition: background 0.3s ease;
}

.submit-btn:hover, button:hover {
    background: #2980b9;
}

button {
    margin-top: 1rem;
    background: #95a5a6;
}

/* Fieldset and Legend */
fieldset {
    border: 1px solid #bdc3c7;
    padding: 1rem;
    border-radius: 4px;
}

legend h2 {
    padding: 0 1rem;
    color: #3498db;
}

/* Responsive design */
@media (max-width: 768px) {
    .form-container {
        margin: 1rem;
    }
}

@media (max-width: 480px) {
    html {
        font-size: 50%;
    }

    .form-container {
        padding: 1rem;
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
  <a href="#" id="homeLink">Home</a>
  <a href="#" id="newReservationLink">New Reservation</a>
  <a href="#" id="viewReservationsLink">View Reservations</a>
  <a href="Client_Comenting.php" >Feedback</a>
  <a href="LogOut.php">Logout</a>
</div>
<!-- Home    ----------------------------------------------------------------------------------------------------------------------------------------------------->
<div class="content">
  <div id="quickTips">
    <h1>Quick Tips</h1>
    <div class="tips">
      <p>Use the links at the left to navigate through the system.</p>
      <p>View and make new bookings by clicking on "New Reservation".</p>
      <p>After payment, your reservation details will be available under "View Reservations".</p>
      <p>For any assistance or feedback, use the "Feedback" section.</p>
    </div>
  </div>
<!--  The form section starts here  --------------------------------------------------------------------------------------------------------------------------------------->
  <!-- This form will be hidden initially -->
  <div id="newReservationForm" class="hidden">
    <div class="form-container">
      <h1>Pet Sitting Request Form</h1>
      <p>Please fill out this form to request pet sitting services. Fields marked with an * are required.</p>
      <form action="formPost.php" method="post" enctype="multipart/form-data" id="petForm" novalidate>
          
          <fieldset>
              <legend><h2>Pet Information</h2></legend>
              <div class="form-group">
                  <label for="petName">Pet's Name:</label><br>
                  <input type="text" id="petName" name="petName" required><br>
              </div>
              <div class="form-group">
                  <label for="petType">Type of Pet:</label><br>
                  <input type="text" id="petType" name="petType" placeholder="e.g., Dog, Cat, Bird" required><br>
              </div>
              <div class="form-group">
                  <label for="breed">Breed (if applicable):</label><br>
                  <input type="text" id="breed" name="breed"><br>
              </div>
              <div class="form-group">
                  <label for="age">Age of Pet:</label><br>
                  <input type="number" id="age" name="age" required><br>
              </div>
              <div class="form-group">
                  <label for="gender">Gender of Pet:</label><br>
                  <select id="gender" name="gender">
                      <option value="">Select Gender</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                  </select><br>
              </div>
              <div class="form-group">
                  <label for="photo">Pet's Photo:</label><br>
                  <input type="file" id="photo" name="photo" class="photo-input" required><br>
              </div>
          </fieldset>
          
          
          <!-- Health and Behavior -->
          <fieldset>
              <legend><h2>Health and Behavior</h2></legend>
              <div class="form-group">
                  <label>Is your pet vaccinated? *</label><br>
                  <div class="radio-group">
                      <input type="radio" id="vaccinatedYes" name="vaccinated" value="Yes" required>
                      <label for="vaccinatedYes">Yes</label>
                      <input type="radio" id="vaccinatedNo" name="vaccinated" value="No" required>
                      <label for="vaccinatedNo">No</label>
                  </div>
              </div>
              <!-- more form groups for health and behavior -->

              <div class="form-group">
                  <label for="healthIssues">Any specific health issues or allergies? Please specify:</label><br>
                  <textarea id="healthIssues" name="healthIssues"></textarea><br>
              </div>
              <div class="form-group">
                  <label for="temperament">Describe your pet's temperament and behavior:</label><br>
                  <textarea id="temperament" name="temperament" required></textarea><br>
              </div>
              <h3>Diet and Feeding Schedule:</h3>
          <div class="form-group">
              <label for="foodType">Type of food:</label><br>
              <input type="text" id="foodType" name="foodType" required><br>
          </div>
          <div class="form-group">
              <label for="feedingTimes">Feeding times:</label><br>
              <input type="text" id="feedingTimes" name="feedingTimes" placeholder="e.g., 8 AM, 12 PM, 6 PM" required><br>
          </div>
          <h3>Exercise and Play:</h3>
          <div class="form-group">
              <label for="exerciseNeeds">Exercise needs:</label><br>
              <input type="text" id="exerciseNeeds" name="exerciseNeeds" placeholder="e.g., walks, playtime" required><br>
          </div>
          <div class="form-group">
              <label for="favoriteToys">Favorite toys and activities:</label><br>
              <textarea id="favoriteToys" name="favoriteToys" placeholder="e.g., Frisbee, Tug Rope"></textarea><br>
          </div>
          <h3>Pet Sitting Details:</h3>
          <div class="form-group">
              <label for="sittingDates">Dates for pet sitting:</label><br>
              <input type="text" id="sittingDates" name="sittingDates" placeholder="e.g., Jan 1 - Jan 5" required><br>
          </div>
          <div class="form-group">
              <label for="sittingTime">Preferred time for sitting:</label><br>
              <input type="text" id="sittingTime" name="sittingTime" placeholder="e.g., Morning, Afternoon, Evening" required><br>
          </div>
          <div class="form-group">
              <label for="specialInstructions">Any special instructions or routines?</label><br>
              <textarea id="specialInstructions" name="specialInstructions"></textarea><br>
          </div>
          </fieldset>
          <br>
         
          
          <button type="submit" class="submit-btn">Submit Request</button>
          
      </form>
      <button onclick="location.href='Client_Home.html'" >Back </button>
  </div>
  </div>

  <!-- Corrected ID for the "View Reservations" content div -->
<div id="View_Reservations" class="content hidden">
<?php if (isset($posts) && !empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
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
        
          <?php if (isset($_GET['showDetailsForPostId']) && $_GET['showDetailsForPostId'] == $post['Post_Id']): ?>
            
            <?php
              // Fetch HealthBehavior data from the database
              $stmt = $conn->prepare("SELECT * FROM HealthBehavior WHERE Post_Id = ?");
              $stmt->bind_param("i", $post['Post_Id']);
              $stmt->execute();
              $healthDetails = $stmt->get_result()->fetch_assoc();
              $stmt->close();
            ?>
              <div class="read-more-text">
          
            <span class="detail"><strong>Vaccinated:</strong> <?= htmlspecialchars($healthDetails['Vaccinated']) ?></span>
            <span class="detail"><strong>Vaccinated:</strong> <?= htmlspecialchars($healthDetails['Vaccinated']) ?></span>
            <span class="detail"><strong>Health Issues:</strong> <?= htmlspecialchars($healthDetails['HealthIssues']) ?></span>
            <span class="detail"><strong>Temperament:</strong> <?= htmlspecialchars($healthDetails['Temperament']) ?></span>
            <span class="detail"><strong>Diet Type:</strong> <?= htmlspecialchars($healthDetails['DietType']) ?></span>
            <span class="detail"><strong>Feeding Times:</strong> <?= htmlspecialchars($healthDetails['FeedingTimes']) ?></span>
            <span class="detail"><strong>Exercise Needs:</strong> <?= htmlspecialchars($healthDetails['ExerciseNeeds']) ?></span>
            <span class="detail"><strong>Favorite Toys:</strong> <?= htmlspecialchars($healthDetails['FavoriteToys']) ?></span>
            <span class="detail"><strong>Sitting Dates:</strong> <?= htmlspecialchars($healthDetails['SittingDates']) ?></span>
            <span class="detail"><strong>Sitting Time:</strong> <?= htmlspecialchars($healthDetails['SittingTime']) ?></span>
            <span class="detail"><strong>Special Instructions:</strong> <?= htmlspecialchars($healthDetails['SpecialInstructions']) ?></span>
          </div>
          
          <?php endif; ?>
        </div>
        
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No reservations found.</p>
  <?php endif; ?>


  




</div>
<script>
// JavaScript to switch between Quick Tips, New Reservation Form, and View Reservations
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('homeLink').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('quickTips').classList.remove('hidden');
        document.getElementById('newReservationForm').classList.add('hidden');
        document.getElementById('View_Reservations').classList.add('hidden');
    });

    document.getElementById('newReservationLink').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('quickTips').classList.add('hidden');
        document.getElementById('newReservationForm').classList.remove('hidden');
        document.getElementById('View_Reservations').classList.add('hidden');
    });

    document.getElementById('viewReservationsLink').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('quickTips').classList.add('hidden');
    document.getElementById('newReservationForm').classList.add('hidden');
    document.getElementById('View_Reservations').classList.remove('hidden');
});
});

function toggleHealthDetails(postId, element) {
  const detailsDiv = document.getElementById('health-details-' + postId);
  
  // Check if details are already loaded
  if (!detailsDiv.dataset.loaded) {
    // Data is not loaded, fetch from the server
    fetch('getHealthDetails.php?postId=' + postId)
      .then(response => response.json())
      .then(data => {
        detailsDiv.innerHTML = data.html; // Assuming the server sends back HTML
        detailsDiv.dataset.loaded = 'true'; // Mark as loaded
        detailsDiv.classList.remove('hidden'); // Show details
        element.textContent = 'Hide Information'; // Change button text
      })
      .catch(error => {
        console.error('Error fetching health details:', error);
      });
  } else {
    // Toggle visibility if data is already loaded
    if (detailsDiv.classList.contains('hidden')) {
      detailsDiv.classList.remove('hidden');
      element.textContent = 'Hide Information';
    } else {
      detailsDiv.classList.add('hidden');
      element.textContent = 'View More Information';
    }
  }
}



const parentContainer =  document.querySelector('.content');

parentContainer.addEventListener('click', event=>{

    const current = event.target;

    const isReadMoreBtn = current.className.includes('read-more-btn');

    if(!isReadMoreBtn) return;

    const currentText = event.target.parentNode.querySelector('.read-more-text');

    currentText.classList.toggle('read-more-text--show');

    current.textContent = current.textContent.includes('Read More') ? "Read Less..." : "Read More...";

})
</script>


</html>
