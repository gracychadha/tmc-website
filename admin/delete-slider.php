<?php
include("db/config.php");

// Check if a single slider ID is provided for deletion
if (isset($_GET['id'])) {
    $slider_id = base64_decode($_GET['id']);
    $slider_id = mysqli_real_escape_string($db, $slider_id);
    
    // Delete the slider image file
    deleteSlider($db, $slider_id);
    
    // Redirect to the manage-slider.php page
    header("Location: manage-slider.php");
    exit(); // Terminate script execution after redirection
}

// Check if multiple slider IDs are provided for deletion
if (isset($_POST['slider_ids'])) {
    $slider_ids = $_POST['slider_ids'];
    
    // Delete sliders with provided IDs
    foreach ($slider_ids as $encoded_id) {
        $slider_id = base64_decode($encoded_id);
        deleteSlider($db, $slider_id);
    }
    
    // Redirect to the manage-slider.php page
    header("Location: manage-slider.php");
    exit(); // Terminate script execution after redirection
}

// If no slider ID provided, redirect to manage-slider.php
header("Location: manage-slider.php");
exit(); // Terminate script execution after redirection

// Function to delete slider image file and record from the database
function deleteSlider($db, $slider_id) {
    $sql = "SELECT image FROM slider WHERE s_id = '$slider_id'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $slider_image = $row['image'];
        
        // Delete slider image file
        $slider_path = "image_slider/" . $slider_image;
        if (file_exists($slider_path)) {
            unlink($slider_path);
        }
    }
    
    // Delete record from the database
    $delete_sql = "DELETE FROM slider WHERE s_id = '$slider_id'";
    mysqli_query($db, $delete_sql);
}
?>
