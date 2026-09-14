<?php
require_once('admin/db/config.php');
echo "=== contact table ===" . PHP_EOL;
$r = mysqli_query($db, "DESCRIBE contact");
while ($row = mysqli_fetch_assoc($r)) {
    echo $row['Field'] . " | " . $row['Type'] . PHP_EOL;
}
echo PHP_EOL . "=== captcha table ===" . PHP_EOL;
$r2 = mysqli_query($db, "DESCRIBE captcha");
if ($r2 && mysqli_num_rows($r2) > 0) {
    while ($row = mysqli_fetch_assoc($r2)) {
        echo $row['Field'] . " | " . $row['Type'] . PHP_EOL;
    }
} else {
    echo "captcha table missing" . PHP_EOL;
}
echo PHP_EOL . "=== google_captcha table ===" . PHP_EOL;
$r3 = mysqli_query($db, "DESCRIBE google_captcha");
if ($r3 && mysqli_num_rows($r3) > 0) {
    while ($row = mysqli_fetch_assoc($r3)) {
        echo $row['Field'] . " | " . $row['Type'] . PHP_EOL;
    }
    $r4 = mysqli_query($db, "SELECT * FROM google_captcha LIMIT 1");
    if ($r4 && mysqli_num_rows($r4) > 0) {
        $row = mysqli_fetch_assoc($r4);
        echo "Data: " . print_r($row, true) . PHP_EOL;
    }
} else {
    echo "google_captcha table missing" . PHP_EOL;
}
echo PHP_EOL . "=== captcha table data ===" . PHP_EOL;
$r5 = mysqli_query($db, "SELECT * FROM captcha LIMIT 1");
if ($r5 && mysqli_num_rows($r5) > 0) {
    $row = mysqli_fetch_assoc($r5);
    echo "Data: " . print_r($row, true) . PHP_EOL;
} else {
    echo "captcha table empty or missing" . PHP_EOL;
}
