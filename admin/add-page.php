<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}
include('db/config.php');
$msg = "";

if (isset($_POST['submit'])) {
    $Title = $_POST['title'];
    $status = $_POST['status'];
    $content = $_POST['content'];
    $slug = $_POST['slug'];
    
    $Title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_STRING);
    
    date_default_timezone_set('Asia/Kolkata');
    
    // Get the current date in Asia/Kolkata timezone
    $createdDate = date('Y-m-d H:i:s A');
    
    $sql = "INSERT INTO page (title,status,content,slug_url,created_date) VALUES ('$Title','$status','$content','$slug','$createdDate')";
    if ($db->query($sql) === TRUE) {
        $msg = "
            <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
            <strong><i class='feather icon-check'></i>Thanks!</strong> page added successfully
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
            ";
    } else {
        echo "Error: " . $sql . "<br>" . $db->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Add New Page </title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.tiny.cloud/1/w9s9fz3wcjh5gsl1vp3uc6ka4gjtwty0jxcq12z65kb0svwi/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
   
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
                                <h5 class="m-b-10">Add New Page
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
                                               <label for="title" class="form-label">Page Title <span class="red-text">*</span></label>
                                               <input type="text" id="title" name="title" class="form-control" placeholder="Enter page title" required>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="slug" class="form-label">Slug URL <span class="red-text">*</span></label>
                                                <input type="text" id="slug" name="slug" class="form-control" placeholder="Enter slug URL" required>
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
                                            <div class="form-group">Content <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="content">Content<span class=" ">
                                                    </span></label>
                                                <textarea class="form-control" rows="2" cols="45"
                                                    name="content"></textarea>
                                            </div>
                                        </div>
                    
                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Add Page
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
        document.getElementById('title').addEventListener('input', function(event) {
            var title = event.target.value;
            var slug = title.trim().toLowerCase().replace(/\s+/g, '-'); // Replace spaces with dashes
            document.getElementById('slug').value = slug;
        });
    </script>
    
    <script>
    tinymce.init({
        selector: 'textarea',
        plugins: 'advlist autolink lists link image imagetools charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount hr nonbreaking toc textpattern emoticons contextmenu directionality emoticons importcss lists noneditable pagebreak save spellchecker tabfocus template tinydrive',
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor | removeformat | link unlink | image media | code | hr | table | emoticons | toc | contextmenu | directionality | nonbreaking | pagebreak | save | spellchecker | tabfocus | template | tinydrive',
        menubar: 'file edit view insert format tools table help',
        toolbar_mode: 'floating',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author Name',
        branding: false,
        contextmenu: "paste | link image inserttable | cell row column deletetable",
        importcss_append: true,
        templates: [
            { title: 'Test template 1', content: '<b>This is a test template.</b>' },
            { title: 'Test template 2', content: '<i>This is another test template.</i>' }
        ],
        tinydrive_token_provider: 'URL_TO_YOUR_TOKEN_PROVIDER',
        tinydrive_dropbox_app_key: 'YOUR_DROPBOX_APP_KEY'
    });
	</script>

</body>

</html>