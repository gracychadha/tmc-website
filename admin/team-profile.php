<?php
session_start();
require_once('db/config.php');
require_once('includes/mailer.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit();
}

$admin_id = base64_decode($_SESSION['adminId']);
date_default_timezone_set('Asia/Kolkata');

// Fetch the logged-in admin account
$stmt = $db->prepare("SELECT * FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result_admin = $stmt->get_result();
$stmt->close();
$admin = $result_admin->fetch_assoc();

$team_id = isset($admin['team_id']) ? intval($admin['team_id']) : 0;
$team = null;
if ($team_id > 0) {
    $stmt = $db->prepare("SELECT * FROM team_members WHERE idteam_members = ?");
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result_team = $stmt->get_result();
    $stmt->close();
    if ($result_team->num_rows > 0) {
        $team = $result_team->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['team_profile_submit'])) {
        if (!$team) {
            $_SESSION['message'] = "No team profile is linked to your account.";
        } else {
            $member_name = htmlspecialchars(strip_tags(trim($_POST['member_name'])));
            $role = htmlspecialchars(strip_tags(trim($_POST['role'])));
            $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
            $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
            $facebook = htmlspecialchars(strip_tags(trim($_POST['facebook'])));
            $linkedin = htmlspecialchars(strip_tags(trim($_POST['linkedin'])));
            $twitter = htmlspecialchars(strip_tags(trim($_POST['twitter'])));
            $bio = $_POST['content'];

            // Email validation + uniqueness across admin accounts
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = "Please enter a valid email address.";
                header("Location: team-profile.php");
                exit();
            }
            $stmt = $db->prepare("SELECT admin_id FROM admin WHERE email = ? AND admin_id != ?");
            $stmt->bind_param("si", $email, $admin_id);
            $stmt->execute();
            $dup = $stmt->get_result();
            $stmt->close();
            if ($dup->num_rows > 0) {
                $_SESSION['message'] = "That email is already used by another account.";
                header("Location: team-profile.php");
                exit();
            }

            // Handle profile picture upload
            $image_path = $team['profile_picture'];
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
                if ($check !== false) {
                    $targetFile = "team/" . basename($_FILES["featured_image"]["name"]);
                    if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                        $image_path = $targetFile;
                    } else {
                        $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                        header("Location: team-profile.php");
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "File is not an image.";
                    header("Location: team-profile.php");
                    exit();
                }
            }

            $username = htmlspecialchars(strip_tags(trim($_POST['username'])));
            if ($username === '') {
                $username = $admin['username'];
            }

            $stmt = $db->prepare("UPDATE team_members SET member_name = ?, role = ?, email = ?, phone = ?, linkedin = ?, twitter = ?, facebook = ?, bio = ?, profile_picture = ? WHERE idteam_members = ?");
            $stmt->bind_param("sssssssssi", $member_name, $role, $email, $phone, $linkedin, $twitter, $facebook, $bio, $image_path, $team_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $db->prepare("UPDATE admin SET username = ?, email = ?, phone = ? WHERE admin_id = ?");
            $stmt->bind_param("sssi", $username, $email, $phone, $admin_id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = "Team profile updated successfully!";
        }
        header("Location: team-profile.php");
        exit();
    }

    if (isset($_POST['password_submit'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (password_verify($current_password, $admin['password'])) {
            if ($new_password === $confirm_password && strlen($new_password) >= 6) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE admin SET password = ? WHERE admin_id = ?");
                $stmt->bind_param("si", $hashed_new_password, $admin_id);
                $stmt->execute();
                $stmt->close();
                $_SESSION['message'] = "Password updated successfully!";
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['message'] = "New password and confirm password do not match.";
            } else {
                $_SESSION['message'] = "Password must be at least 6 characters long.";
            }
        } else {
            $_SESSION['message'] = "Current password is incorrect.";
        }
        header("Location: team-profile.php");
        exit();
    }
}

// Re-fetch after possible update
if ($team_id > 0) {
    $stmt = $db->prepare("SELECT * FROM team_members WHERE idteam_members = ?");
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result_team = $stmt->get_result();
    $stmt->close();
    if ($result_team->num_rows > 0) {
        $team = $result_team->fetch_assoc();
    }
}

// Fetch favicon
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";
if ($stmt = $db->prepare($sqlfav)) {
    $stmt->execute();
    $stmt->bind_result($favicon);
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon;
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
    <title>My Team Profile - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>

    <div class="main-wrapper">
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php'); ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">My Team Profile</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Team Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <?php if (!$team): ?>
                    <div class="alert alert-info">No team profile is linked to your account. Please contact the administrator.</div>
                <?php else: ?>

                    <div class="row">
                        <!-- Team Profile -->
                        <div class="col-lg-7 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">Team Profile Information</h4>
                                </div>
                                <div class="card-body">
                                    <form action="team-profile.php" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" name="member_name" class="form-control" value="<?php echo htmlspecialchars($team['member_name']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Designation</label>
                                                <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($team['role']); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Username (Login)</label>
                                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email (Login)</label>
                                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($team['email']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($team['phone']); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Profile Image</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?php echo !empty($team['profile_picture']) ? htmlspecialchars($team['profile_picture']) : 'assets/img/default-avatar.png'; ?>" alt="Profile" class="avatar avatar-xl rounded-circle object-fit-cover">
                                                    <input type="file" class="form-control" name="featured_image" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Facebook Link</label>
                                                <input type="url" name="facebook" class="form-control" value="<?php echo htmlspecialchars($team['facebook']); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">LinkedIn</label>
                                                <input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($team['linkedin']); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Twitter Link</label>
                                                <input type="url" name="twitter" class="form-control" value="<?php echo htmlspecialchars($team['twitter']); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Bio</label>
                                                <div id="summernote"></div>
                                                <input type="hidden" name="content" id="team-profile-content" value="">
                                            </div>
                                        </div>
                                        <button type="submit" name="team_profile_submit" class="btn btn-primary">Save Team Profile</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Login Security -->
                        <div class="col-lg-5 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">Login Security</h4>
                                </div>
                                <div class="card-body">
                                    <form action="team-profile.php" method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="new_password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" name="confirm_password" class="form-control" required>
                                        </div>
                                        <button type="submit" name="password_submit" class="btn btn-primary">Change Password</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <footer class="footer">
                <div class="mt-5 text-center">
                    <?php require_once('copyright.php'); ?>
                </div>
            </footer>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/bootstrap.bundle.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/moment.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/feather.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/jquery.slimscroll.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/jquery.dataTables.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/plugins/summernote/summernote-lite.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            <?php if ($team): ?>
                $('#summernote').summernote({
                    height: 150
                });
                $('#summernote').summernote('code', <?php echo json_encode($team['bio']); ?>);

                $('form').on('submit', function() {
                    var content = $('#summernote').summernote('code');
                    $('#team-profile-content').val(content);
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>