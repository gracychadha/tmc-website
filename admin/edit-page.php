<?php
session_start();
include('db/config.php');
$msg = "";

if (isset($_GET['id'])) {
    $encodedpageId = $_GET['id'];
    $pageId = base64_decode($encodedpageId);
    
    $query = "SELECT * FROM page WHERE page_id = $pageId";
    $result = mysqli_query($db, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Fetch category details from $row
        $pagetitle = $row['title'];
        $pageStatus = $row['status'];
        $pagecontent = $row['content'];
        
        // Check if the form is submitted for updating
        if (isset($_POST['submit'])) {
            // Get updated values from the form
            $updatedpagetitle = mysqli_real_escape_string($db, $_POST['title']);
            $updatedpageStatus = mysqli_real_escape_string($db, $_POST['status']);
            $updatedpagecontent = mysqli_real_escape_string($db, $_POST['content']);
            
            // Generate slug URL based on the updated page title
            $slug = slugify($updatedpagetitle);
            
            // Update the category in the database
            $updateQuery = "UPDATE page SET title = '$updatedpagetitle', status='$updatedpageStatus', content = '$updatedpagecontent', slug_url = '$slug' WHERE page_id = $pageId";
            if ($db->query($updateQuery) === TRUE) {
                $status = 1;
                $re = base64_encode($status);
                echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='manage-page.php?status=$re';</SCRIPT>");
            }
        }
    } else {
        echo "Page not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}

// Function to generate slug URL
function slugify($text)
{
    // Replace non-letter or non-digit characters with -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    // Trim trailing - characters
    $text = trim($text, '-');
    // Convert to lowercase
    $text = strtolower($text);
    // If empty, return 'n-a'
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Page</title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.tiny.cloud/1/w9s9fz3wcjh5gsl1vp3uc6ka4gjtwty0jxcq12z65kb0svwi/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
     
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
                                <h5 class="m-b-10">Update Page</h5>
                            </div>
<!--                             <ul class="breadcrumb"> -->
<!--                                 <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li> -->
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
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data" autocomplete="off">
                                <div class=" ">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">
        										<label for="title" class="form-label">Page Title <span class="red-text">*</span></label>
        										<input type="text" name="title" class="form-control" id="title" value="<?php echo $pagetitle; ?>" placeholder="Enter page title" required>
    										</div>
										</div>
										<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">
        										<label for="slug" class="form-label">Slug URL <span class="red-text">*</span></label>
        										<input type="text" name="slug" class="form-control" id="slug" value="<?php echo slugify($pagetitle); ?>" placeholder="Slug URL" required>
    										</div>
										</div>
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="status" class="form-label">Status <span class="red-text">*</span></label>
                                                <select id="" name="status" class="form-control" required>
                                                    <option value="" selected disabled>Choose</option>
                                                    <option value="1" <?php echo ($pageStatus == 1) ? 'selected' : ''; ?>>Enable</option>
                                                    <option value="0" <?php echo ($pageStatus != 1) ? 'selected' : ''; ?>>Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Content <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="content">Content<span class=" ">
                                                    </span></label>
                                                <textarea class="form-control" rows="5" cols="45" name="content"><?php echo $pagecontent; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Update Page
                                           </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="dt-responsive table-responsive">
                                <!-- Your table content goes here -->
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
        $(document).ready(function() {
            $("#goldmessage").delay(5000).slideUp(300);
        });
    </script>
    
    <script>
    // Function to generate slug URL
    function slugify(text) {
        text = text.toLowerCase().trim().replace(/\s+/g, '-'); // Replace spaces with -
        return text;
    }

    // Update slug URL when the page title changes
    document.getElementById('title').addEventListener('input', function() {
        var title = this.value;
        var slug = slugify(title);
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
            templates: [{
                    title: 'Test template 1',
                    content: '<b>This is a test template.</b>'
                },
                {
                    title: 'Test template 2',
                    content: '<i>This is another test template.</i>'
                }
            ],
            tinydrive_token_provider: 'URL_TO_YOUR_TOKEN_PROVIDER',
            tinydrive_dropbox_app_key: 'YOUR_DROPBOX_APP_KEY'
        });
    </script>
</body>
</html>
