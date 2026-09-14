<?php
session_start();
require_once('db/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_POST['type'])) {
    $type = sanitize_input($_POST['type']);
    
    $stmt = $db->prepare("SELECT seo_title, seo_description, seo_keywords FROM seo_settings WHERE type = ? LIMIT 1");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'data' => [
                'seo_title' => $data['seo_title'] ?? '',
                'seo_description' => $data['seo_description'] ?? '',
                'seo_keywords' => $data['seo_keywords'] ?? ''
            ]
        ]);
    } else {
        // No data found for this type
        echo json_encode([
            'success' => true,
            'data' => [
                'seo_title' => '',
                'seo_description' => '',
                'seo_keywords' => ''
            ]
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Type parameter missing']);
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>