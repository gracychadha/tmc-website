<?php
session_start();
$upload_directory = "media/";
error_reporting(0);
if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

include("db/config.php");

// Register user
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $status = $_POST['status'];
    
    // Insert into media table
    $media_query = "INSERT INTO media (title,status) VALUES ('$title','$status')";
    mysqli_query($db, $media_query);
    $media_id = mysqli_insert_id($db); // Get the ID of the newly inserted media
    
    // Count total files
    $countfiles = count($_FILES['uploaded_file']['name']);
    
    // Looping all files
    for ($i = 0; $i < $countfiles; $i++) {
        $temp_name = $_FILES["uploaded_file"]["tmp_name"][$i];
        $original_name = $_FILES["uploaded_file"]["name"][$i];
        $file_size = $_FILES["uploaded_file"]["size"][$i];
        
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
            if ($file_size < 5 * 1024 * 1024) {
                $unique_filename = uniqid() . '_' . $original_name;
                move_uploaded_file($temp_name, $upload_directory . $unique_filename);
                
                // Insert into media_images table
                $image_query = "INSERT INTO media_images (media_id, image_filename) VALUES ('$media_id', '$unique_filename')";
                mysqli_query($db, $image_query);
                
                $msg = "
                <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                <strong><i class='feather icon-check'></i>Thanks!</strong> Add Media record Successfully
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
}

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <title>Add Media</title>

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
                                <h5 class="m-b-10">Add Media
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
                                            <div class="form-group">Title <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">Show On Top Menu<span
                                                        class=" ">
                                                    </span></label>
                                               <input name="title" type="text" class="form-control input-md"
                                                    placeholder="Enter the title "required >
                                            </div>
                                        </div>
  
									    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">Image <span class="red-text">*</span>
        										<label class="sr-only control-label" for="name">Image<span class=" "></span></label>
        											<div class="input-group">
            											<input name="uploaded_file[]" type="file" class="form-control input-md mr-2" required accept="image/*" multiple onchange="showPreviewButton()">
            											<div class="input-group-append">
                                                            <button type="button" id="previewBtn" class="btn btn-secondary" onclick="showPreviewModal()" style="display: none;">
    															<i class="far fa-eye"></i> Preview *
															</button>
            											</div>
        											</div>
        										<small class="text-muted"><span style="color: red;">*Upload supported file(Max 2MB)</span></small>
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
            										<div class="modal-footer">
                                                        <!-- Button to go to previous image -->
                										<button type="button" id="prevBtn" class="btn btn-secondary mr-2"><i class="fas fa-chevron-left"></i></button>

                                                        <!-- Button to go to next image -->
                										<button type="button" id="nextBtn" class="btn btn-secondary"><i class="fas fa-chevron-right"></i></button>
            										</div>
        										</div>
    										</div>
										</div>
                                        <!-- Modal end -->
                                        
									   
									    <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12 col-12">
                                            <div class="form-group">Status <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">Status<span class=" ">
                                                    </span></label>
                                                <select id="" name="status" class="form-control"
                                                    oninvalid="this.setCustomValidity('Please Select Status')"
                                                    oninput="setCustomValidity('')" required>
                                                    <option value="">Choose</option>
                                                    <option value="1">Enable</option>
                                                    <option value="0">Disable</option>

                                                </select>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Add Media
                                            </button>
                                        </div>
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
    // Function to show the preview button when a file is selected
    function showPreviewButton() 
    {
        var previewBtn = document.getElementById('previewBtn');
        previewBtn.style.display = 'block';
    }
	</script>
    
    <script>
    function showPreviewModal() {
        var modal = document.getElementById('imagePreviewModal');
        var modalImg = document.getElementById('previewImage');
        var files = document.getElementsByName('uploaded_file[]')[0].files;
        var currentIndex = 0;

        // Check if any file is selected
        if (files.length > 0) {
            // Display the first image initially
            displayImage(files[currentIndex]);

            // Show the modal
            $(modal).modal('show');

            // Add event listeners for navigation buttons
            document.getElementById('prevBtn').addEventListener('click', function() {
                currentIndex = (currentIndex - 1 + files.length) % files.length;
                displayImage(files[currentIndex]);
            });

            document.getElementById('nextBtn').addEventListener('click', function() {
                currentIndex = (currentIndex + 1) % files.length;
                displayImage(files[currentIndex]);
            });
        }

        // Function to display the image
        function displayImage(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                modalImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
	</script>
</body>
</html>
