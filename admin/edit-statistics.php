<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["login_user"]))
{
    header("location: index.php");
}
include('db/config.php');
$msg="";

if (isset($_GET['id'])) {
    $encodedStatId = $_GET['id'];
    $statId = base64_decode($encodedStatId);
    
    $query = "SELECT * FROM statistics WHERE stat_id = $statId";
    $result = mysqli_query($db, $query);
    
    $row = mysqli_fetch_assoc($result);

    $Achievements = isset($row['our_achievements']) ? $row['our_achievements'] : '';
    $Performance = isset($row['performance_rating']) ? $row['performance_rating'] : '';
    $Clients = isset($row['our_clients']) ? $row['our_clients'] : '';
    $Projects = isset($row['our_projects']) ? $row['our_projects'] : '';
    $Experience = isset($row['our_experience']) ? $row['our_experience'] : '';
    $Engagements = isset($row['our_overseas_engagements']) ? $row['our_overseas_engagements'] : '';
    $existingStatus = isset($row['status']) ? $row['status'] : '';
}

if (isset($_POST['submit']))
{
    $NewAchievements = filter_input(INPUT_POST, 'achievements', FILTER_SANITIZE_STRING);
    $NewPerformance  = filter_input(INPUT_POST, 'performance', FILTER_SANITIZE_STRING);
    $NewClients = filter_input(INPUT_POST, 'clients', FILTER_SANITIZE_STRING);
    $NewProjects = filter_input(INPUT_POST, 'projects', FILTER_SANITIZE_STRING);
    $NewExperience = filter_input(INPUT_POST, 'experience', FILTER_SANITIZE_STRING);
    $NewEngagements = filter_input(INPUT_POST, 'engagements', FILTER_SANITIZE_STRING);
    $NewStatus = $_POST['statstatus'];
    
    $sql = "UPDATE statistics
            SET
            our_achievements = '$NewAchievements',
            performance_rating = '$NewPerformance',
            our_clients = '$NewClients',
            our_projects = '$NewProjects',
            our_experience = '$NewExperience',
            our_overseas_engagements = '$NewEngagements',
            status ='$NewStatus'
            WHERE stat_id = '$statId'";
    
    if ($db->query($sql) === TRUE)
    {
        $staus = 1;
        
        $re = base64_encode($staus);
        
        echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='manage-statistics.php?status=$re';</SCRIPT>");
    }
    else
    {
        echo "Error: " . $sql . "<br>" . $db->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <title>Update Statistics</title>

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
                                <h5 class="m-b-10">Update Statistics
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
                            <form class="contact-us" method="post" action="" enctype="multipart/form-data" autocomplete="off">
                                <div class=" ">
                                    <!-- Text input-->
                                      <div class="row">
                                           <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                              <div class="form-group">
                                                 <label for="achievements" class="form-label">Our Achievements</label>
                    						     <input type="text" name="achievements" class="form-control" value="<?php echo $Achievements; ?>" placeholder="Enter the number of achievements">
                                              </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                              <div class="form-group">
                                                 <label for="performance" class="form-label">Performance Rating</label>
                    						     <input type="text" name="performance" class="form-control" value="<?php echo $Performance; ?>" placeholder="Enter Performance rating">
                                              </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                              <div class="form-group">
                                                 <label for="clients" class="form-label">Number of Clients <span class="red-text">*</span></label>
                    						     <input type="text" name="clients" class="form-control" value="<?php echo $Clients; ?>" placeholder="Enter number of clients">
                                              </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                              <div class="form-group">
                                                 <label for="projects" class="form-label">Number of Projects <span class="red-text">*</span></label>
                    						     <input type="text" name="projects" class="form-control" value="<?php echo $Projects; ?>" placeholder="Enter number of projects">
                                              </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                              <div class="form-group">
                                                 <label for="experience" class="form-label">Our Field Experience</label>
                    						     <input type="text" name="experience" class="form-control" value="<?php echo $Experience; ?>" placeholder="Enter years of experience">
                                              </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                              <div class="form-group">
                                                 <label for="engagements" class="form-label"> Our Overseas Engagements</label>
                    						     <input type="text" name="engagements" class="form-control" value="<?php echo $Engagements; ?>" placeholder="Enter number of engagements">
                                              </div>
                                            </div>
                                             <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label for="statstatus" class="form-label">Status *</label>
                                                <select id="" name="statstatus" class="form-control" required>
                                                    <option value="" selected disabled>Choose</option>
                                                    <option value="1" <?php echo ($existingStatus == 1) ? 'selected' : ''; ?>>
                                                        Enable</option>
                                                    <option value="0" <?php echo ($existingStatus != 1) ? 'selected' : ''; ?>>
                                                        Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                            
                                        <!-- Button -->
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <button type="submit" class="btn btn-secondary" name="submit" id="submit">
                                                <i class="feather icon-save"></i>&nbsp; Update Statistics
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



</body>

</html>