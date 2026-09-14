<?php
include('db/config.php');

// Check if single category ID is provided for deletion
if (isset($_GET['id'])) {
    // Single category ID is provided
    $submenu_id = base64_decode($_GET['id']);
    
    // Delete category with provided ID
    $delete_query = $sql = "DELETE FROM sub_menu WHERE sub_menu_id = '$submenu_id'";
    $delete_result = mysqli_query($db, $delete_query);
    
    if ($delete_result) {
        // Category successfully deleted
        header("location: manage-sub-menu.php?status=" . base64_encode(1)); // Redirect with success status
        exit(); // Terminate script execution after redirection
    } else {
        // Error occurred while deleting category
        header("location: manage-sub-menu.php?status=" . base64_encode(-1)); // Redirect with error status
        exit(); // Terminate script execution after redirection
    }
}

// Check if multiple category IDs are provided for deletion
if (isset($_POST['sub_menu_ids'])) {
    // Array to hold category IDs
    $submenu_ids = $_POST['sub_menu_ids'];
    
    // Delete categories with provided IDs
    $success_count = 0;
    foreach ($submenu_ids as $encoded_id) {
        $submenu_id = base64_decode($encoded_id);
        $delete_query = $sql = "DELETE FROM sub_menu WHERE sub_menu_id = '$submenu_id'";
        $delete_result = mysqli_query($db, $delete_query);
        
        if ($delete_result) {
            $success_count++;
        }
    }
    
    if ($success_count > 0) {
        // At least one category deleted successfully
        header("location: manage-sub-menu.php?status=" . base64_encode(1)); // Redirect with success status
        exit(); // Terminate script execution after redirection
    } else {
        // Error occurred while deleting categories
        header("location: manage-sub-menu.php?status=" . base64_encode(-1)); // Redirect with error status
        exit(); // Terminate script execution after redirection
    }
}

// If no category ID provided, redirect to all-categories.php with error status
header("location: manage-sub-menu.php?status=" . base64_encode(-1));
exit(); // Terminate script execution after redirection
?>

