<?php
include("db/config.php");

// Check if a single service ID is provided for deletion
if (isset($_GET['id'])) {
    $service_id = base64_decode($_GET['id']);
    $service_id = mysqli_real_escape_string($db, $service_id);
    
    // Delete the service image file
    deleteService($db, $service_id);
    
    // Redirect to the manage-services.php page
    header("Location: manage-services.php");
    exit(); // Terminate script execution after redirection
}

// Check if multiple service IDs are provided for deletion
if (isset($_POST['services_ids'])) {
    $service_ids = $_POST['services_ids'];
    
    // Delete services with provided IDs
    foreach ($service_ids as $encoded_id) {
        $service_id = base64_decode($encoded_id);
        deleteService($db, $service_id);
    }
    
    // Redirect to the manage-services.php page
    header("Location: manage-services.php");
    exit(); // Terminate script execution after redirection
}

// If no service ID provided, redirect to manage-services.php
header("Location: manage-services.php");
exit(); // Terminate script execution after redirection

// Function to delete service image file and record from the database
function deleteService($db, $service_id) {
    $sql = "SELECT image FROM services WHERE service_id = '$service_id'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $service_image = $row['image'];
        
        // Delete service image file
        $service_path = "services/" . $service_image;
        if (file_exists($service_path)) {
            unlink($service_path);
        }
    }
    
    // Delete record from the database
    $delete_sql = "DELETE FROM services WHERE service_id = '$service_id'";
    mysqli_query($db, $delete_sql);
}
?>
