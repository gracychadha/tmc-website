<?php
include("db/config.php");

// Check if a single or multiple advertisement IDs are provided for deletion
if (isset($_GET['id'])) {
    // Single deletion
    $advt_id = base64_decode($_GET['id']);
    $ad_id = mysqli_real_escape_string($db, $advt_id);
    
    // Delete the advertisement image and entry
    deleteHorizontalAd($db, $ad_id);
    
    // Redirect to the manage-horizontal-advt.php page
    header("Location: manage-horizontal-advt.php");
    exit(); // Terminate script execution after redirection
} elseif (isset($_POST['advt_ids'])) {
    // Multiple deletion
    $success_count = 0;
    $ad_ids = $_POST['advt_ids'];
    
    foreach ($ad_ids as $encoded_id) {
        $ad_id = base64_decode($encoded_id);
        $ad_id = mysqli_real_escape_string($db, $ad_id);
        
        // Delete the advertisement image and entry
        deleteHorizontalAd($db, $ad_id);
        
        $success_count++;
    }
    
    if ($success_count > 0) {
        // At least one deletion successful
        header("Location: manage-horizontal-advt.php?status=" . base64_encode(1));
    } else {
        // Error in multiple deletion
        header("Location: manage-horizontal-advt.php?status=" . base64_encode(0));
    }
    exit(); // Terminate script execution after redirection
}

// If no advertisement ID provided, redirect to manage-horizontal-advt.php
header("Location: manage-horizontal-advt.php");
exit(); // Terminate script execution after redirection

// Function to delete horizontal advertisement image and entry
function deleteHorizontalAd($db, $ad_id) {
    // Fetch the image filename from the database
    $sql = "SELECT image FROM horizontal_ad WHERE ad_id = '$ad_id'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $advt_image = $row['image'];
        
        // Delete advertisement image file
        $advt_path = "ads/horizontal/" . $advt_image;
        if (file_exists($advt_path)) {
            unlink($advt_path);
        }
    }
    
    // Delete entry from the horizontal_ad table
    $delete_sql = "DELETE FROM horizontal_ad WHERE ad_id = '$ad_id'";
    mysqli_query($db, $delete_sql);
}
?>
