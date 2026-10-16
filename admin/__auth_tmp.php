<?php
session_start();
$_SESSION['adminId'] = base64_encode(4);
echo "auth ok";
