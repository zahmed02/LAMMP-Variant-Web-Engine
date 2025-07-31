<?php
include('../connect.php');


if(isset($_POST['channel'])) {
    $channel_name = htmlspecialchars(trim($_POST["user_input"]));
    $channel_id = $_POST['channel'];

    if($channel_id == 0) {
        // Check if channel_name already exists
        $check_q = "SELECT * FROM channel WHERE channel_name = '".$conn->real_escape_string($channel_name)."'";
        $check_r = $conn->query($check_q);

        if ($check_r->num_rows > 0) {
            $data = array(
                "msg"   =>  $channel_name . " Channel name already exists. Choose a different name.",
                "msg_code" =>  1
            );
        } else {
            // Insert new channel_name
            $insert_q = "INSERT INTO `channel`(`channel_name`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                        VALUES ('".$conn->real_escape_string($channel_name)."','1','".$date_time."','1','".$date_time."')";
            $insert_r = $conn->query($insert_q);

            if ($insert_r) {
                $data = array(
                    "msg"   =>  $channel_name . " Channel successfully inserted.",
                    "msg_code" =>  0
                );

                // echo "<p style='color:green;'>Channel successfully inserted.</p>";
            } else {
                $data = array(
                    "msg"   =>  $channel_name . " Error inserting data.",
                    "msg_code" =>  1
                );
            }
        }
    } else {
        $qu = "UPDATE `channel` SET `channel_name`='".$channel_name."', `modified_by`='1', `modified_at`='".$date_time."' WHERE id = $channel_id";
        $ru = $conn->query($qu);

        if($ru) {
            $data = array(
                "msg"   =>  $channel_name . " Channel successfully updated.",
                "msg_code" =>  0
            );
        } else {
            $data = array(
                "msg"   =>  $channel_name . " Error inserting data.",
                "msg_code" =>  1
            );
        }

    }

    

    echo json_encode($data, TRUE);
}


if(isset($_POST['department'])) {
$deptId = intval($_POST['department']);
    $name   = $conn->real_escape_string(trim($_POST['department_name']));
    $cid    = intval($_POST['channel_id']);

    if ($deptId === 0) {
        // Check duplicate
        $chk = $conn->query("SELECT 1 FROM departments WHERE department_name='$name'");
        if ($chk && $chk->num_rows > 0) {
            $data = ['msg'=>"$name already exists.", 'msg_code'=>1];
        } else {
            $ins = $conn->query("INSERT INTO departments(department_name,channel_id,created_by,created_at,modified_by,modified_at)
                VALUES('$name',$cid,1,'$date_time',1,'$date_time')");
            $data = $ins
                ? ['msg'=>"$name inserted.", 'msg_code'=>0]
                : ['msg'=>"Error: " . $conn->error, 'msg_code'=>1];
        }
    } else {
        $upd = $conn->query("UPDATE departments SET 
            department_name='$name',
            channel_id=$cid,
            modified_by=1,
            modified_at='$date_time'
            WHERE id=$deptId");
        $data = $upd
            ? ['msg'=>"$name updated.", 'msg_code'=>0]
            : ['msg'=>"Error: " . $conn->error, 'msg_code'=>1];
    }
    header('Content-Type: application/json');
    echo json_encode($data);

}

if(isset($_POST['desk'])) {
  $did = intval($_POST['desk']);
  $nm  = $conn->real_escape_string(trim($_POST['desk_name']));
  $cid = intval($_POST['channel_id']);
  if($did===0){
    $chk = $conn->query("SELECT 1 FROM desks WHERE desk_name='$nm'");
    if($chk && $chk->num_rows){
      $data=['msg'=>"$nm exists.",'msg_code'=>1];
    } else {
      $ins=$conn->query("INSERT INTO desks(desk_name,channel_id,created_by,created_at,modified_by,modified_at)
        VALUES('$nm',$cid,1,'$date_time',1,'$date_time')");
      $data=$ins?['msg'=>"$nm added.",'msg_code'=>0]:['msg'=>"Error:".$conn->error,'msg_code'=>1];
    }
  } else {
    $upd=$conn->query("UPDATE desks SET desk_name='$nm',channel_id=$cid,modified_by=1,modified_at='$date_time' WHERE id=$did");
    $data=$upd?['msg'=>"$nm updated.",'msg_code'=>0]:['msg'=>"Error:".$conn->error,'msg_code'=>1];
  }
  header('Content-Type:application/json');
  echo json_encode($data);
}

if(isset($_POST['designation'])) {
        $did  = intval($_POST['designation']);
    $nm   = $conn->real_escape_string(trim($_POST['designation_name']));

    if ($did === 0) {
        $chk = $conn->query("SELECT 1 FROM designation WHERE designation_name='$nm'");
        if ($chk && $chk->num_rows) {
            $data=['msg'=>"$nm exists.",'msg_code'=>1];
        } else {
            $ins = $conn->query("INSERT INTO designation(designation_name,created_by,created_at,modified_by,modified_at)
                                 VALUES('$nm',1,'$date_time',1,'$date_time')");
            $data = $ins ? ['msg'=>"$nm added.",'msg_code'=>0] : ['msg'=>"Error:".$conn->error,'msg_code'=>1];
        }
    } else {
        $upd = $conn->query("UPDATE designation SET designation_name='$nm',modified_by=1,modified_at='$date_time' WHERE id=$did");
        $data = $upd ? ['msg'=>"$nm updated.",'msg_code'=>0] : ['msg'=>"Error:".$conn->error,'msg_code'=>1];
    }
    header('Content-Type:application/json'); 
    echo json_encode($data);
}

if (isset($_POST['users'])) {
    $uid = intval($_POST['users']);
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string($_POST['password']);
    $ec = intval($_POST['employee_code']);
    $em = $conn->real_escape_string(trim($_POST['email']));
    $fn = $conn->real_escape_string(trim($_POST['full_name']));
    $di = intval($_POST['designation_id']);
    $dp = intval($_POST['department_id']);
    $dk = intval($_POST['desk_id']);
    $lm = intval($_POST['line_manager']);
    $hd = intval($_POST['hod']);
    $ci = intval($_POST['channel_id']);
    $li = intval($_POST['location_id']);
    $ur = intval($_POST['user_role']);
    $ia = intval($_POST['is_active']);
    // INSERT
    if ($uid === 0) {
        // Check if employee_code OR email already exist
        $chk = $conn->query("SELECT 1 FROM users WHERE employee_code = $ec OR email = '$em'");
        if ($chk && $chk->num_rows) {
            $data = ['msg' => 'Employee Code or Email already exists.', 'msg_code' => 1];
        } else {
            $ins = $conn->query("INSERT INTO users(username, password, employee_code, email, full_name, designation_id, department_id, desk_id, line_manager, hod, channel_id, location_id, user_role, is_active, created_by, created_at, modified_by, modified_at)
            VALUES('$username', '$password', $ec, '$em', '$fn', $di, $dp, $dk, $lm, $hd, $ci, $li, $ur, $ia, 1, '$date_time', 1, '$date_time')");
            $data = $ins ? ['msg' => 'User added.', 'msg_code' => 0] : ['msg' => 'Error: ' . $conn->error, 'msg_code' => 1];
        }
    // UPDATE
    } else {
        // Check if employee_code or email is being used by someone else
        $chk = $conn->query("SELECT 1 FROM users WHERE (employee_code = $ec OR email = '$em') AND id != $uid");
        if ($chk && $chk->num_rows) {
            $data = ['msg' => 'Employee Code or Email already exists for another user.', 'msg_code' => 1];
        } else {
            $upd = $conn->query("UPDATE users SET 
                employee_code = $ec, 
                email = '$em', 
                full_name = '$fn', 
                designation_id = $di, 
                department_id = $dp, 
                desk_id = $dk, 
                line_manager = $lm, 
                hod = $hd, 
                channel_id = $ci, 
                location_id = $li, 
                user_role = $ur, 
                is_active = $ia, 
                modified_by = 1, 
                modified_at = '$date_time' 
                WHERE id = $uid");

            $data = $upd ? ['msg' => 'User updated.', 'msg_code' => 0] : ['msg' => 'Error: ' . $conn->error, 'msg_code' => 1];
        }
    }
    header('Content-Type: application/json'); 
    echo json_encode($data);
}

if(isset($_POST['location'])) {
    $lid = intval($_POST['location']);
    $nm  = $conn->real_escape_string(trim($_POST['location_name']));
    if ($lid === 0) {
        $chk = $conn->query("SELECT 1 FROM location WHERE location_name='$nm'");
        if ($chk && $chk->num_rows) {
            $data=['msg'=>"$nm exists.",'msg_code'=>1];
        } else {
            $ins = $conn->query("INSERT INTO location(location_name,created_by,created_at,modified_by,modified_at)
                                 VALUES('$nm',1,'$date_time',1,'$date_time')");
            $data = $ins?['msg'=>"$nm added.",'msg_code'=>0]:['msg'=>"Error:".$conn->error,'msg_code'=>1];
        }
    } else {
        $upd = $conn->query("UPDATE location SET location_name='$nm',modified_by=1,modified_at='$date_time' WHERE id=$lid");
        $data = $upd?['msg'=>"$nm updated.",'msg_code'=>0]:['msg'=>"Error:".$conn->error,'msg_code'=>1];
    }
    header('Content-Type:application/json'); 
    echo json_encode($data);
}

if (isset($_POST['login'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    // Query to check if the user exists
    $query = "SELECT id, employee_code, email, full_name FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        // User found, fetch details
        $user = $result->fetch_assoc();
        // Set session variables
        $_SESSION['id'] = $user['id'];
        $_SESSION['employee_code'] = $user['employee_code'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];

        $data = [
            "msg" => "Login successful.",
            "msg_code" => 0
        ];
    } else {
        // User not found
        $data = [
            "msg" => "Invalid username or password.",
            "msg_code" => 1
        ];
    }

    // print_r($_SESSION);

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    
    // Prepare the response
    $data = [
        "msg" => "Logout successful. Session has been destroyed.",
        "msg_code" => 0
    ];
    // Optionally, you can echo a message for debugging purposes
    echo "Session has been destroyed."; // This will be visible in the server response, not in the browser
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}