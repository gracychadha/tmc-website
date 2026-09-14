<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}
include('db/config.php');
$upload_directory = "study_material/";
$msg = "";

if (isset($_POST['submit'])) {
    $material_name = $_POST['material_name'];
    $selected_categories = $_POST['category_name'];
    $status = $_POST['status'];
    
    $temp_name = $_FILES["material"]["tmp_name"];
    $original_name = $_FILES["material"]["name"];
    $file_size = $_FILES["material"]["size"];
    
    $allowed_types = ["application/pdf"];
    $file_type = mime_content_type($temp_name);
    
    date_default_timezone_set('Asia/Kolkata');
    
    // Get the current date in Asia/Kolkata timezone
    $createdDate = date('Y-m-d H:i:s A');
    
    if (!in_array($file_type, $allowed_types)) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Error !</strong> Please Upload Pdf File.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>";
    } else {
        if ($file_size < 100 * 1024 * 1024) {
            $unique_filename = uniqid() . '_' . $original_name;
            move_uploaded_file($temp_name, $upload_directory . $unique_filename);
            
            $query = "INSERT INTO study_material (study_material_name, study_material, status, created_date) VALUES ('$material_name', '$unique_filename', '$status', '$createdDate')";
            mysqli_query($db, $query);
            
            $study_material_id = mysqli_insert_id($db);
            
            foreach ($selected_categories as $selected_category) {
                $category_query = "SELECT category_id FROM category WHERE category_name = '$selected_category'";
                $result = mysqli_query($db, $category_query);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $category_id = $row['category_id'];
                    $insert_query = "INSERT INTO study_material_category (study_material_id, category_id) VALUES ('$study_material_id', '$category_id')";
                    mysqli_query($db, $insert_query);
                }
            }
            
            $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                        <strong><i class='feather icon-check'></i>Thanks!</strong> Study Material added successfully.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                        </button>
                        </div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                        <strong><i class='feather icon-check'></i>Error !</strong> File size exceeds the limit of 100MB.
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
    <title>Add Study Material</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
    
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
                                <h5 class="m-b-10">Add Study Material
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
                                               <label for="material_name" class="form-label">Study Material Name <span class="red-text">*</span></label>
                    						   <input type="text" name="material_name" class="form-control" placeholder="Enter study material name" required>
                                            </div>
                                        </div>


                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                           <div class="form-group">
                                               <label for="category_name" class="form-label">Category Name <span class="red-text">*</span></label>
                    								<?php
                                                        $category_query = "SELECT * FROM category WHERE status='1'";
                                                        $result = $db->query($category_query);
                                                        
                                                        if ($result->num_rows > 0) {
                                                            echo "<select name='category_name[]' id='multiSelect' class='form-control select' multiple='multiple' required>";
                                                            echo "<option value='' disabled>Choose</option>";

                                                            while ($row = $result->fetch_assoc()) {
                                                                 echo "<option value='{$row['category_name']}'>{$row['category_name']}</option>";
                                                            }
                                        
                                                             echo "</select>";
                                                         } 
                                                         else 
                                                         {
                                                              echo "No categories found.";
                                                          }
                                                     ?>
                                            	</div>
                                            </div>
                                            
                                           <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
    											<div class="form-group">
        											<label for="material" class="form-label">Study Material <span class="red-text">*</span></label>
        											<input type="file" name="material" class="form-control input-md" required accept="application/pdf">
        											<small class="text-muted"><span style="color: red;">*Upload supported file (Max 100MB)</span></small>                      
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

                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Add Study Material
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
    $(document).ready(function () {
        $('.select').multiselect({
            includeSelectAllOption: true // Optionally include a "Select All" option
            // Add any other options you need
        });
    });
	</script>

</body>

</html>