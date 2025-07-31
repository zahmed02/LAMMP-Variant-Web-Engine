<?php 
  include('connect.php');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert Channel</title>

    <script src="https://portal.humnetwork.tv//assets/js/jquery-3.7.1.min.js"></script>
</head>
<body>

<?php

if(isset($_GET['channel_id'])) {
    $channel_id = $_GET['channel_id'];

    $qs = "SELECT * FROM channel WHERE id = $channel_id";
    $rs = $conn->query($qs);

    foreach($rs as $ds){
        $channel_name = $ds['channel_name'];
    }

    $btn_text = "Update";

} else {
    $channel_id = 0;
    $channel_name = '';
    $btn_text = "Add";
}



// echo $channel_id;

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $channel_name = htmlspecialchars(trim($_POST["user_input"]));

//     // Check if channel_name already exists
//     $check_q = "SELECT * FROM channel WHERE channel_name = '".$conn->real_escape_string($channel_name)."'";
//     $check_r = $conn->query($check_q);

//     if ($check_r->num_rows > 0) {
//         echo "<p style='color:red;'>Channel name already exists. Choose a different name.</p>";
//     } else {
//         // Insert new channel_name
//         $insert_q = "INSERT INTO `channel`(`channel_name`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
//                      VALUES ('".$conn->real_escape_string($channel_name)."','1','".$date_time."','1','".$date_time."')";
//         $insert_r = $conn->query($insert_q);

//         if ($insert_r) {
//             echo "<p style='color:green;'>Channel successfully inserted.</p>";
//         } else {
//             echo "<p style='color:red;'>Error inserting data.</p>";
//         }
//     }
// }
?>

<form method="post" action="" id="channel">
    <label for="user_input">Enter something:</label><br>
    <input type="text" id="user_input" name="user_input" value="<?php echo $channel_name; ?>" required><br><br>
    <input type="hidden" name="channel" value="<?php echo $channel_id; ?>" />
    <input type="submit" value="<?php echo $btn_text; ?>">
</form>

<script>
    $(document).ready(function () {
        $('form').on('submit', function (e) {
            $.ajax({
                url: "inc/process.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                dataType: "json",
                processData: false,
                }).done(function (data) {
                    //alert(data.msg_code)
                    if(data.msg_code == 0) {
                        alert(data.msg);
                    document.getElementById("channel").reset();
                    //window.location.replace("https://portal.humnetwork.tv/");
                    } else {
                        alert(data.msg);
                    }
                    console.log(data);
                //   console.log(data);
                });

                event.preventDefault();
        });
    });
</script>

</body>
</html>