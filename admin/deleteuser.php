<?php

session_start();
include("db/config.php");


if ($_GET['id']) {
    $id = $_GET['id'];
}
$del = "DELETE from admin where username = '$id'";

$result = mysqli_query($db, $del);
$sql9 = "DELETE from members where member_id= '$id'";
$result9 = mysqli_query($db, $sql9);

$sql8 = "DELETE from tender where id= '$id'";
$result8 = mysqli_query($db, $sql8);

$sql111 = "DELETE from media_dest where media_id = '$id'";
$result111= mysqli_query($db, $sql111);