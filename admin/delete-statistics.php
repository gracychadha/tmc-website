<?php
include("db/config.php");

$stat_id = base64_decode($_GET['id']);
$stat_id = mysqli_real_escape_string($db, $stat_id);

$sql = "DELETE FROM statistics WHERE stat_id = '$stat_id'";
$result = mysqli_query($db, $sql);

header("Location: manage-statistics.php");

?>