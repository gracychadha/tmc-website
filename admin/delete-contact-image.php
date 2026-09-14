<?php
include("db/config.php");

// Check if a single slider ID is provided for deletion
if (isset($_GET['id'])) {
    $id = base64_decode($_GET['id']);
    $id = mysqli_real_escape_string($db, $id);
    
    // Delete the slider image file
    deleteSlider($db, $id);
    
    // Redirect to the manage-slider.php page
    header("Location: manage-contact-image.php");
    exit(); // Terminate script execution after redirection
}

// Check if multiple slider IDs are provided for deletion
if (isset($_POST['news_ids'])) {
    $news_ids = $_POST['news_ids'];
    
    // Delete sliders with provided IDs
    foreach ($news_ids as $encoded_id) {
        $news_ids = base64_decode($encoded_id);
        deleteSlider($db, $news_ids);
    }
    
    // Redirect to the manage-slider.php page
    header("Location: manage-contact-image.php");
    exit(); // Terminate script execution after redirection
}

// If no slider ID provided, redirect to manage-slider.php
header("Location: manage-contact-image.php");
exit(); // Terminate script execution after redirection

// Function to delete slider image file and record from the database
function deleteSlider($db, $id) {
    $sql = "SELECT img_url FROM contactimg WHERE id = '$id'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $slider_image = $row['img_url'];
        
        // Delete slider image file
        $slider_path = "contact/" . $slider_image;
        if (file_exists($slider_path)) {
            unlink($slider_path);
        }
    }
    
    // Delete record from the database
    $delete_sql = "DELETE FROM contactimg WHERE id = '$id'";
    mysqli_query($db, $delete_sql);
}
?>
