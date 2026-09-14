<?php

session_start();

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

include("db/config.php");

if (count($_POST) > 0) {
    $result = mysqli_query($db, "SELECT * FROM admin WHERE username='" . $_SESSION["login_user"] . "'");
    $row = mysqli_fetch_array($result);
    
    $old = md5($_POST["oldp"]);
    $new = md5($_POST["newp"]);
    if ($old == $row["password"]) {
        // Check if new password and confirm password match
        if ($_POST["newp"] == $_POST["confirmp"])
        {
            // Check if old password and new password are same
            if ($old != $new) {
                mysqli_query($db, "UPDATE admin SET password='" . $new . "' WHERE username='" . $_SESSION["login_user"] . "'");
                $message = "Password Changed";
                echo ("<SCRIPT LANGUAGE='JavaScript'> window.location.href='logout.php'; </SCRIPT>");
            } else {
                $message = " Old and new passwords are the same. Please try a different one.";
            }
        } else {
            $message = " New Password and Confirm Password do not match";
        }
    } else {
        $message = " Current Password is not correct";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Change Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Codedthemes" />
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .red-text {
            color: red;
    }     
    </style>
    
</head>

<body>

    <!-- Header -->
    <?php
    include("header.php");
    ?>
    <!-- /Header -->

    <!-- navbar -->
    <?php
    include("navbar.php");
    ?>
    <!-- /navbar -->


    <section class="pcoded-main-container">
        <div class="pcoded-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Change Password
                                </h5>
                            </div>
<!--                             <ul class="breadcrumb"> -->
<!--                                 <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a> -->
<!--                                 </li> -->
<!--                             </ul> -->
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header table-card-header">
                            <?php

                            if (isset($message)) {


                                echo " <div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='successMessage'>
  <strong><i class=' feather  icon icon-info'></i>Error!</strong>$message.
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
  </button>
</div> ";
                            }
                            ?>
                            <br />
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data" autocomplete="off">
                                <div class=" ">
                                    <!-- Text input-->
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Old Password <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">Old Password<span class=" "> </span></label>
                                                <input id="name" name="oldp" type="password" placeholder="Enter old password" class="form-control input-md" required oninvalid="this.setCustomValidity('Please Enter old Password')" oninput="setCustomValidity('')">
                                            </div>
                                        </div>


                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">New Password <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">New Password<span class=" "> </span></label>
                                                <input id="name" name="newp" type="password" placeholder="Enter new password" class="form-control input-md" required oninvalid="this.setCustomValidity('Please create new Password')" oninput="setCustomValidity('')">
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Confirm Password <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">Confirm Password<span class=" "> </span></label>
                                                <input id="name" name="confirmp" type="password" placeholder="Confirm new password" class="form-control input-md" required oninvalid="this.setCustomValidity('Please confirm new Password')" oninput="setCustomValidity('')">
                                            </div>
                                        </div>
                                        <!-- End Confirm Password -->

                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save lg"></i>&nbsp;Change Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="dt-responsive table-responsive">


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <!--<script src="assets/js/menu-setting.min.js"></script>-->

    <script src="assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/plugins/buttons.colVis.min.js"></script>
    <script src="assets/js/plugins/buttons.print.min.js"></script>
    <script src="assets/js/plugins/pdfmake.min.js"></script>
    <script src="assets/js/plugins/jszip.min.js"></script>
    <script src="assets/js/plugins/dataTables.buttons.min.js"></script>
    <script src="assets/js/plugins/buttons.html5.min.js"></script>
    <script src="assets/js/plugins/buttons.bootstrap4.min.js"></script>
    <script src="assets/js/pages/data-export-custom.js"></script>

    <script>
        $(document).ready(function () {
            $("#successMessage").delay(5000).slideUp(300);

    });
    </script>
</body>

</html>