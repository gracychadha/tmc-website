<?php
session_start();
require_once('db/config.php');
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Fetch existing data for Company Info section
$sql_check_company = "SELECT * FROM company_info LIMIT 1";
$result_company = $db->query($sql_check_company);

$name = '';
$address = '';
$city = '';
$state = '';
$country = '';
$phone1 = '';
$phone2 = '';
$email = '';
$fax_number = '';
$about_company = '';

if ($result_company->num_rows > 0) {
    $row_company = $result_company->fetch_assoc();
    $name = $row_company['name'];
    $address = $row_company['address'];
    $city = $row_company['city'];
    $state = $row_company['state'];
    $country = $row_company['country'];
    $phone1 = $row_company['phone1'];
    $phone2 = $row_company['phone2'];
    $email = $row_company['email'];
    $fax_number = $row_company['fax_number'];
    $about_company = $row_company['about_company'];
}

// Fetch existing data for Social Links section
$sql_check_social = "SELECT * FROM social_link LIMIT 1";
$result_social = $db->query($sql_check_social);

$facebook = '';
$instagram = '';
$twitter = '';
$linkedin = '';

if ($result_social->num_rows > 0) {
    $row_social = $result_social->fetch_assoc();
    $facebook = $row_social['facebook'];
    $instagram = $row_social['instagram'];
    $twitter = $row_social['twiter'];
    $linkedin = $row_social['linkedin'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['company_submit'])) {
        // Process Company Info section
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $state = isset($_POST['state']) ? $_POST['state'] : '';
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $phone1 = isset($_POST['phone1']) ? $_POST['phone1'] : '';
        $phone2 = isset($_POST['phone2']) ? $_POST['phone2'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $fax_number = isset($_POST['fax_number']) ? $_POST['fax_number'] : '';
        $about_company = isset($_POST['about_company']) ? $_POST['about_company'] : '';

        // Re-fetch the existing data to ensure the result is accurate
        $result_company = $db->query($sql_check_company);

        if ($result_company->num_rows > 0) {
            // Update record
            $row_company = $result_company->fetch_assoc();
            $sql_update = "UPDATE company_info SET name = ?, address = ?, city = ?, state = ?, country = ?, phone1 = ?, phone2 = ?, email = ?, fax_number = ?, about_company = ? WHERE id = ?";
            $stmt = $db->prepare($sql_update);
            $stmt->bind_param("ssssssssssi", $name, $address, $city, $state, $country, $phone1, $phone2, $email, $fax_number, $about_company, $row_company['id']);
            $stmt->execute();
            $_SESSION['message'] = "Company info record updated successfully.";
        } else {
            // Insert record
            $sql_insert = "INSERT INTO company_info (name, address, city, state, country, phone1, phone2, email, fax_number, about_company) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("ssssssssss", $name, $address, $city, $state, $country, $phone1, $phone2, $email, $fax_number, $about_company);
            $stmt->execute();
            $_SESSION['message'] = "Company info record added successfully.";
        }
    }

    if (isset($_POST['social_submit'])) {
        // Process Social Links section
        $facebook = isset($_POST['facebook']) ? $_POST['facebook'] : '';
        $instagram = isset($_POST['instagram']) ? $_POST['instagram'] : '';
        $twitter = isset($_POST['twitter']) ? $_POST['twitter'] : '';
        $linkedin = isset($_POST['linkedin']) ? $_POST['linkedin'] : '';

        // Re-fetch the existing data to ensure the result is accurate
        $result_social = $db->query($sql_check_social);

        if ($result_social->num_rows > 0) {
            // Update record
            $row_social = $result_social->fetch_assoc();
            $sql_update = "UPDATE social_link SET facebook = ?, instagram = ?, twiter = ?, linkedin = ? WHERE id = ?";
            $stmt = $db->prepare($sql_update);
            $stmt->bind_param("ssssi", $facebook, $instagram, $twitter, $linkedin, $row_social['id']);
            $stmt->execute();
            $_SESSION['message'] = "Social links record updated successfully.";
        } else {
            // Insert record
            $sql_insert = "INSERT INTO social_link (facebook, instagram, twiter, linkedin) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("ssss", $facebook, $instagram, $twitter, $linkedin);
            $stmt->execute();
            $_SESSION['message'] = "Social links record added successfully.";
        }
    }

    header("Location: admin-general-setting.php");
    exit();
}

// SQL query with a prepared statement
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";

if ($stmt = $db->prepare($sqlfav)) {
    // Execute the statement
    $stmt->execute();

    // Bind the result to a variable
    $stmt->bind_result($favicon);

    // Fetch the result
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon; // Build the full path
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>General Setting - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php'); ?>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between border-bottom pb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Company Information</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Company Information</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="container mt-5">
                            <!-- Company Info Section -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm">
                                    <h3 class="mb-3">Company Information</h3>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter company name" value="<?php echo htmlspecialchars($name); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" value="<?php echo htmlspecialchars($address); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter city" value="<?php echo htmlspecialchars($city); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="state" class="form-label">State</label>
                                            <input type="text" class="form-control" id="state" name="state" placeholder="Enter state" value="<?php echo htmlspecialchars($state); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control" id="country" name="country" placeholder="Enter country" value="<?php echo htmlspecialchars($country); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone1" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone1" name="phone1" placeholder="Enter phone number" value="<?php echo htmlspecialchars($phone1); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone2" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone2" name="phone2" placeholder="Enter phone number" value="<?php echo htmlspecialchars($phone2); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($email); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="fax_number" class="form-label">Fax Number</label>
                                            <input type="text" class="form-control" id="fax_number" name="fax_number" placeholder="Enter fax number" value="<?php echo htmlspecialchars($fax_number); ?>">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="about_company" class="form-label">About Company</label>
                                            <textarea class="form-control" id="about_company" name="about_company" placeholder="Enter about company"><?php echo htmlspecialchars($about_company); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="company_submit"><?php echo $result_company->num_rows > 0 ? 'Update' : 'Add'; ?></button>
                                    </div>
                                </div>
                            </form>

                            <!-- Social Links Section -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm mt-5">
                                    <h5 class="mb-3">Social Links</h5>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="facebook" class="form-label">Facebook</label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" placeholder="Enter Facebook URL" value="<?php echo htmlspecialchars($facebook); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="instagram" class="form-label">Instagram</label>
                                            <input type="text" class="form-control" id="instagram" name="instagram" placeholder="Enter Instagram URL" value="<?php echo htmlspecialchars($instagram); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="twitter" class="form-label">Twitter</label>
                                            <input type="text" class="form-control" id="twitter" name="twitter" placeholder="Enter Twitter URL" value="<?php echo htmlspecialchars($twitter); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="linkedin" class="form-label">LinkedIn</label>
                                            <input type="text" class="form-control" id="linkedin" name="linkedin" placeholder="Enter LinkedIn URL" value="<?php echo htmlspecialchars($linkedin); ?>">
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="social_submit"><?php echo $result_social->num_rows > 0 ? 'Update' : 'Add'; ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Wrapper -->

        <footer class="footer">
            <div class="mt-5 text-center">
                <?php require_once('copyright.php'); ?>
            </div>
        </footer>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Datetimepicker JS -->
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <!-- Datatable JS -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <!-- Theme Script JS -->
    <script src="assets/js/theme-script.js"></script>

    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert2 notifications
            <?php if (isset($_SESSION['message'])): ?>
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: "<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'error',
                    title: "<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>