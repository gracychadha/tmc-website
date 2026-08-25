<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

include("db/config.php");

// Fetch existing menu details for update
if (isset($_GET['id'])) {
    $encodedMenuId = $_GET['id'];
    $menuId = base64_decode($encodedMenuId);
    
    $query = "SELECT * FROM menu WHERE menu_id = $menuId";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Existing menu details
        $existingMenuName = $row['menu_name'];
        $existingStatus = $row['status'];
        $existingDescription = $row['description'];
        $existingNavigationLink = $row['navigation_link'];
        
    } else {
        echo "Menu not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}

// Update menu
if (isset($_POST['submit'])) {
    $menuName = $_POST['menu_name'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $NavigationLink = $_POST['navigation'];
    
    // Update menu details
    $updateQuery = "UPDATE menu SET menu_name = '$menuName', status = '$status', description = '$description', navigation_link='$NavigationLink' WHERE menu_id = $menuId";
    mysqli_query($db, $updateQuery);
    
    $staus = 1;
    
    $re = base64_encode($staus);
    
    echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='manage-menu.php?status=$re';</SCRIPT>");
}
?>


<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Update Menu</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
     
    <style>
        .red-text {
            color: red;
    }     
    </style>
    
</head>

<body class="">

    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

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
                                <h5 class="m-b-10">Update Menu
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
                                            <div class="form-group">
                                                <label for="menu_name" class="form-label">Menu Name <span class="red-text">*</span></label>
                                                <input type="text" name="menu_name" class="form-control"
                                                    placeholder="Enter menu name" value="<?php echo $existingMenuName; ?>"
                                                    required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="navigation" class="form-label">Navigation Link</label>
                                                <input type="text" name="navigation" class="form-control"
                                                    placeholder="Enter the navigation link" required
                                                    value="<?php echo $existingNavigationLink; ?>">
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="status" class="form-label">Status <span class="red-text">*</span></label>
                                                <select id="" name="status" class="form-control" required>
                                                    <option value="" selected disabled>Choose</option>
                                                    <option value="1" <?php echo ($existingStatus == 1) ? 'selected' : ''; ?>>
                                                        Enable</option>
                                                    <option value="0" <?php echo ($existingStatus != 1) ? 'selected' : ''; ?>>
                                                        Disable</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Description <span class="red-text">*</span></label>
                                                <textarea class="form-control" rows="5" cols="45" name="description"
                                                    placeholder="Enter menu description"
                                                    id="editor1"
                                                    required><?php echo $existingDescription; ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Update Menu
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
            $("#goldmessage").delay(5000).slideUp(300);
        });
    </script>

</body>
</html>
