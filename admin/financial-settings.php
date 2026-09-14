<?php
session_start();
include("db/config.php");

$msg = "";

if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Retrieve form data
    $newPaymentMethod = $_POST['new_payment_method'];
    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $email = $_POST['email'];
    $apiKey = $_POST['api_key'];
    $secretKey = $_POST['secret_key'];
    
    // Validate if required fields are not empty
    if (!empty($newPaymentMethod) && empty($paymentMethod) && empty($email) && empty($apiKey) && empty($secretKey)) {
        // Insert new payment method
        $insertQuery = "INSERT INTO payment_methods (method) VALUES ('$newPaymentMethod')";
        
        if ($db->query($insertQuery) === TRUE) {
            $msg = "
                <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                <strong><i class='feather icon-check'></i>Success!</strong> The New Payment Method has been added.
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
                </button>
                </div>
            ";
        }
    } else {

        if (!empty($paymentMethod) && !empty($email) && !empty($apiKey) && !empty($secretKey)) {
            // Check if the payment method already exists
            $checkQuery = "SELECT * FROM payment_method_details WHERE method_name = '$paymentMethod'";
            $checkResult = $db->query($checkQuery);
            
            if ($checkResult->num_rows > 0) {
                // Update existing payment method details
                $updateQuery = "UPDATE payment_method_details SET email_address = '$email', api_key = '$apiKey', secret_key = '$secretKey' WHERE method_name = '$paymentMethod'";
                
                if ($db->query($updateQuery) === TRUE) {
                    $msg = "
                        <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                        <strong><i class='feather icon-check'></i>Success!</strong> The Payment Method setting has been updated.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                        </button>
                        </div>
                    ";
                }
            } else {
                // Insert new payment method details
                $insertDetailsQuery = "INSERT INTO payment_method_details (method_name, email_address, api_key, secret_key) VALUES ('$paymentMethod', '$email', '$apiKey', '$secretKey')";
                
                if ($db->query($insertDetailsQuery) === TRUE) {
                    $msg = "
                        <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='goldmessage'>
                        <strong><i class='feather icon-check'></i>Thanks!</strong> The Payment Method setting has been added.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                        </button>
                        </div>
                    ";
                }
            }
        } else {
            // Handle empty fields error
            echo "All fields are required!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Financial Settings </title>

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
                                <h5 class="m-b-10">Financial Settings
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
                            <h6><span data-feather="credit-card"></span> PAYMENT GATEWAY</h6>
                            <hr/>
                            <form method="post" action="">
                                <div class="row">
                                   <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">New Payment Method *</label>
										    <input type="text" name="new_payment_method" class="form-control" placeholder="Enter the new payment method">
										     <small class="text-muted">Leave it blank if you don't want to add new Payment method.</small>
                                        </div>
                                    </div>
                                      
                                     <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                           <div class="form-group">
                                               <label for="payment_method" class="form-label">Select Payment Method *</label>
                    								<?php
                                                        $category_query = "SELECT * FROM payment_methods WHERE status='1'";
                                                        $result = $db->query($category_query);
                                                        
                                                        if ($result->num_rows > 0) {
                                                            echo "<select name='payment_method' class='form-control select'>";
                                                            echo "<option value='' selected disabled>Choose</option>";

                                                            while ($row = $result->fetch_assoc()) {
                                                                 echo "<option value='{$row['method']}'>{$row['method']}</option>";
                                                            }
                                        
                                                             echo "</select>";
                                                         } 
                                                         else 
                                                         {
                                                              echo "No Payment Methods found.";
                                                          }
                                                     ?>
                                            	</div>
                                            </div>
                                    
                                    
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Email Address *</label>
										    <input type="text" name="email" class="form-control" placeholder="Enter the Email Address">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Api Key *</label>
											<input type="text" name="api_key" class="form-control" placeholder="Enter the Api Key">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-6">
                                        <div class="form-group">
                                            <label class="form-label">Secret Key *</label>
											<input type="text" name="secret_key" class="form-control" placeholder="Enter the Secret Key">
                                        </div>
                                    </div>
                                    
                                   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <button type="submit" class="btn btn-secondary" name="submit" id="submit">
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