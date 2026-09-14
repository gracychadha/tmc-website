<?php
session_start();
error_reporting(0);
$upload_directory = "media/";

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

include("db/config.php");

$msg = "";

// Fetch existing media details
if (isset($_GET['id'])) {
    $encodedMediaId = $_GET['id'];
    $mediaId = base64_decode($encodedMediaId);
    
    $mediaQuery = "SELECT * FROM media WHERE media_id = '$mediaId'";
    $result = mysqli_query($db, $mediaQuery);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $mediaDetails = mysqli_fetch_assoc($result);
            $existingTitle = $mediaDetails['title'];
            $existingStatus = $mediaDetails['status'];
        } else {
            // Handle case where no media with the given ID is found
            die("Media not found with the given ID");
        }
    } else {
        // Handle error if query fails
        die("Error fetching media details: " . mysqli_error($db));
    }
}

if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $status = $_POST['status'];
    
    // Check if new images are selected
    if (!empty(array_filter($_FILES['uploaded_files']['name']))) {
        // Fetch existing image filenames
        $imageFilenames = [];
        $selectImageQuery = "SELECT image_filename FROM media_images WHERE media_id = '$mediaId'";
        $result = mysqli_query($db, $selectImageQuery);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $imageFilenames[] = $row['image_filename'];
            }
        }
        
        // Delete entries from the database
        $deleteQuery = "DELETE FROM media_images WHERE media_id = '$mediaId'";
        mysqli_query($db, $deleteQuery);
        
        // Unlink files from the server
        foreach ($imageFilenames as $filename) {
            unlink($upload_directory . $filename);
        }
        
        // Upload new images
        $uploaded_files = $_FILES['uploaded_files'];
        $file_count = count($uploaded_files['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            $temp_name = $uploaded_files['tmp_name'][$i];
            $original_name = $uploaded_files['name'][$i];
            $file_size = $uploaded_files['size'][$i];
            
            $allowed_types = ["image/jpeg", "image/png", "image/gif"];
            $file_type = mime_content_type($temp_name);
            
            if (!in_array($file_type, $allowed_types)) {
                $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                <strong><i class='feather icon-check'></i>Error !</strong> Please Upload Image Files.
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>
              </div>";
            } else {
                if ($file_size < 5 * 1024 * 1024) {
                    $unique_filename = uniqid() . '_' . $original_name;
                    move_uploaded_file($temp_name, $upload_directory . $unique_filename);
                    
                    // Insert new images into the database
                    $insertImageQuery = "INSERT INTO media_images (media_id, image_filename) VALUES ('$mediaId', '$unique_filename')";
                    mysqli_query($db, $insertImageQuery);
                } else {
                    $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                    <strong><i class='feather icon-check'></i>Error !</strong> File size exceeds the limit of 5MB.
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                      <span aria-hidden='true'>&times;</span>
                    </button>
                  </div>";
                }
            }
        }
    }
    
    // Update title and status
    $updateQuery = "UPDATE media m
                                   JOIN media_images mi ON m.media_id = mi.media_id
                                   SET m.title = '$title',
                                   m.status = '$status',
                                   mi.status = '$status'
                                   WHERE m.media_id = $mediaId;
                   ";
    mysqli_query($db, $updateQuery);
    
    // Redirect to manage-media.php after update
    $status = 1;
    $encodedStatus = base64_encode($status);
    echo ("<script>window.location.href='manage-media.php?status=$encodedStatus';</script>");
}

?>


<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Update Media</title>

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
                                <h5 class="m-b-10">Update Media
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
                                                <label for="title" class="form-label">Title <span class="red-text">*</span></label>
                                                <input type="text" name="title" class="form-control"
                                                    value="<?php echo isset($existingTitle) ? $existingTitle : ''; ?>"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="status" class="form-label">Status <span class="red-text">*</span></label>
                                                <select id="" name="status" class="form-control" required>
                                                    <option value="" selected disabled>Choose</option>
                                                    <option value="1"
                                                        <?php echo ($existingStatus == 1) ? 'selected' : ''; ?>>
                                                        Enable</option>
                                                    <option value="0"
                                                        <?php echo ($existingStatus != 1) ? 'selected' : ''; ?>>
                                                        Disable</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">Image 
        										<label class="sr-only control-label" for="name">Image<span class=" "></span></label>
        											<div class="input-group">
            											<input name="uploaded_files[]" type="file" class="form-control input-md mr-2" accept="image/*" multiple onchange="showPreviewButton()">
            											<div class="input-group-append">
                                                            <button type="button" id="previewBtn" class="btn btn-secondary" onclick="showPreviewModal()" style="display: none;">
    															<i class="far fa-eye"></i> Preview *
															</button>
            											</div>
        											</div>
        										<small class="text-muted">Leave it blank if you don't want to change the image.</small>
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
                                        
                                        <!-- Button -->
                                    	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        	<button type="submit" class="btn btn-secondary" name="update" id="update">
                                            	<i class="feather icon-save"></i>&nbsp; Update Media
                                        	</button>
                                    	</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="dt-responsive table-responsive"></div>
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
    <script>
        $(document).ready(function () {
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
        var files = document.getElementsByName('uploaded_files[]')[0].files;
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
