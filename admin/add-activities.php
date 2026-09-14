<?php
session_start();
$upload_directory = "activities/";
error_reporting(0);
if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}
$name = $_SESSION['login_user'];
include("db/config.php");
// Register user
if (isset($_POST['submit'])) {
    $act = $_POST['category'];

    $menu = $_POST['menu'];
    $details = $_POST['editor1'];
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
            move_uploaded_file($temp_name, $upload_directory . $unique_filename);
            $query = "insert into  activities (Activities_Name,type,Add_Details,image) values('$act','$menu','$details','$unique_filename')";
            mysqli_query($db, $query);

            $msg = "
            <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
            <strong><i class='feather icon-check'></i>Thanks!</strong>Add Activities Successfully
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
            ";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Error !</strong> File size exceeds the limit of 2MB.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>";
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title> Add Activities </title>



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






                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-home"></i></span><span class="pcoded-mtext">Cities</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="add-cities.php">Add Cities</a></li>
                            <li><a href="manage-cities.php">Manage Cities</a></li>

                        </ul>
                    </li>



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
                                    class="feather icon-file"></i></span><span
                                class="pcoded-mtext">Activities</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="add-activities.php">Add Activities</a></li>
                            <li><a href="manage-activities.php">Manage Activities</a></li>

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
                    <span><?php echo $name ?></span>
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
                                <h5 class="m-b-10">Add Activities
                                </h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">

                <div class="col-sm-12">
                    <div class="card">


                        <div class="card-header table-card-header">

                            <?php
                            if ($msg) {
                                echo $msg;
                            }
                            ?>

                            <br />

                            <form class="contact-us" method="post" action="" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class=" ">
                                    <!-- Text input-->
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group"> Activities Name
                                                <label class="sr-only control-label" for="name">Category Name<span
                                                        class=" ">
                                                    </span></label>
                                                <input id="category" name="category" type="text"
                                                    placeholder=" Enter the Activities Name"
                                                    class="form-control input-md" required
                                                    oninvalid="this.setCustomValidity('Please Enter Activities Name')"
                                                    oninput="setCustomValidity('')">
                                            </div>
                                        </div>





                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Type*
                                                <label class="sr-only control-label" for="name">Show On Top Menu<span
                                                        class=" ">
                                                    </span></label>
                                                <select class="form-control" name="menu" required>

                                                    <option value="">Select</option>
                                                    <option value="Events">Events</option>
                                                    <option value="Festival">Festival</option>

                                                </select>
                                            </div>
                                        </div>




                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group"> Add Details
                                                <label class="sr-only control-label" for="name">Image<span class=" ">
                                                    </span></label>
                                                <textarea class="form-control" rows="2" cols="45" name="editor1"
                                                    id="editor1"></textarea>


                                            </div>
                                        </div>


                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Image
                                                <label class="sr-only control-label" for="name">Image<span class=" ">
                                                    </span></label>
                                                <input name="uploaded_file" type="file" class="form-control input-md"
                                                    required accept="image/*">
                                            </div>


                                        </div>

                                        <!-- Text input-->



                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Add Activities
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
    $(document).ready(function() {
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