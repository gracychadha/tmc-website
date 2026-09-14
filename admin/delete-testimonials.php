<?php
include("db/config.php");

// Check if a single testimonial ID is provided for deletion
if (isset($_GET['id'])) {
    $test_id = base64_decode($_GET['id']);
    $test_id = mysqli_real_escape_string($db, $test_id);
    
    // Delete the testimonial image file from the server directory
    deleteTestimonialImage($db, $test_id);
    
    // Delete the testimonial record from the database
    deleteTestimonial($db, $test_id);
    
    // Redirect to the manage-testimonials.php page
    header("Location: manage-testimonials.php");
    exit(); // Terminate script execution after redirection
}

// Check if multiple testimonial IDs are provided for deletion
if (isset($_POST['testimonial_ids'])) {
    $testimonial_ids = $_POST['testimonial_ids'];
    
    // Delete testimonials and their associated image files with provided IDs
    foreach ($testimonial_ids as $encoded_id) {
        $test_id = base64_decode($encoded_id);
        
        // Delete the testimonial image file from the server directory
        deleteTestimonialImage($db, $test_id);
        
        // Delete the testimonial record from the database
        deleteTestimonial($db, $test_id);
    }
    
    // Redirect to the manage-testimonials.php page
    header("Location: manage-testimonials.php");
    exit(); // Terminate script execution after redirection
}

// If no testimonial ID provided, redirect to manage-testimonials.php
header("Location: manage-testimonials.php");
exit(); // Terminate script execution after redirection

// Function to delete the testimonial image file from the server directory
function deleteTestimonialImage($db, $test_id) {
    $image_query = "SELECT image FROM testimonial WHERE test_id = '$test_id'";
    $image_result = mysqli_query($db, $image_query);
    $image_row = mysqli_fetch_assoc($image_result);
    $image_filename = $image_row['image'];
    $image_path = "testimonial/" . $image_filename;
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Function to delete the testimonial record from the database
function deleteTestimonial($db, $test_id) {
    $delete_query = "DELETE FROM testimonial WHERE test_id = '$test_id'";
    mysqli_query($db, $delete_query);
}
?>
