<?php
// File upload logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "uploads/";
    $image_name = basename($_FILES['file']['name']); // 'file' is the key used by TinyMCE
    $target_file = $target_dir . uniqid() . "_" . $image_name;

    // Ensure the uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        // Return the uploaded file's URL
        echo json_encode(['location' => $target_file]);
    } else {
        // Return error response
        http_response_code(400);
        echo json_encode(['error' => 'Failed to upload image.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
