<?php
session_start();

$upload_directory = "tour/";
if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

$name = $_SESSION['login_user'];
include("db/config.php");
$en = $_GET["id"];
$de = base64_decode($en);
// Register user
if (isset($_POST['submit'])) {

    if (!empty($_FILES["uploaded_file"]["name"])) {

        $dest = $_POST['department'];
        $title = $_POST['title'];
        $menu = $_POST['menu'];
        $weather = $_POST['weather'];
        $details = $_POST['editor1'];
        $map = $_POST['map'];
        $status = $_POST['status'];
        $temp_name = $_FILES["uploaded_file"]["tmp_name"];
        $original_name = $_FILES["uploaded_file"]["name"];
        $file_size = $_FILES["uploaded_file"]["size"];
        mysqli_select_db($db, DB_NAME);
        // Move the uploaded file to the desired directory
        $allowed_types = ["image/jpeg", "image/png", "image/gif"];
        $file_type = mime_content_type($temp_name);
        if (!in_array($file_type, $allowed_types)) {

            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Error !</strong> Please Upload Image File.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>";
        } else {

            if ($file_size < 2 * 1024 * 1024) {
                $unique_filename = uniqid() . '_' . $original_name;
                // Delete the old image
                $query = "SELECT * FROM tours WHERE tour_id ='" . $de . "'";
                $result = mysqli_query($db, $query);
                $row = mysqli_fetch_row($result);
                $f = $row['6'];
                $old = "tour/" . $f; // Path to the old image
                unlink($old);
                move_uploaded_file($temp_name, $upload_directory . $unique_filename);
                foreach($dest as $destid){
                mysqli_query($db, "UPDATE tours set tour_name ='$title', tour_menu='$menu',	weather='$weather',tour_details='$details',map='$map',tour_image='$unique_filename',status='$status'  WHERE tour_id='" . $de . "'");
                }
                $stat = 1;
                $re = base64_encode($stat);
                echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='view-tour.php?status=$re';
    </SCRIPT>");
            } else {
                $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Error !</strong> File size exceeds the limit of 2MB.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>";
            }
        }
    } else {
        $dest = $_POST['department'];
        $title = $_POST['title'];
        $menu = $_POST['menu'];
        $weather = $_POST['weather'];
        $details = $_POST['editor1'];
        $map = $_POST['map'];
        $status = $_POST['status'];
        mysqli_select_db($db, DB_NAME);
        mysqli_query($db, "UPDATE tours set  department='$desid',tour_name ='$title', tour_menu='$menu',	weather='$weather',tour_details='$details',map='$map',status='$status' WHERE tour_id='" . $de . "'");
        $stat = 1;
        $re = base64_encode($stat);
        echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='view-tour.php?status=$re';
    </SCRIPT>");
    }
}

$result = mysqli_query($db, "SELECT destination.dest_name, tours.tour_name, tours.tour_menu, tours.weather, tours.tour_details, tours.map, tours.tour_image, tours.status
FROM tours
INNER JOIN destination ON tours.dest_id = destination.dest_id WHERE tour_id='" . $de . "'");
$row1 = mysqli_fetch_row($result);


$ct = "SELECT * FROM destination";
$result1 = mysqli_query($db, $ct);
?>


<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Edit Tour</title>



    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.tiny.cloud/1/l0jt1pl0jxgk8lnq5hkx6x384hqvgjse7l8c3mnanxhhzju3/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


</head>

<body class="">

    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>





    <nav class="pcoded-navbar menupos-fixed menu-light ">
        <div class="navbar-wrapper  ">
            <div class="navbar-content scroll-div ">
                <ul class="nav pcoded-inner-navbar ">
                    <li class="nav-item pcoded-menu-caption">
                        <label>Navigation</label>
                    </li>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link " style="background:#ff5522; color:#fff;"><span
                                class="pcoded-micon"><i class="feather icon-home"></i></span><span
                                class="">Dashboard</span></a>
                    </li>






                    <!--<li class="nav-item pcoded-hasmenu">-->
                    <!--    <a href="#!" class="nav-link "><span class="pcoded-micon"><i-->
                    <!--                class="feather icon-home"></i></span><span class="pcoded-mtext">Cities</span></a>-->
                    <!--    <ul class="pcoded-submenu">-->
                    <!--        <li><a href="add-cities.php">Add Cities</a></li>-->
                    <!--        <li><a href="manage-cities.php">Manage Cities</a></li>-->

                    <!--    </ul>-->
                    <!--</li>-->



                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-credit-card"></i></span><span
                                class="pcoded-mtext">Destinations</span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-destination.php">Add Destination</a></li>
                            <li><a href="manage-destination.php">Manage Destination</a></li>



                        </ul>
                    </li>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-edit"></i></span><span class="pcoded-mtext">Tours</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="add-tour.php">Add Tour</a></li>
                            <li><a href="view-tour.php">Manage Tour</a></li>

                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-camera"></i></span><span
                                class="pcoded-mtext">Festival</span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-festival.php">Add Festival</a></li>
                            <li><a href="view-festival.php">Manage Festival</a></li>



                        </ul>
                    </li>

                    <!--<li class="nav-item pcoded-hasmenu">-->
                    <!--    <a href="#!" class="nav-link "><span class="pcoded-micon"><i-->
                    <!--                class="feather icon-camera"></i></span><span class="pcoded-mtext">Destination-->
                    <!--            Media</span></a>-->
                    <!--    <ul class="pcoded-submenu">-->

                    <!--        <li><a href="add-dest-media.php">Add Media</a></li>-->
                    <!--        <li><a href="view-dest-media.php">Manage Media</a></li>-->



                    <!--    </ul>-->
                    <!--</li>-->


                    <!--<li class="nav-item pcoded-hasmenu">-->
                    <!--    <a href="#!" class="nav-link "><span class="pcoded-micon"><i-->
                    <!--                class="feather icon-camera"></i></span><span class="pcoded-mtext">Tour Media-->
                    <!--            Gallery</span></a>-->
                    <!--    <ul class="pcoded-submenu">-->

                    <!--        <li><a href="add-media.php">Add Media</a></li>-->
                    <!--        <li><a href="view-media.php">Manage Media</a></li>-->



                    <!--    </ul>-->
                    <!--</li>-->


                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-image"></i></span><span class="pcoded-mtext">Image Slider
                            </span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-slider.php">Add Image Slider</a></li>
                            <li><a href="view-slider.php">Manage Slider</a></li>



                        </ul>
                    </li>



                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-users"></i></span><span class="pcoded-mtext">Testimonials
                            </span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-testimonials.php">Add Testimonials</a></li>
                            <li><a href="view-testimonials.php">Manage Testimonials</a></li>



                        </ul>
                    </li>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-camera"></i></span><span class="pcoded-mtext">Blog</span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-blog.php">Add Blog</a></li>
                            <li><a href="view-blog.php">Manage Blog</a></li>



                        </ul>
                    </li>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-camera"></i></span><span class="pcoded-mtext">Media
                                Gallery</span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-media.php">Add Media</a></li>
                            <li><a href="view-media.php">Manage Media</a></li>



                        </ul>
                    </li>


                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-users"></i></span><span class="pcoded-mtext">Users</span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="add-user.php">Add User</a></li>
                            <li><a href="view-user.php">Manage User</a></li>



                        </ul>
                    </li>





                    <li class="nav-item">
                        <a href="registered-users.php" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-globe"></i></span><span class="">Query Request</span></a>
                    </li>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-credit-card"></i></span><span class="pcoded-mtext">Website
                            </span></a>
                        <ul class="pcoded-submenu">



                            <li><a href="add-content.php">Add Content</a></li>
                            <li><a href="manage-content.php"> Manage Content</a></li>



                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="changepass.php" class="nav-link"><span class="pcoded-micon"><i
                                    class="feather icon-command"></i></span><span class="">Change
                                Password</span></a>
                    </li>



                    <li class="nav-item">
                        <a href="logout.php" class="nav-link " style="background:#ff5522; color:#fff;"><span
                                class="pcoded-micon"><i class="feather icon-power"></i></span><span class="">Log
                                out</span></a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>


    <header class="navbar pcoded-header navbar-expand-lg navbar-light headerpos-fixed header-blue">
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            <a href="#!" class="b-brand" style="font-size:24px;">
                ADMIN PANEL

            </a>
            <a href="#!" class="mob-toggler">
                <i class="feather icon-more-vertical"></i>
            </a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">

                    <div class="search-bar">

                        <button type="button" class="close" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="#!" class="full-screen" onClick="javascript:toggleFullScreen()"><i
                            class="feather icon-maximize"></i></a>
                </li>
            </ul>


        </div>
        </div>
        </li>

        <div class="dropdown drp-user">
            <a href="#!" class="dropdown-toggle" data-toggle="dropdown">
                <img src="assets/images/user.png" class="img-radius wid-40" alt="User-Profile-Image">
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-notification">
                <div class="pro-head">
                    <img src="assets/images/user.png" class="img-radius" alt="User-Profile-Image">
                    <span>
                        <?php echo $name ?>
                    </span>
                    <a href="logout.php" class="dud-logout" title="Logout">
                        <i class="feather icon-log-out"></i>
                    </a>
                </div>
                <ul class="pro-body">
                    <li><a href="logout.php" class="dropdown-item"><i class="feather icon-lock"></i> Log out</a></li>
                </ul>
            </div>
        </div>
        </li>
        </ul>
        </div>
    </header>


    <section class="pcoded-main-container">
        <div class="pcoded-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Edit Tour
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="row">

                <div class="col-sm-12">
                    <div class="card">
                        <?php
                        if ($msg) {
                            echo $msg;
                        }
                        ?>

                        <div class="card-header table-card-header">
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class=" ">
                                    <!-- Text input-->
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Destination
                                                <label class="sr-only control-label" for="name">Tour Name<span
                                                        class=" ">
                                                    </span></label>
                                                <?php

                                                echo "<select class='form-control multiple-select'  name='department[]' id='multiSelect' multiple='multiple' required>";
                                                while ($row = mysqli_fetch_row($result1)) {
                                                    $selected = $row1['0'] == $row['1'] ? "selected=''" : '';

                                                    echo "<option value='" . $row['0'] . "' " . $selected . ">" . $row['1'] . "</option>";
                                                }
                                                echo "</select>";
                                                ?>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Tour Title
                                                <label class="sr-only control-label" for="name">Tour Name<span
                                                        class=" ">
                                                    </span></label>
                                                <input id="title" name="title" type="text"
                                                    placeholder=" Enter the Tour Title" class="form-control input-md"
                                                    required
                                                    oninvalid="this.setCustomValidity('Please Enter Tour Name ')"
                                                    oninput="setCustomValidity('')" value="<?php echo $row1['1']; ?>">
                                            </div>
                                        </div>





                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Show On Top Menu*
                                                <label class="sr-only control-label" for="name">Show On Top Menu<span
                                                        class=" ">
                                                    </span></label>
                                                <select class="form-control" name="menu" required>
                                                    <option value="<?php echo $row1['2']; ?>">
                                                        <?php echo $row1['2']; ?>
                                                    </option>

                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>

                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Weather *
                                                <label class="sr-only control-label" for="name">Show On Top Menu<span
                                                        class=" ">
                                                    </span></label>
                                                <select class="form-control" name="weather" required>
                                                    <option value="<?php echo $row1['3']; ?>">
                                                        <?php echo $row1['3']; ?>
                                                    </option>

                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>

                                                </select>
                                            </div>
                                        </div>




                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Tour Description
                                                <label class="sr-only control-label" for="name">Image<span class=" ">
                                                    </span></label>
                                                <textarea class="form-control" rows="2" cols="45" name="editor1"
                                                    id="editor1" required><?php echo $row1['4']; ?></textarea>


                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Add Map
                                                <label class="sr-only control-label" for="name">Add Map<span class=" ">
                                                    </span></label>
                                                <select class="form-control" name="map" required>
                                                    <option value="<?php echo $row1['5']; ?>">
                                                        <?php echo $row1['5']; ?>
                                                    </option>

                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>


                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Status*
                                                <label class="sr-only control-label" for="name"> Status<span class=" ">
                                                    </span></label>
                                                <select id="" name="status" class="form-control" required>
                                                    <option value="<?php echo $row1['7']; ?>">
                                                        <?php if ($row1['7'] == 1) {
                                                            echo "Enable";
                                                        } else {
                                                            echo "Disable";
                                                        } ?>
                                                    </option>
                                                    <option value="1">Enable</option>
                                                    <option value="0">Disabe</option>



                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Tour Image
                                                <label class="sr-only control-label" for="name">Image<span class=" ">
                                                    </span></label>
                                                <input name="uploaded_file" type="file" class="form-control input-md"
                                                    accept="image/*">
                                            </div>


                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">


                                            <div class="form-group">

                                                <?php echo '<img src="tour/' . $row1['6'] . '" style="width:300px;height:120px;" class="img-thumbnail responsive" />'; ?>

                                            </div>


                                        </div>
                                        <!-- Text input-->



                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Update Tour
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>$(".multiple-select").select2({
            //   maximumSelectionLength: 2
        });</script>
    <script>
        $(document).ready(function () {
            $("#goldmessage").delay(5000).slideUp(300);
        });
    </script>

    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                value: 'First.Name',
                title: 'First Name'
            },
            {
                value: 'Email',
                title: 'Email'
            },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
                "See docs to implement AI Assistant"))
        });
    </script>
</body>

</html>