<?php
session_start();
$_SESSION['adminId'] = base64_encode(1);
$_SESSION['userName'] = 'Test';
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db/config.php';

echo "=== TEST EDIT ===\n";
$_POST = [
    'edit-partner' => 'Save Changes',
    'idpartner' => '7',
    'existing_image' => 'partners/logo-7.webp',
    'status' => 'on'
];
$_FILES = ['image' => ['name' => '', 'error' => 4, 'tmp_name' => '', 'size' => 0, 'type' => '']];
$_SERVER['REQUEST_METHOD'] = 'POST';

// Simulate edit handler
if (isset($_POST['edit-partner'])) {
    $idpartner = intval($_POST['idpartner']);
    $status = isset($_POST['status']) ? 1 : 0;
    echo "idpartner: $idpartner, status: $status\n";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = $_FILES['image']['name'];
    } else {
        $image_path = $_POST['existing_image'];
    }
    echo "image_path: $image_path\n";

    $stmt = $db->prepare("UPDATE partners SET image = ?, status = ? WHERE idpartner = ?");
    echo "Prepare: " . ($stmt ? 'OK' : 'FAIL:' . $db->error) . "\n";
    echo "Bind: " . ($stmt->bind_param("sii", $image_path, $status, $idpartner) ? 'OK' : 'FAIL:' . $stmt->error) . "\n";
    echo "Execute: " . ($stmt->execute() ? 'OK rows=' . $stmt->affected_rows : 'FAIL:' . $stmt->error) . "\n";
    $stmt->close();
}

echo "\n=== TEST DELETE (single) ===\n";
$_POST = [
    'delete-form' => 'Yes, Delete',
    'ids' => '7'
];
$_SERVER['REQUEST_METHOD'] = 'POST';

if (isset($_POST['delete-form'])) {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $idsArray = explode(',', $ids);
        echo "IDs array: "; print_r($idsArray);

        $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
        $stmt = $db->prepare("DELETE FROM partners WHERE idpartner IN ($placeholders)");
        echo "Prepare: " . ($stmt ? 'OK' : 'FAIL:' . $db->error) . "\n";

        $types = str_repeat('i', count($idsArray));
        echo "Types: $types, count: " . count($idsArray) . "\n";
        echo "Bind: " . ($stmt->bind_param($types, ...$idsArray) ? 'OK' : 'FAIL:' . $stmt->error) . "\n";
        echo "Execute: " . ($stmt->execute() ? 'OK rows=' . $stmt->affected_rows : 'FAIL:' . $stmt->error) . "\n";
        $stmt->close();
    } else {
        echo "No IDs provided!\n";
    }
}

echo "\n=== TEST DELETE (multiple) ===\n";
$_POST = [
    'delete-form' => 'Yes, Delete',
    'ids' => '6,5'
];
$_SERVER['REQUEST_METHOD'] = 'POST';

if (isset($_POST['delete-form'])) {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $idsArray = explode(',', $ids);
        echo "IDs array: "; print_r($idsArray);

        $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
        $stmt = $db->prepare("DELETE FROM partners WHERE idpartner IN ($placeholders)");
        echo "Prepare: " . ($stmt ? 'OK' : 'FAIL:' . $db->error) . "\n";

        $types = str_repeat('i', count($idsArray));
        echo "Bind: " . ($stmt->bind_param($types, ...$idsArray) ? 'OK' : 'FAIL:' . $stmt->error) . "\n";
        echo "Execute: " . ($stmt->execute() ? 'OK rows=' . $stmt->affected_rows : 'FAIL:' . $stmt->error) . "\n";
        $stmt->close();
    }
}

echo "\n=== VERIFY ===\n";
$result = $db->query('SELECT idpartner, image, status FROM partners ORDER BY idpartner DESC');
while ($row = $result->fetch_assoc()) {
    echo "ID:{$row['idpartner']} | Img:{$row['image']} | Status:{$row['status']}\n";
}
$db->close();
