<?php
session_start();
if (!isset($_SESSION["login_user"])) {
    header("location: index.php");
}
$name = $_SESSION['login_user'];
include("db/config.php");
$query = "SELECT post.*, GROUP_CONCAT(parent_category.parent_category_name) AS post_categories
          FROM post
          LEFT JOIN post_categories ON post.post_id = post_categories.post_id
          LEFT JOIN parent_category ON post_categories.parent_category_id = parent_category.parent_category_id
          GROUP BY post.post_id";
$result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <title>All Posts</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
                                <h5 class="m-b-10">All Posts
                                </h5>
                            </div>
<!-- 							<ul class="breadcrumb"> -->
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
                        </div>
                        <div class="card-body">
                           <form id="deleteForm" action="delete-post.php" method="post">
                         <button type="button" id="deleteSelected" class="btn btn-danger mb-2">Delete Selected</button>
                            <div class="dt-responsive table-responsive">
                                <?php
                                if (isset($_GET['status'])) {
                                    $st = $_GET['status'];
                                    $st1 = base64_decode($st);
                                    if ($st1 > 0) {
                                        echo " <div class='alert alert-success alert-dismissible fade show' role='alert' style='font-size:16px;' id='gold'>
  <strong><i class='feather icon-check'></i>Success!</strong> Post has been Updated Successfully.
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
  </button>
</div> ";
                                    } else {
                                        echo " <div class='alert alert-danger alert-dismissible fade show' role='alert' style='font-size:16px;' id='gold'>
  <strong>Error!</strong> Post not Updated
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
  </button>
</div> ";
                                    }
                                }
                                ?>
                                <br />
                                <?php
                                echo '<table id="basic-btn" class="table table-striped table-bordered nowrap">';
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>SELECT</th>";
                                echo "<th>SNO</th>";
                                echo "<th>Title</th>";
                                echo "<th>Post Category</th>";
                                //echo "<th>Description</th>";
                                echo "<th>Image</th>";
                                echo "<th>Added By</th>";
                                echo "<th>Status</th>";
                                echo "<th>Edit</th>";
                                echo "</tr>";
                                echo "</thead>";
                                ?>
                                <?php
                                $count = 1;
                                echo "<tbody>";
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr class='record'>";
                                    $encoded_id = base64_encode($row['post_id']); // Encode the category ID
                                    echo "<td><input type='checkbox' name='post_ids[]' value='$encoded_id'></td>";
                                    echo "<td> $count </td>";
                                    echo "<td>" . $row['title'] . "</td>";
                                    echo "<td>";
                                        $categories = explode(',', $row['post_categories']);
                                            echo "<div>";
                                                for ($i = 0; $i < count($categories); $i++)
                                                {
                                                    echo "<div>{$categories[$i]}";
                                                    if ($i < count($categories) - 1)
                                                    {
                                                        echo ",";
                                                    }
                                                echo "</div>";
                                                }
                                            echo "</div>";
                                    echo "</td>";
//                                  $truncatedContent = strlen($row['content']) > 20 ? substr($row['content'], 0, 20) . "..." : $row['content'];
//                                  echo "<td>" . $truncatedContent . "</td>";
                                    echo "<td>" . '<img src="post/' . $row['image'] . '" style="width:150px;height:150px" />' . "</td>";
                                    echo "<td>" . $row['publish'] . "</td>";
                                    echo "<td>";
                                    if ($row['status'] == 1) {
                                        echo "Enable";
                                    } else {
                                        echo "Disable";
                                    }
                                    echo "</td>";
                                    echo "<td>
            <a href='edit-post.php?id=$encoded_id' class='btn btn-warning'>
                <i class='feather icon-edit'></i> Edit
            </a>
            <a href='delete-post.php?id=$encoded_id' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this post?\")'>
                <i class='feather icon-trash'></i> Delete
            </a>
        </td>";
                                    echo "</tr>";
                                    $count++;
                                }
                                echo "</tfoot>";
                                echo "</table>";
                                ?>
                            </div>
                           <br/>
                        </form>
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
        $("#gold").delay(5000).slideUp(300);
    });
    </script>
    <script type="text/javascript">
    $(function() {
        $(".delbutton").click(function() {
            var element = $(this);
            var del_id = element.attr("id");
            var info = 'id=' + del_id;
            if (confirm("Are you sure you want to delete this Record?")) {
                $.ajax({
                    type: "GET",
                    url: "deletegold.php",
                    data: info,
                    success: function() {}
                });
                $(this).parents(".record").animate({
                        backgroundColor: "#FF3"
                    }, "fast")
                    .animate({
                        opacity: "hide"
                    }, "slow");
            }
            return false;
        });
    });
    </script>
     <script>
        // JavaScript to handle deletion of selected categories
        document.getElementById("deleteSelected").addEventListener("click", function() {
            var form = document.getElementById("deleteForm");
            var checkboxes = form.querySelectorAll("input[type=checkbox]:checked");
            if (checkboxes.length === 0) {
                alert("Please select at least one post to delete.");
            } else {
                if (confirm("Are you sure you want to delete the selected posts?")) {
                    form.submit();
                }
            }
        });
    </script>
</body>
</html>