<?php
session_start();
$upload_directory = "post/";
error_reporting(E_ALL); // Enable error reporting for debugging
$msg="";
if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
    exit; // Add exit after redirection to stop further execution
}

include("db/config.php");
$name = $_SESSION['login_user'];

if (isset($_POST['submit'])) {
    // Retrieve form data
    $title = $_POST['title'];
    $selected_categories = $_POST['parent_category'];
    $status = $_POST['status'];
    $content = $_POST['content'];
    $publish = $name;
    
    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s A");
    
    $temp_name = $_FILES["image"]["tmp_name"];
    $original_name = $_FILES["image"]["name"];
    $file_size = $_FILES["image"]["size"];
    
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
        if ($file_size < 2 * 1024 * 1024) {
            $unique_filename = uniqid() . '_' . $original_name;
            move_uploaded_file($temp_name, $upload_directory . $unique_filename);
            
            // Insert the post into the 'post' table
            $query = "INSERT INTO post (title, status, image, content, publish, date) VALUES ('$title', '$status', '$unique_filename', '$content', '$publish', '$date')";
            mysqli_query($db, $query);
            
            // Get the ID of the inserted post
            $post_id = mysqli_insert_id($db);
            
            // Insert categories into the 'post_categories' table
            foreach ($selected_categories as $category_id) {
                $category_query = "INSERT INTO post_categories (post_id, parent_category_id) VALUES ('$post_id', '$category_id')";
                mysqli_query($db, $category_query);
            }
            
            $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
            <strong><i class='feather icon-check'></i>Thanks!</strong> Post Added Successfully.
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
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
?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Add New Post</title>

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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <Style>
        .select2-container--default .select2-selection--multiple .select2-selection__rendered li {
            list-style: none;
            color: #003399;
            ;
            background: #fff;
        }
        
        .red-text {
            color: red;
        }     
    </Style>
     
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
                                <h5 class="m-b-10">Add New Post
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

                            <form class="contact-us" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" autocomplete="off">
                                <div class=" ">
                                    <!-- Text input-->
                                    <div class="row">
                                         <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                               <label for="title" class="form-label">Post Title <span class="red-text">*</span></label>
                    						   <input type="text" name="title" class="form-control" placeholder="Enter post's title" required>
                                            </div>
                                        </div>

                                       <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">
        										<label for="parent_category_name" class="form-label">Post Category <span class="red-text">*</span></label>
        											<?php
                                                        $category_query = "SELECT * FROM parent_category WHERE status='1'";
                                                        $result = $db->query($category_query);

                                                        if ($result->num_rows > 0) {
                                                            echo "<select name='parent_category[]' id='multiSelect' class='form-control select' multiple='multiple' required>";
                                                            echo "<option value='' disabled>choose . . .</option>";
                                                            
                                                                while ($row = $result->fetch_assoc()) {
                                                                    echo "<option value='{$row['parent_category_id']}'>{$row['parent_category_name']}</option>";
                                                                }
                                                                echo "</select>";
                                                        } else {
                                                                echo "No parent categories found.";
                                                        }
                                                     ?>
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
                                                               
                                         <!-- Text input-->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">Content <span class="red-text">*</span>
                                                <label class="sr-only control-label" for="content">Content<span class=" ">
                                                    </span></label>
                                                <textarea class="form-control" rows="2" cols="45"
                                                    name="content"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    										<div class="form-group">Image <span class="red-text">*</span>
        										<label class="sr-only control-label" for="name">Image<span class=" "></span></label>
        											<div class="input-group">
            											<input name="image" type="file" class="form-control input-md mr-2" required accept="image/*" onchange="showPreviewButton()">
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
                                            </div>
                                        </div>
                                        </div>
                                        <!-- Modal end -->
                                        
                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Add Post
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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#multiSelect').select2({
                width: '100%',
                closeOnSelect: false,
                templateSelection: function(data, container) {
                    // Add a cross icon to selected items    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

                    var $option = $(data.element);
                    var text = $option.text();
                    container.text(text);
                    container.append('<span class="remove-item" data-value="' + data.id + '">&times;</span>');
                }
            });

            // Handle removal of selected items
            $('#multiSelect').on('click', '.remove-item', function() {
                var valueToRemove = $(this).data('value');
                var $select = $('#multiSelect');
                $select.find('option[value="' + valueToRemove + '"]').prop('selected', false);
                $select.trigger('change.select2');
            });
        });
    </script>
    
    <script>
    $(document).ready(function() {
        $("#goldmessage").delay(5000).slideUp(300);
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
    
    <script>
    $(document).ready(function () {
        $('.select').multiselect({
            includeSelectAllOption: true // Optionally include a "Select All" option
            // Add any other options you need
        });
    });
	</script>
	
	<script>
    // Function to show the preview button when a file is selected
    function showPreviewButton() {
        var previewBtn = document.getElementById('previewBtn');
        previewBtn.style.display = 'block';
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