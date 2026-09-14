<?php
include("db/config.php");

// Check if single or multiple client IDs are provided for deletion
if (isset($_GET['id'])) {
    // Single deletion
    $client_id = base64_decode($_GET['id']);
    $client_id = mysqli_real_escape_string($db, $client_id);
    
    // Delete the client image and entry
    deleteClient($db, $client_id);
    
    // Redirect to the manage-client.php page
    header("Location: manage-client.php");
    exit(); // Terminate script execution after redirection
} elseif (isset($_POST['client_ids'])) {
    // Multiple deletion
    $success_count = 0;
    $client_ids = $_POST['client_ids'];
    
    foreach ($client_ids as $encoded_id) {
        $client_id = base64_decode($encoded_id);
        $client_id = mysqli_real_escape_string($db, $client_id);
        
        // Delete the client image and entry
        deleteClient($db, $client_id);
        
        $success_count++;
    }
    
    if ($success_count > 0) {
        // At least one deletion successful
        header("Location: manage-client.php?status=" . base64_encode(1));
    } else {
        // Error in multiple deletion
        header("Location: manage-client.php?status=" . base64_encode(0));
    }
    exit(); // Terminate script execution after redirection
}

// If no client ID provided, redirect to manage-client.php
header("Location: manage-client.php");
exit(); // Terminate script execution after redirection

// Function to delete client image and entry
function deleteClient($db, $client_id) {
    // Fetch the image filename from the database
    $sql = "SELECT image FROM client WHERE Client_id = '$client_id'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $client_image = $row['image'];
        
        // Delete client image file
        $client_path = "client/" . $client_image;
        if (file_exists($client_path)) {
            unlink($client_path);
        }
    }
    
    // Delete entry from the client table
    $delete_sql = "DELETE FROM client WHERE Client_id = '$client_id'";
    mysqli_query($db, $delete_sql);
}
?>
