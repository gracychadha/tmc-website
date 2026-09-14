<?php
include("db/config.php");

// Check if single or multiple post IDs are provided for deletion
if (isset($_GET['id'])) {
    // Single deletion
    $post_id = base64_decode($_GET['id']);
    $post_id = mysqli_real_escape_string($db, $post_id);
    
    // Delete the post image file from the server directory
    deletePostImage($db, $post_id);
    
    // Delete post categories
    $delete_categories_sql = "DELETE FROM post_categories WHERE post_id = '$post_id'";
    $delete_categories_result = mysqli_query($db, $delete_categories_sql);
    
    // Delete the post record from the database
    $delete_post_sql = "DELETE FROM post WHERE post_id = '$post_id'";
    $delete_post_result = mysqli_query($db, $delete_post_sql);
    
    if ($delete_categories_result && $delete_post_result) {
        // Single deletion successful
        header("Location: manage-post.php?status=" . base64_encode(1));
    } else {
        // Error in single deletion
        header("Location: manage-post.php?status=" . base64_encode(0));
    }
    exit(); // Terminate script execution after redirection
} elseif (isset($_POST['post_ids'])) {
    // Multiple deletion
    $success_count = 0;
    $post_ids = $_POST['post_ids'];
    
    foreach ($post_ids as $encoded_id) {
        $post_id = base64_decode($encoded_id);
        $post_id = mysqli_real_escape_string($db, $post_id);
        
        // Delete the post image file from the server directory
        deletePostImage($db, $post_id);
        
        // Delete post categories
        $delete_categories_sql = "DELETE FROM post_categories WHERE post_id = '$post_id'";
        $delete_categories_result = mysqli_query($db, $delete_categories_sql);
        
        // Delete the post record from the database
        $delete_post_sql = "DELETE FROM post WHERE post_id = '$post_id'";
        $delete_post_result = mysqli_query($db, $delete_post_sql);
        
        if ($delete_categories_result && $delete_post_result) {
            $success_count++;
        }
    }
    
    if ($success_count > 0) {
        // At least one deletion successful
        header("Location: manage-post.php?status=" . base64_encode(1));
    } else {
        // Error in multiple deletion
        header("Location: manage-post.php?status=" . base64_encode(0));
    }
    exit(); // Terminate script execution after redirection
}

// If no post ID provided, redirect to manage-post.php
header("Location: manage-post.php");
exit(); // Terminate script execution after redirection

// Function to delete the post image file from the server directory
function deletePostImage($db, $post_id) {
    $image_query = "SELECT image FROM post WHERE post_id = '$post_id'";
    $image_result = mysqli_query($db, $image_query);
    $image_row = mysqli_fetch_assoc($image_result);
    $image_filename = $image_row['image'];
    $image_path = "post/" . $image_filename;
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}
?>
