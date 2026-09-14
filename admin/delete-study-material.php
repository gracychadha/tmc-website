<?php
include("db/config.php");

// Check if a single study material ID is provided for deletion
if (isset($_GET['id'])) {
    $material_id = base64_decode($_GET['id']);
    $material_id = mysqli_real_escape_string($db, $material_id);
    
    // Fetch the study material filename
    $material_filename_query = "SELECT study_material FROM study_material WHERE material_id = '$material_id'";
    $material_filename_result = mysqli_query($db, $material_filename_query);
    $material_filename_row = mysqli_fetch_assoc($material_filename_result);
    $material_filename = $material_filename_row['study_material'];
    
    // Delete the study material file from the server directory
    $material_path = "study_material/" . $material_filename;
    if (file_exists($material_path)) {
        unlink($material_path);
    }
    
    // Delete the study material and associated categories
    deleteStudyMaterialAndCategories($db, $material_id);
    
    // Redirect to the manage-study-material.php page
    header("Location: manage-study-material.php");
    exit(); // Terminate script execution after redirection
}

// Check if multiple study material IDs are provided for deletion
if (isset($_POST['study_material_ids'])) {
    $material_ids = $_POST['study_material_ids'];
    
    // Delete study materials and associated categories with provided IDs
    foreach ($material_ids as $encoded_id) {
        $material_id = base64_decode($encoded_id);
        
        // Fetch the study material filename
        $material_filename_query = "SELECT study_material FROM study_material WHERE material_id = '$material_id'";
        $material_filename_result = mysqli_query($db, $material_filename_query);
        $material_filename_row = mysqli_fetch_assoc($material_filename_result);
        $material_filename = $material_filename_row['study_material'];
        
        // Delete the study material file from the server directory
        $material_path = "study_material/" . $material_filename;
        if (file_exists($material_path)) {
            unlink($material_path);
        }
        
        // Delete study material and associated categories
        deleteStudyMaterialAndCategories($db, $material_id);
    }
    
    // Redirect to the manage-study-material.php page
    header("Location: manage-study-material.php");
    exit(); // Terminate script execution after redirection
}

// If no study material ID provided, redirect to manage-study-material.php
header("Location: manage-study-material.php");
exit(); // Terminate script execution after redirection

// Function to delete study material and associated categories from the database
function deleteStudyMaterialAndCategories($db, $material_id) {
    // Delete associated categories first
    $delete_categories_sql = "DELETE FROM study_material_category WHERE study_material_id = '$material_id'";
    mysqli_query($db, $delete_categories_sql);
    
    // Delete study material
    $delete_material_sql = "DELETE FROM study_material WHERE material_id = '$material_id'";
    mysqli_query($db, $delete_material_sql);
}
?>
