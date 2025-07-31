<?php
session_start(); // Start the session at the beginning of your PHP file
// Check if there is a logout message
if (isset($_SESSION['logout_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['logout_message'] . "</div>";
    unset($_SESSION['logout_message']); // Unset the message after displaying it
}
print_r($_SESSION); // This will show the session data for debugging


// Check if session variables are set and display them
// if (isset($_SESSION["favcolor"]) && isset($_SESSION["favanimal"])) {
//     echo "Favorite Color: " . $_SESSION["favcolor"] . "<br>";
//     echo "Favorite Animal: " . $_SESSION["favanimal"] . "<br>";
    
//     // Unset the session variables
//     unset($_SESSION["favcolor"]);
//     unset($_SESSION["favanimal"]);
    
//     echo "Session variables have been unset.<br>";
// } else {
//     echo "Session variables are not set.<br>";
// }
?>


<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Smarthr - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
        <meta name="author" content="Dreamstechnologies - Bootstrap Admin Template">
        <title>Login - HRMS admin template</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">

		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

		<!-- Lineawesome CSS -->
        <link rel="stylesheet" href="assets/css/line-awesome.min.css">
		<link rel="stylesheet" href="assets/css/material.css">
			
		<!-- Lineawesome CSS -->
		<link rel="stylesheet" href="assets/css/line-awesome.min.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">

    </head>
    <body class="account-page">
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<a href="job-list.html" class="btn btn-primary apply-btn">Apply Job</a>
				<div class="container">
				
					<!-- Account Logo -->
					<div class="account-logo">
						<a href="admin-dashboard.html"><img src="assets/img/logo2.png" alt="Dreamguy's Technologies"></a>
					</div>
					<!-- /Account Logo -->
					
					<div class="account-box">
						<div class="account-wrapper">
							<h3 class="account-title">Login</h3>
							<p class="account-subtitle">Access to our dashboard</p>
							
							<!-- Account Form -->
							<form action="admin-dashboard.html">
								<div class="input-block mb-4">
									<label class="col-form-label">Username</label>
									<input class="form-control" name="username" type="text" value="Zubair28">
								</div>
								<div class="input-block mb-4">
									<div class="row align-items-center">
										<div class="col">
											<label class="col-form-label">Password</label>
										</div>
										<div class="col-auto">
											<a class="text-muted" href="forgot-password.html">
												Forgot password?
											</a>
										</div>
									</div>
									<div class="position-relative">
										<input class="form-control" name = "password" type="password" value="123456" id="password">
										<span class="fa-solid fa-eye-slash" id="toggle-password"></span>
									</div>

									<div class="input-block mb-4" style="display: none;">
        						<input type="hidden" name="login" value="0">
    							</div>

								</div>
								<div class="input-block mb-4 text-center">
									<button class="btn btn-primary account-btn" type="submit">Login</button>
								</div>
								<div class="account-footer">
									<p>Don't have an account yet? <a href="register.html">Register</a></p>
								</div>
							</form>
							<!-- /Account Form -->
							
						</div>
					</div>
				</div>
			</div>
        </div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
       <script src="assets/js/jquery-3.7.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>
		
    </body>
</html>

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
                    // document.getElementById("channel").reset();
                    window.location.replace("http://localhost/hr/dashboard.php");
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