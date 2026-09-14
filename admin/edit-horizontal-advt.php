<?php
session_start();
$upload_directory = "ads/horizontal/";
error_reporting(0);
if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}
include("db/config.php");

// Fetch existing slider details for update
if (isset($_GET['id'])) {
    $encodedAdvtId = $_GET['id'];
    $advtId = base64_decode($encodedAdvtId);
    
    $query = "SELECT * FROM horizontal_ad WHERE ad_id = $advtId";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Existing slider details
        $existingTitle = $row['ad_title'];
        $existingStatus = $row['status'];
    } else {
        echo "Advertisement not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}

// Update slider
if (isset($_POST['submit'])) {
    $title = $_POST['advt_title'];
    $status = $_POST['status'];
    
    // Check if a new image is uploaded
    if ($_FILES["uploaded_file"]["size"] > 0) {
        $temp_name = $_FILES["uploaded_file"]["tmp_name"];
        $original_name = $_FILES["uploaded_file"]["name"];
        $file_size = $_FILES["uploaded_file"]["size"];
        
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
            if ($file_size < 5 * 1024 * 1024) {
                $unique_filename = uniqid() . '_' . $original_name;
                move_uploaded_file($temp_name, $upload_directory . $unique_filename);
                
                // Update slider details with the new image
                $updateQuery = "UPDATE horizontal_ad SET ad_title = '$title', image = '$unique_filename', status= '$status' WHERE ad_id = $advtId";
                mysqli_query($db, $updateQuery);
            } else {
                $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
            <strong><i class='feather icon-check'></i>Error !</strong> File size exceeds the limit of 5MB.
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
            }
        }
    } else {
        // Update slider details without changing the image
        $updateQuery = "UPDATE horizontal_ad SET ad_title = '$title', status= '$status' WHERE ad_id = $advtId";
        mysqli_query($db, $updateQuery);
    }
    
    $staus = 1;
    
    $re = base64_encode($staus);
    
    echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='manage-horizontal-advt.php?status=$re';</SCRIPT>");
}
?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Update Horizontal Advertisement</title>

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
    <?php include("header.php"); ?>
    <!-- /Header -->

    <!-- navbar -->
    <?php include("navbar.php"); ?>
    <!-- /navbar -->
    
    
    <section class="pcoded-main-container">
        <div class="pcoded-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Update Horizontal Advertisement</h5>
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
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                           <label for="advt_title" class="form-label">Horizontal Advt Title <span class="red-text">*</span></label>
                    					   <input type="text" name="advt_title" class="form-control" value="<?php echo $existingTitle ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                          <div class="form-group">
                                                <label for="status" class="form-label">Status <span class="red-text">*</span></label>
                                                	<select id="" name="status" class="form-control" required>
                                                		<option value="" disabled>Choose</option>
                                                    	<option value="1" <?php if($existingStatus == 1) echo "selected"; ?>>Enable</option>
                                                    	<option value="0" <?php if($existingStatus == 0) echo "selected"; ?>>Disable</option>
                                                </select>
                                          </div>
                                    </div>

                                     <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    									<div class="form-group">
        									<label for="uploaded_file" class="form-label">Horizontal Advt Image</label>
        										<div class="input-group">
            										<input type="file" name="uploaded_file" id="imageInput" class="form-control input-md mr-2" accept="image/*" onchange="showPreviewButton()">
            										<div class="input-group-append" id="previewButtonContainer" style="display: none;">
               	 										<button type="button" id="previewBtn" class="btn btn-secondary" onclick="showPreviewModal()">
                    											<i class="far fa-eye"></i> Preview *
                										</button>
            										</div>
        										</div>
       											<small class="text-muted">Leave it blank if you don't want to change the advt image.</small>
    										</div>
										</div>

                                         <!-- Modal Start -->
                                        <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
                                        	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            	<div class="modal-content">
                                            	<div class="modal-header">
                                                	<h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                                                	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                		<span aria-hidden="true">&times;</span>
                                                	</button>
                                            	</div>
                                            	<div class="modal-body">
                                                	<img id="previewImage" src="#" alt="Preview Image" style="max-width: 100%; height: auto;">
                                            	</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal end --> 
                                
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                         <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                             <i class="feather icon-save"></i>&nbsp; Update Horizontal Advt
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
    function showPreviewButton() {
        var input = document.getElementById('imageInput');
        var previewButtonContainer = document.getElementById('previewButtonContainer');

        if (input.files && input.files[0]) {
            previewButtonContainer.style.display = 'block';
        } else {
            previewButtonContainer.style.display = 'none';
        }
    }
	</script>
    
    <script>
    // Function to show the preview modal
    function showPreviewModal() {
        var modal = document.getElementById('imagePreviewModal');
        var modalImg = document.getElementById('previewImage');
        var files = document.getElementsByName('uploaded_file')[0].files;

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
