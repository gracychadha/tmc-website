<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

$name = $_SESSION['login_user'];
include("db/config.php");

if (isset($_GET['id'])) {
    $id = base64_decode($_GET['id']);

    $query = "SELECT * FROM videos WHERE video_id='$id'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        header("location: all-videos.php");
    }
}

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Handle image upload
    if ($_FILES['image']['name'] != "") {
        $image_temp_name = $_FILES["image"]["tmp_name"];
        $image_original_name = $_FILES["image"]["name"];
        $image_file_size = $_FILES["image"]["size"];
        $image_file_type = mime_content_type($image_temp_name);

        $allowed_image_types = ["image/jpeg", "image/png", "image/gif"];
        if (in_array($image_file_type, $allowed_image_types) && $image_file_size < 5 * 1024 * 1024) {
            $unique_image_filename = uniqid() . '_' . $image_original_name;
            move_uploaded_file($image_temp_name, "videos/thumbnail/" . $unique_image_filename);
            $thumbnail_url = $unique_image_filename;
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
            <strong><i class='feather icon-check'></i>Error!</strong> Please upload a valid image file (Max 5MB).
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
            $thumbnail_url = $row['thumbnail_url'];
        }
    } else {
        $thumbnail_url = $row['thumbnail_url'];
    }

    // Handle video upload
    if ($_FILES['file']['name'] != "") {
        $video_temp_name = $_FILES["file"]["tmp_name"];
        $video_original_name = $_FILES["file"]["name"];
        $video_file_size = $_FILES["file"]["size"];
        $video_file_type = mime_content_type($video_temp_name);

        $allowed_video_types = ["video/mp4", "video/avi", "video/webm", "video/ogg", "video/ogv", "video/x-msvideo", "video/quicktime", "video/mpeg"];
        if (in_array($video_file_type, $allowed_video_types) && $video_file_size < 100 * 1024 * 1024) {
            $unique_video_filename = uniqid() . '_' . $video_original_name;
            move_uploaded_file($video_temp_name, "videos/videos/" . $unique_video_filename);
            $video_filename = $unique_video_filename;
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
            <strong><i class='feather icon-check'></i>Error!</strong> Please upload a valid video file (Max 100MB).
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
            $video_filename = $row['video_filename'];
        }
    } else {
        $video_filename = $row['video_filename'];
    }

    $category_name = mysqli_real_escape_string($db, $category);

    // Retrieve the category_id based on the provided category_name
    $category_query = "SELECT category_id FROM category WHERE category_name = '$category_name'";
    $result = mysqli_query($db, $category_query);

    if ($category_row = mysqli_fetch_assoc($result)) {
        $category_id = $category_row['category_id'];

        // Update data in the database
        $update_query = "UPDATE videos SET
                        category_id='$category_id',
                        category_name='$category_name',
                        video_title='$title',
                        video_description='$description',
                        video_filename='$video_filename',
                        thumbnail_url='$thumbnail_url',
                        status='$status'
                        WHERE video_id='$id'";
        if ($db->query($update_query) === TRUE)
        {
            $staus = 1;
            
            $re = base64_encode($staus);
            
            echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='manage-videos.php?status=$re';</SCRIPT>");
        } 
    } else {
        // Handle the case where the category_name doesn't exist in the categories table
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Error!</strong> Category not found.
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
    <title>Update Video</title>
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
                                <h5 class="m-b-10">Update Video
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
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class=" ">
                                    <!-- Text input-->
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="title" class="form-label">Video Title <span class="red-text">*</span></label>
                                                <input type="text" name="title" class="form-control"
                                                    placeholder="Enter Video title" value="<?php echo $row['video_title']; ?>"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="category_name" class="form-label">Course Name <span class="red-text">*</span></label>
                                                <?php
                                                $category_query = "SELECT * FROM category WHERE status='1'";
                                                $category_result = $db->query($category_query);

                                                if ($category_result->num_rows > 0) {
                                                    echo "<select name='category' class='form-control select'>";
                                                    echo "<option value='' selected disabled>Choose</option>";

                                                    while ($category_row = $category_result->fetch_assoc()) {
                                                        $selected = ($category_row['category_name'] == $row['category_name']) ? 'selected' : '';
                                                        echo "<option value='{$category_row['category_name']}' $selected>{$category_row['category_name']}</option>";
                                                    }

                                                    echo "</select>";
                                                } else {
                                                    echo "No categories found.";
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="video" class="form-label">Select Video</label>
                                                <input type="file" name="file" class="form-control">
                                                <small class="text-muted">Leave it blank if you don't want to change
                                                    the video.</small>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">
        										<label for="name" class="form-label">Video Thumbnail</label>
        										<div class="input-group">
            										<input type="file" name="image" id="imageInput" class="form-control input-md mr-2" accept="image/*" onchange="showPreviewButton()">
            										<div class="input-group-append" id="previewButtonContainer" style="display: none;">
               	 										<button type="button" id="previewBtn" class="btn btn-secondary" onclick="showPreviewModal()">
                    											<i class="far fa-eye"></i> Preview *
                										</button>
            										</div>
        										</div>
       											<small class="text-muted">Leave it blank if you don't want to change the thumbnail.</small>
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

                                        
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="status" class="form-label">Status <span class="red-text">*</span></label>
                                                <select id="" name="status" class="form-control" required>
                                                    <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>
                                                        Enable</option>
                                                    <option value="0" <?php echo ($row['status'] == 0) ? 'selected' : ''; ?>>
                                                        Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Video Description</label>
                                                <textarea class="form-control" rows="5" cols="45" name="description"
                                                 placeholder="Enter the Video Description"><?php echo $row['video_description']; ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Update Video
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
        var files = document.getElementsByName('image')[0].files;

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
