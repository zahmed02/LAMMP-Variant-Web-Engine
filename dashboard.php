<?php
include('header.php');
include('sidebar.php');
include('connect.php'); // Include your database connection file

// Function to fetch count from a table
function fetchCount($conn, $tableName) {
    $sql = "SELECT COUNT(*) AS count FROM $tableName";
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0) ? $result->fetch_assoc()['count'] : 0;
}

// Fetch counts for each entity
$assets_count = fetchCount($conn, 'assets');
$asset_assignment_count = fetchCount($conn, 'assetassignment');
$channels_count = fetchCount($conn, 'channel');
$departments_count = fetchCount($conn, 'departments');
$desks_count = fetchCount($conn, 'desks');
$employees_count = fetchCount($conn, 'users'); // Assuming all users are employees
$locations_count = fetchCount($conn, 'location');
$designation_count = fetchCount($conn, 'designation');

// Close the database connection
$conn->close();
?>

<!-- Page Wrapper -->
<div class="page-wrapper">
            
    <!-- Page Content -->
    <div class="content container-fluid pb-0">
    
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Welcome Admin!</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
    
        <div class="row">
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-box"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $assets_count; ?></h3>
                            <span><a href="assets.php">Assets</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-clipboard-check"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $asset_assignment_count; ?></h3>
                            <span><a href="assetassignment.php">Assets Assignment</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-broadcast-tower"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $channels_count; ?></h3>
                            <span><a href="channels.php">Channels</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-building"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $departments_count; ?></h3>
                            <span><a href="departments.php">Departments</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-desktop"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $desks_count; ?></h3>
                            <span><a href="desks.php">Desks</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-users"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $employees_count; ?></h3>
                            <span><a href="users.php">Employees</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-map-marker-alt"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $locations_count; ?></h3>
                            <span><a href="location.php">Locations</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fa-solid fa-id-badge"></i></span>
                        <div class="dash-widget-info">
                            <h3><?php echo $designation_count; ?></h3>
                            <span><a href="designation.php">Designation</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /Page Content -->

</div>
<!-- /Page Wrapper -->
<?php
include('footer.php');
?>
