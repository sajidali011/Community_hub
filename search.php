<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "community_hub";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query from URL (GET request)
$search_query = isset($_GET['username']) ? $_GET['username'] : '';

// If search query exists, proceed to search in the database
if (!empty($search_query)) {
    $sql = "SELECT * FROM register WHERE username LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_query_like = "%" . $search_query . "%";  // Adding wildcards for partial matching
    $stmt->bind_param("s", $search_query_like);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If a user is found with the username
    if ($result->num_rows > 0) {
        // Loop through the results and display them
        while ($row = $result->fetch_assoc()) {
            $username = $row['username'];
            $firstname = $row['firstname'];
            $imgupload = $row['imgupload'] ? $row['imgupload'] : 'default-avatar.png'; // Default image if none
            $user_email = $row['email'];  // Assuming you have an 'email' field for user identification

            // Display search result
            echo "<div class='search-result'>";
            echo "<h3><a href='user_profile.php?email=" . urlencode($user_email) . "'>" . htmlspecialchars($username) . "</a></h3>";
            echo "<p><strong>First Name:</strong> " . htmlspecialchars($firstname) . "</p>";
            echo "<img src='" . htmlspecialchars($imgupload) . "' alt='Profile Image' width='100' height='100'>";
            echo "</div>";
        }
    } else {
        echo "<p>No user found with the username: " . htmlspecialchars($search_query) . "</p>";
    }
    $stmt->close();
} else {
    echo "<p>Please enter a username to search.</p>";
}

$conn->close();
?>

<a href="index.php">Back to Home</a>
