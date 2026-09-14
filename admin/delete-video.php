<?php
include("db/config.php");

// Check if a single video ID is provided for deletion
if (isset($_GET['id'])) {
    $video_id = base64_decode($_GET['id']);
    $video_id = mysqli_real_escape_string($db, $video_id);
    
    // Delete the video file and thumbnail
    deleteVideo($db, $video_id);
    
    // Redirect to the manage-videos.php page
    header("Location: manage-videos.php");
    exit(); // Terminate script execution after redirection
}

// Check if multiple video IDs are provided for deletion
if (isset($_POST['video_ids'])) {
    // Array to hold video IDs
    $video_ids = $_POST['video_ids'];
    
    // Delete videos with provided IDs
    $success_count = 0;
    foreach ($video_ids as $encoded_id) {
        $video_id = base64_decode($encoded_id);
        // Delete the video file and thumbnail
        deleteVideo($db, $video_id);
        $success_count++;
    }
    
    // Redirect with success or error status
    if ($success_count > 0) {
        // At least one video deleted successfully
        header("location: manage-videos.php?status=" . base64_encode(1)); // Redirect with success status
    } else {
        // Error occurred while deleting videos
        header("location: manage-videos.php?status=" . base64_encode(-1)); // Redirect with error status
    }
    exit(); // Terminate script execution after redirection
}

// If no video ID provided, redirect to manage-videos.php with error status
header("location: manage-videos.php?status=" . base64_encode(-1));
exit(); // Terminate script execution after redirection

// Function to delete video file and record from the database
function deleteVideo($db, $video_id) {
    $sql = "SELECT video_filename, thumbnail_url FROM videos WHERE video_id = '$video_id'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $video_filename = $row['video_filename'];
        $thumbnail_url = $row['thumbnail_url'];
        
        // Delete video file
        $video_path = "videos/videos/" . $video_filename;
        if (file_exists($video_path)) {
            unlink($video_path);
        }
        
        // Delete thumbnail file
        $thumbnail_path = "videos/thumbnail/" . $thumbnail_url;
        if (file_exists($thumbnail_path)) {
            unlink($thumbnail_path);
        }
    }
    
    // Delete record from the database
    $delete_sql = "DELETE FROM videos WHERE video_id = '$video_id'";
    mysqli_query($db, $delete_sql);
}
?>
