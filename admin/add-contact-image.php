<?php
session_start();
$upload_directory = "contact/";
error_reporting(1);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
    exit;
}

include("db/config.php");

if (isset($_POST['submit'])) {
    $status = $_POST['status'];

    $temp_name = $_FILES["uploaded_file"]["tmp_name"];
    $original_name = $_FILES["uploaded_file"]["name"];
    $file_size = $_FILES["uploaded_file"]["size"];
    $upload_directory = "contact/";

    $allowed_types = ["image/jpeg", "image/png", "image/gif"];
    $file_type = mime_content_type($temp_name);

    if (!in_array($file_type, $allowed_types)) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                <strong><i class='feather icon-check'></i>Error!</strong> Please Upload Image File.
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                  <span aria-hidden='true'>×</span>
                </button>
              </div>";
    } else {
        if ($file_size < 2 * 1024 * 1024) {
            $unique_filename = uniqid() . '_' . basename($original_name);
            $target_path = $upload_directory . $unique_filename;

            if (move_uploaded_file($temp_name, $target_path)) {
                // Use prepared statement
                $query = "INSERT INTO contactimg (img_url, status) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                if ($stmt === false) {
                    die("Prepare failed: " . $db->error);
                }

                // Bind parameters: 'ssssss' indicates all parameters are strings
                $stmt->bind_param("ss", $unique_filename,$status);

                if ($stmt->execute()) {
                    $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                            <strong><i class='feather icon-check'></i>Thanks!</strong> Contact Image Added Successfully
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>×</span>
                            </button>
                          </div>";
                } else {
                    die("Execute failed: " . $stmt->error);
                }
                $stmt->close();
            } else {
                $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                        <strong><i class='feather icon-check'></i>Error!</strong> Failed to move uploaded file.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                          <span aria-hidden='true'>×</span>
                        </button>
                      </div>";
            }
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                    <strong><i class='feather icon-check'></i>Error!</strong> File size exceeds the limit of 2MB.
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                      <span aria-hidden='true'>×</span>
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
    <title>Add Contact Image</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/css/style.css">

    <!-- cropper js -->
    <script src="https://unpkg.com/cropperjs"></script>

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
                                <h5 class="m-b-10">Add Contact Image
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
                                        <!-- <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Title <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">Show On Top Menu<span
                                                        class=" ">
                                                    </span></label>
                                               <input name="title" type="text" class="form-control input-md"
                                                    placeholder="Enter the title "required >
                                            </div>
                                        </div> -->

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Image <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="name">Image<span
                                                        class=" "></span></label>
                                                <div class="input-group">
                                                    <input name="uploaded_file" type="file"
                                                        class="form-control input-md mr-2" required accept="image/*"
                                                        onchange="showPreviewButton()">
                                                    <div class="input-group-append">
                                                        <button type="button" id="previewBtn" class="btn btn-secondary"
                                                            onclick="showPreviewModal()" style="display: none;">
                                                            <i class="far fa-eye"></i> Preview
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="text-muted"><span style="color: red;">*Upload supported
                                                        file (Max 2MB & 300px*300px )</span></small>
                                            </div>
                                        </div>

                                        <!-- Modal Start -->
                                        <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog"
                                            aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="imagePreviewModalLabel">Image
                                                            Preview</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img id="previewImage" src="#" alt="Preview Image"
                                                            style="max-width: 100%; height: auto;">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal End -->

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
                                                <i class="feather icon-save"></i>&nbsp; Add Contact Image
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
    function showPreviewButton() {
        var previewBtn = document.getElementById('previewBtn');
        previewBtn.style.display = 'block';
    }

    // Function to show the image preview in the modal
    function showPreviewModal() {
        var modal = document.getElementById('imagePreviewModal');
        var modalImg = document.getElementById('previewImage');
        var fileInput = document.getElementsByName('uploaded_file')[0];
        var file = fileInput.files[0];

        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                modalImg.src = e.target.result;
                $(modal).modal('show'); // Show the modal using Bootstrap's jQuery method
            };
            reader.readAsDataURL(file);
        } else {
            alert('Please select an image first!');
        }
    }
    </script>
    <script>
    var $image = $('#previewImage');

    $image.cropper({
        aspectRatio: 16 / 9,
        crop: function(event) {
            console.log(event.detail.x);
            console.log(event.detail.y);
            console.log(event.detail.width);
            console.log(event.detail.height);
            console.log(event.detail.rotate);
            console.log(event.detail.scaleX);
            console.log(event.detail.scaleY);
        }
    });

    // Get the Cropper.js instance after initialized
    var cropper = $image.data('cropper');
    </script>
</body>

</html>