<?php 
include('connect.php');

// Fetch locations for dropdown
$locations = [];
$location_q = "SELECT id, location_name FROM location"; // Adjust the table name if necessary
$location_r = $conn->query($location_q);
if ($location_r) {
    while ($row = $location_r->fetch_assoc()) {
        $locations[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asset_name = htmlspecialchars(trim($_POST["asset_name"]));
    $asset_type = htmlspecialchars(trim($_POST["asset_type"]));
    $serial_number = htmlspecialchars(trim($_POST["serial_number"]));
    $purchase_date = htmlspecialchars(trim($_POST["purchase_date"]));
    $purchase_cost = isset($_POST["purchase_cost"]) ? floatval($_POST["purchase_cost"]) : 0.0;
    $status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
    $location_id = isset($_POST["location_id"]) ? intval($_POST["location_id"]) : 0;

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($asset_name) || empty($asset_type) || empty($serial_number) || empty($purchase_date) || $purchase_cost <= 0 || $location_id === 0) {
        $response['msg'] = 'All fields are required and purchase cost must be greater than zero.';
    } else {
        // Check for duplicate asset
        $check_q = "SELECT * FROM assets WHERE serial_number = '".$conn->real_escape_string($serial_number)."'";
        $check_r = $conn->query($check_q);

        if ($check_r && $check_r->num_rows > 0) {
            $response['msg'] = 'Asset with this serial number already exists. Choose a different serial number.';
        } else {
            // Insert new asset
            $insert_q = "INSERT INTO `assets`(`asset_name`, `asset_type`, `serial_number`, `purchase_date`, `purchase_cost`, `status`, `location`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                        VALUES ('".$conn->real_escape_string($asset_name)."', '".$conn->real_escape_string($asset_type)."', '".$conn->real_escape_string($serial_number)."', '$purchase_date', $purchase_cost, $status, $location_id, 1, NOW(), 1, NOW())";
            $insert_r = $conn->query($insert_q);

            if ($insert_r) {
                $response['msg'] = 'Asset successfully inserted.';
                $response['msg_code'] = 0; // Success
            } else {
                $response['msg'] = 'Error inserting asset: ' . $conn->error;
            }
        }
    }

    // Return JSON response
    echo json_encode($response);
    exit();
}

// Determine add vs update
if (isset($_GET['asset_id'])) {
    $assetId = intval($_GET['asset_id']);
    $as = $conn->query("SELECT * FROM assets WHERE asset_id = $assetId")->fetch_assoc();
    $asset_name = $as['asset_name'];
    $asset_type = $as['asset_type'];
    $serial_number = $as['serial_number'];
    $purchase_date = $as['purchase_date'];
    $purchase_cost = $as['purchase_cost'];
    $status = $as['status'];
    $location_id = intval($as['location']);
    $btn_text = 'Update';
} else {
    $assetId = 0;
    $asset_name = '';
    $asset_type = '';
    $serial_number = '';
    $purchase_date = '';
    $purchase_cost = 0.0;
    $status = 1; // Default to Active
    $location_id = 0;
    $btn_text = 'Add';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert / Update Asset</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<form id="assetForm" method="post">
    <label>Asset Name:</label><br>
    <input type="text" name="asset_name" required value="<?= htmlspecialchars($asset_name) ?>"><br><br>

    <label>Asset Type:</label><br>
    <input type="text" name="asset_type" required value="<?= htmlspecialchars($asset_type) ?>"><br><br>

    <label>Serial Number:</label><br>
    <input type="text" name="serial_number" required value="<?= htmlspecialchars($serial_number) ?>"><br><br>

    <label>Purchase Date:</label><br>
    <input type="datetime-local" name="purchase_date" required value="<?= htmlspecialchars($purchase_date) ?>"><br><br>

    <label>Purchase Cost:</label><br>
    <input type="number" step="0.01" name="purchase_cost" required value="<?= htmlspecialchars($purchase_cost) ?>"><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Inactive</option>
    </select><br><br>

    <label>Select Location:</label><br>
    <select name="location_id" required>
        <option value="">-- Select --</option>
        <?php foreach ($locations as $loc): ?>
            <option value="<?= $loc['id'] ?>" <?= $loc['id'] == $location_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($loc['location_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="hidden" name="asset" value="<?= $assetId ?>">
    <input type="submit" value="<?= $btn_text ?>">
</form>

<script>
$(function(){
    $('#assetForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: 'add_assets.php', // The same file to handle the form submission
            method: 'POST',
            data: $(this).serialize(), // Serialize the form data
            dataType: 'json'
        }).done(function(data){
            alert(data.msg); // Show the message from the server
            if (data.msg_code === 0) {
                $('#assetForm')[0].reset(); // Reset the form if successful
                // Optionally, you can refresh the assets table here
                // location.reload(); // Uncomment to refresh the page
            }
        }).fail(function() {
            alert('An error occurred while processing your request.');
        });
    });
});
</script>

</body>
</html>
