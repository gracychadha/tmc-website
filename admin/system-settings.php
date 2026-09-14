<?php
session_start();
include("db/config.php");

$msg="";

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

$result = mysqli_query($db, "SELECT * FROM google_captcha");
$row = mysqli_fetch_assoc($result);

$sitekey = isset($row['site_key']) ? $row['site_key'] : '';
$secretKey = isset($row['secret_key']) ? $row['secret_key'] : '';
$g = isset($row['captcha_id']) ? $row['captcha_id'] : '';

$result1 = mysqli_query($db, "SELECT * FROM smtp_email");
$row1 = mysqli_fetch_assoc($result1);

$Address_smtp = isset($row1['from_email']) ? $row1['from_email'] : '';
$password_smtp = isset($row1['password']) ? $row1['password'] : '';
$host_smtp = isset($row1['host']) ? $row1['host'] : '';
$port_smtp = isset($row1['port']) ? $row1['port'] : '';
$s = isset($row1['smtp_id']) ? $row1['smtp_id'] : '';

$result2 = mysqli_query($db, "SELECT * FROM login_settings");
$row2 = mysqli_fetch_assoc($result2);

$Number = isset($row2['helpdesk_no']) ? $row2['helpdesk_no'] : '';
$l = isset($row2['id']) ? $row2['id'] : '';

$result3 = mysqli_query($db, "SELECT * FROM map");
$row3 = mysqli_fetch_assoc($result3);

$MapKey = isset($row3['map_api_key']) ? $row3['map_api_key'] : '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit1'])) {
        // Handle submit1 button
        if (isset($_POST['key'])) {
            $key = $_POST['key'];
            $secret_key = $_POST['secret'];
            
            $sql = "INSERT INTO google_captcha (captcha_id, site_key, secret_key)
                    VALUES ('$g', '$key', '$secret_key')
                    ON DUPLICATE KEY UPDATE site_key='$key', secret_key='$secret_key'";
            
            if ($db->query($sql) === TRUE) {
                $msg = "
                    <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                    <strong><i class='feather icon-check'></i>Success!</strong> The Google reCAPTCHA setting has been updated.
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                    </button>
                    </div>
                ";
            } else {
                echo "Error: " . $sql . "<br>" . $db->error;
            }
        }
    } elseif (isset($_POST['submit2'])) {
        // Handle submit2 button
        $email = $_POST['from_email'];
        $password = $_POST['password'];
        $host = isset($_POST['host']) ? $_POST['host'] : ''; 
        $port = isset($_POST['port']) ? $_POST['port'] : ''; 
        
        $sql = "INSERT INTO smtp_email (smtp_id, from_email, password, host, port)
                VALUES ('$s', '$email', '$password', '$host', '$port')
                ON DUPLICATE KEY UPDATE from_email='$email', password='$password', host='$host', port='$port'";
        
        if ($db->query($sql) === TRUE) {
            $msg = "
                <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                <strong><i class='feather icon-check'></i>Success!</strong> The SMTP setting has been updated.
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
                </button>
                </div>
            ";
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }
    }elseif (isset($_POST['submit3'])) {
        // Handle submit3 button
        $helpdeskNumber = $_POST["helpdesk_number"];
        
        // Define directory paths for logo, favicon, and landing page logo
        $backendPanelLogoDirectory = "logo/backend_panel_logo/";
        $faviconDirectory = "logo/favicon/";
        $landingPageLogoDirectory = "logo/landing_page_logo/";
        
        // Process backend panel logo
        if ($_FILES["logo"]["name"]) {
            $logoPath = $_FILES["logo"]["name"];
            $targetPath = $backendPanelLogoDirectory . $logoPath;
            move_uploaded_file($_FILES["logo"]["tmp_name"], $targetPath);
            // Concatenate the directory with the file name
            $backendPanelLogoPath = $backendPanelLogoDirectory . $logoPath;
            
            // Delete the previous backend panel logo file from the folder if it exists
            if (!empty($row2['backend_panel_logo'])) {
                unlink($row2['backend_panel_logo']);
            }
        } else {
            // If no new logo is uploaded, keep the existing path
            $backendPanelLogoPath = $row2['backend_panel_logo'];
        }
        
        // Process favicon
        if ($_FILES["favicon"]["name"]) {
            $faviconPath = $_FILES["favicon"]["name"];
            $targetPath = $faviconDirectory . $faviconPath;
            move_uploaded_file($_FILES["favicon"]["tmp_name"], $targetPath);
            // Concatenate the directory with the file name
            $faviconFilePath = $faviconDirectory . $faviconPath;
            
            // Delete the previous favicon file from the folder if it exists
            if (!empty($row2['favicon'])) {
                unlink($row2['favicon']);
            }
        } else {
            // If no new favicon is uploaded, keep the existing path
            $faviconFilePath = $row2['favicon'];
        }
        
        // Process landing page logo
        if ($_FILES["logo1"]["name"]) {
            $landingPageLogoPath = $_FILES["logo1"]["name"];
            $targetPath = $landingPageLogoDirectory . $landingPageLogoPath;
            move_uploaded_file($_FILES["logo1"]["tmp_name"], $targetPath);
            // Concatenate the directory with the file name
            $landingPageLogoFilePath = $landingPageLogoDirectory . $landingPageLogoPath;
            
            // Delete the previous landing page logo file from the folder if it exists
            if (!empty($row2['landing_page_logo'])) {
                unlink($row2['landing_page_logo']);
            }
        } else {
            // If no new landing page logo is uploaded, keep the existing path
            $landingPageLogoFilePath = $row2['landing_page_logo'];
        }
        
        // Update or insert helpdesk number, logo, favicon, and landing page logo paths into the database
        $sql = "INSERT INTO login_settings (id, backend_panel_logo, favicon, landing_page_logo, helpdesk_no)
        VALUES ('$l', '$backendPanelLogoPath', '$faviconFilePath', '$landingPageLogoFilePath', '$helpdeskNumber')
        ON DUPLICATE KEY UPDATE backend_panel_logo='$backendPanelLogoPath', favicon='$faviconFilePath', landing_page_logo='$landingPageLogoFilePath', helpdesk_no='$helpdeskNumber'";
        
        if ($db->query($sql) === TRUE) {
            $msg = "
        <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
        <strong><i class='feather icon-check'></i>Success!</strong> The Logo and Helpdesk Settings have been updated.
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
        <span aria-hidden='true'>&times;</span>
        </button>
        </div>
    ";
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }
    }
    elseif (isset($_POST['submit4'])) {
        // Handle submit4 button
            $map_key = $_POST['map_key'];
            
            $sql = "INSERT INTO map (map_api_key)
                    VALUES ('$map_key')
                    ON DUPLICATE KEY UPDATE map_api_key='$map_key'";
            
            if ($db->query($sql) === TRUE) {
                $msg = "
                    <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                    <strong><i class='feather icon-check'></i>Success!</strong> The Google Maps Api key has been updated.
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                    </button>
                    </div>
                ";
            } else {
                echo "Error: " . $sql . "<br>" . $db->error;
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>System Settings </title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Codedthemes" />

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="">

    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <?php include 'header.php'; ?>
    <?php include 'navbar.php'; ?>

    <section class="pcoded-main-container">
        <div class="pcoded-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">System Settings
                                </h5>
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
                            if ($msg) {
                                echo $msg;
                            }
                            ?>
                            <br />
                            <h6><span data-feather="airplay"></span> DISPLAY GOOGLE RECAPTCHA</h6>
                            <hr />
                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Google Rechaptcha Site Key *</label>
										    <input type="text" name="key" class="form-control" placeholder="Enter the site key" value="<?php echo $sitekey; ?>" required>
                                        </div>
                                    </div>


                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Google Rechaptcha Secret Key *</label>
											<input type="text" name="secret" class="form-control" placeholder="Enter the secret key" value="<?php echo $secretKey; ?>" required>
                                        </div>
                                    </div>
                                
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <button type="submit" class="btn btn-secondary" name="submit1" id="submit">
                                            <i class="feather icon-save lg"></i>&nbsp; Save
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header table-card-header">
                            <h6><span data-feather="inbox"></span> SMTP</h6>
                            <hr/>
                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Email From Address *</label>
										    <input type="text" name="from_email" class="form-control" placeholder="Enter the email from address" value="<?php echo $Address_smtp; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Email Password *</label>
										    <input type="text" name="password" class="form-control" placeholder="Enter the email password" value="<?php echo $password_smtp; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Email Host *</label>
											<input type="text" name="host" class="form-control" placeholder="Enter the email host" value="<?php echo $host_smtp; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Email Port *</label>
											<input type="text" name="port" class="form-control" placeholder="Enter the email port" value="<?php echo $port_smtp; ?>" required>
                                        </div>
                                    </div>
                                    
                                   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <button type="submit" class="btn btn-secondary" name="submit2" id="submit">
                                            <i class="feather icon-save lg"></i>&nbsp; Save
                                        </button>
                                   </div>
                                 </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
    		<div class="col-sm-12">
        		<div class="card">
            		<div class="card-header table-card-header">
                		<h6><span data-feather="image"></span> LOGO AND HELPDESK</h6>
             			<hr />
                			<form method="post" action="" enctype="multipart/form-data">
                   				 <div class="row">
                    				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                        				<div class="form-group">
                            				<label class="form-label">Backend Panel Logo</label>
                            				<input type="file" class="form-control" name="logo">
                                            <small class="text-muted">Leave it blank if you don't want to change the image.</small>                      
                        				</div>
                        				
                        				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    									<div class="form-group">
        									<label class="form-label">*Recent Backend Panel Logo</label>
        										<?php if (!empty($row2['backend_panel_logo'])): ?>
            										<img src="<?php echo $row2['backend_panel_logo']; ?>" alt="Current Logo" style="max-width: 100px; max-height: 100px;">
        												<?php else: ?>
            												<span>No recent logo available</span>
       													<?php endif; ?>
    									</div>
									</div>
									<br/>
									<br/>
                        				
                    				</div>
                    				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                        				<div class="form-group">
                            				<label class="form-label">Helpdesk Number *</label>
                            				<input type="text" class="form-control" name="helpdesk_number" placeholder="Enter the helpdesk Number" value="<?php echo $Number;?>" required>
                        				</div>
                        				
                    				</div>
                    				
                    				
									
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                        				<div class="form-group">
                            				<label class="form-label">Favicon</label>
                            				<input type="file" class="form-control" name="favicon">
                                            <small class="text-muted">Leave it blank if you don't want to change the favicon.</small>                      
                        				</div>
                        				
                        				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
    									<div class="form-group">
        									<label class="form-label">*Recent Favicon</label>
        										<?php if (!empty($row2['favicon'])): ?>
            										<img src="<?php echo $row2['favicon']; ?>" alt="Current Logo" style="max-width: 50px; max-height: 50px;">
        												<?php else: ?>
            												<span>No recent logo available</span>
       													<?php endif; ?>
    									</div>
									</div>
                        				
                    				</div>
                    				
                    				
                    				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                        				<div class="form-group">
                            				<label class="form-label">Landing Page Logo</label>
                            				<input type="file" class="form-control" name="logo1">
                                            <small class="text-muted">Leave it blank if you don't want to change the image.</small>                      
                        				</div>
                        				
                        				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    									<div class="form-group">
        									<label class="form-label">*Recent Landing Page Logo</label>
        										<?php if (!empty($row2['landing_page_logo'])): ?>
            										<img src="<?php echo $row2['landing_page_logo']; ?>" alt="Current Logo" style="max-width: 100px; max-height: 100px;">
        												<?php else: ?>
            												<span>No recent logo available</span>
       													<?php endif; ?>
    									</div>
									</div>
                        				
                    				</div>
                    				
                    				
                    				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        					<button type="submit" class="btn btn-secondary" name="submit3" id="submit">
                           						<i class="feather icon-save lg"></i>&nbsp; Save
                        					</button>
                    				</div>
                    			</div>	
               				</form>
            		</div>
        		</div>
    		</div>
		</div>
		
		<div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header table-card-header">
                            <h6><span data-feather="map"></span> GOOGLE MAP'S</h6>
                            <hr/>
                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Google Map Api Key *</label>
										    <input type="text" name="map_key" class="form-control" placeholder="Enter the api key" value="<?php echo $MapKey; ?>" required>
                                        </div>
                                    </div>
                                   
                                   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <button type="submit" class="btn btn-secondary" name="submit4" id="submit">
                                            <i class="feather icon-save lg"></i>&nbsp; Save
                                        </button>
                                   </div>
                                 </div>
                            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
       feather.replace(); // Initialize Feather Icons
    </script>
    <script>
       $(document).ready(function() {
          $("#goldmessage").delay(5000).slideUp(300);
    });
    </script>
</body>
</html>