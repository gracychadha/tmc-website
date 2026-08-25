<?php
include('db/config.php');

// Check if single category ID is provided for deletion
if (isset($_GET['id'])) {
    // Single category ID is provided
    $category_id = base64_decode($_GET['id']);
    
    // Delete category with provided ID
    $delete_query = "DELETE FROM parent_category WHERE parent_category_id = '$category_id'";
    $delete_result = mysqli_query($db, $delete_query);
    
    if ($delete_result) {
        // Category successfully deleted
        header("location: manage-parent-category.php?status=" . base64_encode(1)); // Redirect with success status
        exit(); // Terminate script execution after redirection
    } else {
        // Error occurred while deleting category
        header("location: manage-parent-category.php?status=" . base64_encode(-1)); // Redirect with error status
        exit(); // Terminate script execution after redirection
    }
}

// Check if multiple category IDs are provided for deletion
if (isset($_POST['category_ids'])) {
    // Array to hold category IDs
    $category_ids = $_POST['category_ids'];
    
    // Delete categories with provided IDs
    $success_count = 0;
    foreach ($category_ids as $encoded_id) {
        $category_id = base64_decode($encoded_id);
        $delete_query = "DELETE FROM parent_category WHERE parent_category_id = '$category_id'";
        $delete_result = mysqli_query($db, $delete_query);
        
        if ($delete_result) {
            $success_count++;
        }
    }
    
    if ($success_count > 0) {
        // At least one category deleted successfully
        header("location: manage-parent-category.php?status=" . base64_encode(1)); // Redirect with success status
        exit(); // Terminate script execution after redirection
    } else {
        // Error occurred while deleting categories
        header("location: manage-parent-category.php?status=" . base64_encode(-1)); // Redirect with error status
        exit(); // Terminate script execution after redirection
    }
}

// If no category ID provided, redirect to all-categories.php with error status
header("location: manage-parent-category.php?status=" . base64_encode(-1));
exit(); // Terminate script execution after redirection
?>
