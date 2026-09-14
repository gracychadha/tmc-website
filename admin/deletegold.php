<?php

session_start();
include("db/config.php");

$id = $_GET['id'];

$sql = "DELETE from activities where actvities_id = '$id'";
$result = mysqli_query($db, $sql);

$sql145 = "DELETE from media where media_id = '$id'";
$ssfs = mysqli_query($db, $sql145);
$sql2 = "DELETE from city where city_id = '$id'";
$result2 = mysqli_query($db, $sql2);

$sql3 = "DELETE from tours where tour_id = '$id'";
$result3 = mysqli_query($db, $sql3);


$sql4 = "DELETE from slider where s_id = '$id'";
$result4 = mysqli_query($db, $sql4);

$sql52 = "DELETE from web_content where cont_id = '$id'";
$result522 = mysqli_query($db, $sql52);

$sql6 = "DELETE from members where member_id= '$id'";
$result6 = mysqli_query($db, $sql6);

$sql7 = "DELETE from destination where dest_id= '$id'";
$result7 = mysqli_query($db, $sql7);
$sql8 = "DELETE from testimonial where test_id= '$id'";
$result8 = mysqli_query($db, $sql8);

$sql111 = "DELETE from media_dest where media_id = '$id'";
$result111= mysqli_query($db, $sql111);

$try2222222= "DELETE from festival where f_id = '$id'";
$set= mysqli_query($db, $try2222222);


$try1323123= "DELETE from blog where blog_id = '$id'";
$set11= mysqli_query($db, $try1323123);