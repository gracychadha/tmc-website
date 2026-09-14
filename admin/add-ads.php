<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}
include('db/config.php');
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $advt_title = $_POST["advt_title"];
    $image_type = $_POST["image_type"];
    $status = $_POST["status"];
    
    // Directory to store uploaded images
    $upload_directory = "ads/";
    
    // Set directory based on image type
    $image_directory = $upload_directory . ($image_type == 'vertical' ? 'vertical/' : 'horizontal/');
    
    // Define supported image types
    $supported_image_types = array("image/jpeg", "image/png", "image/gif");
    
    // Maximum file size in bytes (5 MB)
    $max_file_size = 5 * 1024 * 1024;
    
    date_default_timezone_set('Asia/Kolkata');
    
    // Get the current date in Asia/Kolkata timezone
    $createdDate = date('Y-m-d H:i:s A');
    
    // Function to generate unique identifier for file name
    function generateUniqueFileName($file_name) {
        $uniqid = uniqid();
        $original_file_name = pathinfo($file_name, PATHINFO_FILENAME);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        return $uniqid . '_' . $original_file_name . '.' . $file_extension;
    }
    
    // File upload handling for horizontal image
    if ($image_type == 'horizontal' && isset($_FILES['horizontal_image'])) {
        $horizontal_image_tmp = $_FILES['horizontal_image']['tmp_name'];
        $horizontal_image_name = generateUniqueFileName($_FILES['horizontal_image']['name']);
        $horizontal_image_type = $_FILES['horizontal_image']['type'];
        $horizontal_image_size = $_FILES['horizontal_image']['size'];
        
        // Check if the uploaded image type is supported
        if (!in_array($horizontal_image_type, $supported_image_types)) {
            $msg = "Error: Unsupported file type for horizontal advt image.";
        } elseif ($horizontal_image_size > $max_file_size) {
            $msg = "Error: Horizontal advt image size exceeds the maximum limit (5 MB).";
        } else {
            $horizontal_image_path = $image_directory . $horizontal_image_name;
            
            if (move_uploaded_file($horizontal_image_tmp, $horizontal_image_path)) {
                // File uploaded successfully
                // Insert into horizontal_ad table
                $sql_insert = "INSERT INTO horizontal_ad (ad_title, image, status, ad_date) VALUES ('$advt_title', '$horizontal_image_name', '$status', '$createdDate')";
                
                if (mysqli_query($db, $sql_insert)) {
                    $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                            <strong><i class='feather icon-check'></i>Thanks!</strong> the Horizontal Advt has been added Successfully
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                            </div>
                            ";
                } else {
                    $msg = "Error: " . $sql_insert . "<br>" . mysqli_error($db);
                }
            } else {
                $msg = "Failed to upload horizontal advt image.";
            }
        }
    }
    
    // File upload handling for vertical image
    if ($image_type == 'vertical' && isset($_FILES['vertical_image'])) {
        $vertical_image_tmp = $_FILES['vertical_image']['tmp_name'];
        $vertical_image_name = generateUniqueFileName($_FILES['vertical_image']['name']);
        $vertical_image_type = $_FILES['vertical_image']['type'];
        $vertical_image_size = $_FILES['vertical_image']['size'];
        
        // Check if the uploaded image type is supported
        if (!in_array($vertical_image_type, $supported_image_types)) {
            $msg = "Error: Unsupported file type for vertical advt image.";
        } elseif ($vertical_image_size > $max_file_size) {
            $msg = "Error: Vertical advt image size exceeds the maximum limit (5 MB).";
        } else {
            $vertical_image_path = $image_directory . $vertical_image_name;
            
            if (move_uploaded_file($vertical_image_tmp, $vertical_image_path)) {
                // File uploaded successfully
                // Insert into vertical_ad table
                $sql_insert = "INSERT INTO vertical_ad (ad_title, image, status, ad_date) VALUES ('$advt_title', '$vertical_image_name', '$status', NOW())";
                
                if (mysqli_query($db, $sql_insert)) {
                    $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                            <strong><i class='feather icon-check'></i>Thanks!</strong> the Vertical Advt has been added Successfully
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                            </div>
                            ";
                } else {
                    $msg = "Error: " . $sql_insert . "<br>" . mysqli_error($db);
                }
            } else {
                $msg = "Failed to upload vertical advt image.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Add New Advertisement</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
    
    <script>
        window.onload = function() {
            var imageTypeDropdown = document.getElementById("image_type");
            var verticalImageField = document.getElementById("vertical_image");
            var horizontalImageField = document.getElementById("horizontal_image");

            imageTypeDropdown.addEventListener("change", function() {
                var selectedType = this.value;

                if (selectedType === "vertical") {
                    verticalImageField.disabled = false;
                    horizontalImageField.disabled = true;
                } else if (selectedType === "horizontal") {
                    verticalImageField.disabled = true;
                    horizontalImageField.disabled = false;
                }
            });
        };
    </script>
    
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
                                <h5 class="m-b-10">Add New Advertisement
                                </h5>
                            </div>
<!--                              <ul class="breadcrumb"> -->
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
                                if ($msg) 
                                {
                                    echo $msg;
                                }
                            ?>
                            <br />
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data" autocomplete="off">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                           <label for="advt_title" class="form-label">Advt Title <span class="red-text">*</span></label>
                    					   <input type="text" name="advt_title" class="form-control" placeholder="Enter Advt Title" required>
                                        </div>
                                    </div>

								   <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                       <div class="form-group">
                                            <label for="image_type" class="form-label">Advt type <span class="red-text">*</span></label>
                                            <select id="image_type" name="image_type" class="form-control" required>
    											<option value="" selected disabled>Choose</option>
    											<option value="vertical">Vertical</option>
    											<option value="horizontal">Horizontal</option>
											</select>
                                       </div>
                                  </div>
                                  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
    									<div class="form-group">
        									<label for="horizontal_image" class="form-label">Horizontal Advt Image <span class="red-text">*</span></label>
        										<div class="input-group">
           											<input type="file" name="horizontal_image" id="horizontal_image" class="form-control input-md mr-2" accept="image/*" disabled onchange="showPreviewButton('horizontal_image', 'horizontal_previewBtn')">
            										<div class="input-group-append">
                										<button type="button" id="horizontal_previewBtn" class="btn btn-secondary" onclick="showPreviewModal('horizontal_image', 'horizontal_image_preview_modal', 'horizontal_previewImage')" style="display: none;">
                    										<i class="far fa-eye"></i> Preview *
                										</button>
            										</div>
        										</div>
        									<small class="text-muted"><span style="color: red;">*Upload supported file(Max 5MB And Dimensions < 730 * 100)</span></small>
    									</div>
                                        <!-- Modal for Horizontal Image Preview -->
										<div class="modal fade" id="horizontal_image_preview_modal" tabindex="-1" role="dialog" aria-labelledby="horizontal_image_preview_modalLabel" aria-hidden="true">
    										<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        										<div class="modal-content">
            										<div class="modal-header">
                										<h5 class="modal-title" id="horizontal_image_preview_modalLabel">Horizontal Image Preview</h5>
                										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    										<span aria-hidden="true">&times;</span>
                										</button>
            										</div>
            										<div class="modal-body">
                										<img id="horizontal_previewImage" src="#" alt="Horizontal Preview Image" style="max-width: 100%; height: auto;">
            										</div>
        										</div>
    										</div>
										</div>
                                        <!-- End Modal for Horizontal Image Preview -->
									</div>

									<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    									<div class="form-group">
        									<label for="vertical_image" class="form-label">Vertical Advt Image <span class="red-text">*</span></label>
        										<div class="input-group">
            										<input type="file" name="vertical_image" id="vertical_image" class="form-control input-md mr-2" accept="image/*" disabled onchange="showPreviewButton('vertical_image', 'vertical_previewBtn')">
            										<div class="input-group-append">
                										<button type="button" id="vertical_previewBtn" class="btn btn-secondary" onclick="showPreviewModal('vertical_image', 'vertical_image_preview_modal', 'vertical_previewImage')" style="display: none;">
                    										<i class="far fa-eye"></i> Preview *
                										</button>
            										</div>
        										</div>
        									<small class="text-muted"><span style="color: red;">*Upload supported file(Max 5MB And Dimensions < 350 * 250)</span></small>
    									</div>
                                        <!-- Modal for Vertical Image Preview -->
										<div class="modal fade" id="vertical_image_preview_modal" tabindex="-1" role="dialog" aria-labelledby="vertical_image_preview_modalLabel" aria-hidden="true">
    										<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        										<div class="modal-content">
            										<div class="modal-header">
                										<h5 class="modal-title" id="vertical_image_preview_modalLabel">Vertical Image Preview</h5>
                										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    										<span aria-hidden="true">&times;</span>
                										</button>
            										</div>
            										<div class="modal-body">
                										<img id="vertical_previewImage" src="#" alt="Vertical Preview Image" style="max-width: 100%; height: auto;">
            										</div>
        										</div>
   										 	</div>
										</div>
                                        <!-- End Modal for Vertical Image Preview -->
									</div>

                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                          <div class="form-group">
                                                <label for="category_name" class="form-label">Status <span class="red-text">*</span></label>
                                                	<select id="" name="status" class="form-control" required>
                                                		<option value="" selected disabled>Choose</option>
                                                    	<option value="1">Enable</option>
                                                    	<option value="0">Disable</option>
                                                </select>
                                          </div>
                                    </div>
                                
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                         <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                             <i class="feather icon-save"></i>&nbsp; Add Advt
                                         </button>
                                   </div>
                            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
       feather.replace(); // Initialize Feather Icons
    </script>
    
    <script>
    $(document).ready(function() {
        $("#goldmessage").delay(5000).slideUp(300);
    });
    </script>
    
  	<script>
    // Function to show the preview button when a file is selected
    function showPreviewButton(inputId, previewBtnId) {
        var previewBtn = document.getElementById(previewBtnId);
        var input = document.getElementById(inputId);
        if (input && input.files.length > 0) {
            previewBtn.style.display = 'block';
        } else {
            previewBtn.style.display = 'none';
        }
    }
	</script>

	<script>
    // Function to show the preview modal
    function showPreviewModal(inputId, modalId, modalImgId) {
        var modal = document.getElementById(modalId);
        var modalImg = document.getElementById(modalImgId);
        var input = document.getElementById(inputId);
        var files = input.files;

        // Check if any file is selected
        if (files.length > 0) {
            var file = files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                modalImg.src = e.target.result;
                $(modal).modal('show'); // Show the modal
            }

            reader.readAsDataURL(file);
        }
    }
	</script>

</body>
</html>