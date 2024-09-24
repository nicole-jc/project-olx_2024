<?php
include('config.php');
session_start();

if(isset($_SESSION["user-name"])) {
    // if user is already logged redirect to their page
    header("Location: main.php");
    exit;
}

if (@$_REQUEST['login-button']=="Login")
{
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	
	$query = "SELECT * FROM user WHERE username = '$username' AND password = '$password' ";
	$result = mysqli_query($con, $query);
	while ($column=mysqli_fetch_array($result)) 
	{
		// Session variables
		$_SESSION["user-id"]= $column["userid"]; 
		$_SESSION["user-name"] = $column["username"]; 
		$_SESSION["hierarchy"] = $column["hierarchy"];

		// redirect an user by it's level
		$level = $column['hierarchy'];
		if($level == 2){ 
			header("Location: main.php"); 
			exit; 
		}
		
		if($level == 1){ 
			header("Location: adm.php"); 
			exit; 
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>myMelodies</title>
    <link rel="icon" href="../resources/melody.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">

</head>
<body>
        <!--HEADER-->
<header id="headmenu">
    <div class="left-section">
        <p><a href="main.php"><img src="../resources/melodyfull.png" style="width: 50px; height: 50px;"></a></p>
    </div>
    <div class="center-section">
    </div>
    <div class="right_section">
    </div>
</header>
<div class="main-container">
    <main>
        <div class="register-section">
            <a href="main.php"><img src="../resources/back.png" style="width: 20px; height: 20px; margin-top: 10px;"></a>
            <h3 class="sign-h3">Login</h3>
        <form action="#" method="post">
            <label style="color: white;">Username:</label><br><input type="text" name="username" placeholder="Username..." required><br>
            <label style="color: white;">Password:</label><br><input type="password" name="password" placeholder="Password..." required><br>
            <input type="submit" name="login-button" value="Login"><br>
            <a href="register.php">or sign up</a>
        </form>
        </div>
    </main>