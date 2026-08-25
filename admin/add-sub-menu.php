<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

include("db/config.php");
// Register user
if (isset($_POST['submit'])) {
    $menuName = $_POST['sub_menu_name'];
    $status = $_POST['status'];
    $parentMenu = $_POST['parent_menu'];
    $description=$_POST['description'];
    $NavLink=$_POST['navigation'];
    
    date_default_timezone_set('Asia/Kolkata');
    // Get the current date in Asia/Kolkata timezone
    $createdDate = date('Y-m-d H:i:s A');
    
    $query = "INSERT INTO sub_menu (sub_menu_name, status, parent_menu, description, created_date, navigation_link) VALUES ('$menuName', '$status', '$parentMenu', '$description', '$createdDate', '$NavLink')";
    if(mysqli_query($db, $query)) {
        $msg = "
        <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Thanks!</strong> Sub-Menu Added Successfully.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
        </div>
        ";
    } else {
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Error !</strong> Failed to add Sub-Menu.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
        </div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title> Add Sub Menu</title>

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
                                <h5 class="m-b-10"> Add Sub Menu
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
                    								<label for="menu" class="form-label">Parent Menu Name <span class="red-text">*</span></label>
                    								<?php
                                                          $menu_query = "SELECT * FROM menu WHERE status='1'";
                                                          $result = $db->query($menu_query);

                                                          if ($result->num_rows > 0) {
                                                               echo "<select name='parent_menu' class='form-control select' required>";
                                                               echo "<option value='' selected disabled>Choose</option>";

                                                                while ($row = $result->fetch_assoc()) {
                                                                     echo "<option value='{$row['menu_name']}'>{$row['menu_name']}</option>";
                                                                }
                                        
                                                                echo "</select>";
                                                          } else {
                                                                 echo "No menu found.";
                                                          }
                                                     ?>
                						   </div>
            					       </div>
                                   
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                               <label for="menu_name" class="form-label">Sub Menu Name <span class="red-text">*</span></label>
                    						   <input type="text" name="sub_menu_name" class="form-control" placeholder="Enter sub menu name" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                               <label for="navigation_link" class="form-label">Navigation Link <span class="red-text">*</span></label>
                    						   <input type="text" name="navigation" class="form-control" placeholder="Enter the navigation link" required>
                                            </div>
                                        </div>
                                                  
                                        
										<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                           		<div class="form-group">
                                               		<label for="status" class="form-label">Status <span class="red-text">*</span></label>
                                                		<select id="" name="status" class="form-control" required>
                                                			<option value="" selected disabled>Choose</option>
                                                    		<option value="1">Enable</option>
                                                    		<option value="0">Disable</option>
                                                	</select>
                                            	</div>
                                        </div>
                                        
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                               <label for="description" class="form-label">Description</label>
                                               <textarea class="form-control" rows="5" cols="45" name="description" placeholder="Enter sub menu description"
                                                    id="editor1"></textarea>
                                            </div>
                                        </div>

                                        <!-- Text input-->

                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Add Sub Menu
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
    
   
</body>

</html>