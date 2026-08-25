<?php
require("db/config.php");

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode($data);
    $stmt->close();
}
?>