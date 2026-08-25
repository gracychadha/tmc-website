<?php
include("db/config.php");

// Check if single or multiple media IDs are provided for deletion
if (isset($_GET['id'])) {
    // Single deletion
    $media_id = base64_decode($_GET['id']);
    $media_id = mysqli_real_escape_string($db, $media_id);
    
    // Delete the media images and entries
    deleteMedia($db, $media_id);
    
    // Redirect to the manage-media.php page
    header("Location: manage-media.php");
    exit(); // Terminate script execution after redirection
} elseif (isset($_POST['media_ids'])) {
    // Multiple deletion
    $success_count = 0;
    $media_ids = $_POST['media_ids'];
    
    foreach ($media_ids as $encoded_id) {
        $media_id = base64_decode($encoded_id);
        $media_id = mysqli_real_escape_string($db, $media_id);
        
        // Delete the media images and entries
        deleteMedia($db, $media_id);
        
        $success_count++;
    }
    
    if ($success_count > 0) {
        // At least one deletion successful
        header("Location: manage-media.php?status=" . base64_encode(1));
    } else {
        // Error in multiple deletion
        header("Location: manage-media.php?status=" . base64_encode(0));
    }
    exit(); // Terminate script execution after redirection
}

// If no media ID provided, redirect to manage-media.php
header("Location: manage-media.php");
exit(); // Terminate script execution after redirection

// Function to delete media images and entries
function deleteMedia($db, $media_id) {
    // Fetch the image filenames from the database
    $sql = "SELECT image_filename FROM media_images WHERE media_id = '$media_id'";
    $result = mysqli_query($db, $sql);
    
    // Delete each image file
    while ($row = mysqli_fetch_assoc($result)) {
        $image_filename = $row['image_filename'];
        $image_path = "media/" . $image_filename;
        
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete entries from the media_images table
    $delete_images_sql = "DELETE FROM media_images WHERE media_id = '$media_id'";
    mysqli_query($db, $delete_images_sql);
    
    // Delete entry from the media table
    $delete_sql = "DELETE FROM media WHERE media_id = '$media_id'";
    mysqli_query($db, $delete_sql);
}
?>
