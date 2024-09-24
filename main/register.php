<?php
include('config.php');
session_start();

if (isset($_SESSION["user-name"])) {
    // if user is already logged redirect to their page
    header("Location: user.php");
    exit;
}

if (isset($_REQUEST['register-button']) && $_REQUEST['register-button'] == "Sign up") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // verify if username is already in use
    $query_verify_username = "SELECT * FROM user WHERE username = '$username'";
    $result_verify_username = mysqli_query($con, $query_verify_username);

    if (mysqli_num_rows($result_verify_username) > 0) {
        echo '<script>alert("Sorry, this username is already in use :(.");</script>';
    } else {
        // verify if email is already in use
        $query_verify_email = "SELECT * FROM user WHERE email = '$email'";
        $result_verify_email = mysqli_query($con, $query_verify_email);

        if (mysqli_num_rows($result_verify_email) > 0) {
            echo '<script>alert("Sorry, this e-mail is already in use :(.");</script>';
        } else {
            $query_register = "INSERT INTO user (username, email, password, hierarchy) VALUES ('$username', '$email', '$password', '2')";
            $result_register = mysqli_query($con, $query_register);

            if ($result_register) {
                echo '<script>alert("Welcome :)");</script>';
                header("Location: user.php");
            } else {
                echo '<script>alert("Ops... :( ' . mysqli_error($con) . '");</script>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <title>myMelody: Register</title>
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
        <a href="login.php"><img src="../resources/back.png" style="width: 20px; height: 20px; margin-top: 10px;"></a>
            <h3 class="reg-h3">Sign up</h3>
        <form action="#" method="post">
            <label style="color: white;">Username:</label><br><input type="text" name="username" placeholder="Username..." required><br>
            <label style="color: white;">E-mail:</label><br><input type="text" name="email" placeholder="E-mail..." required><br>
            <label style="color: white;">Password:</label><br><input type="password" name="password" placeholder="Password..." required><br>
            <input type="submit" name="register-button" value="Sign up"><br>
            <a href="main.php">Main page</a>
        </form>
        </div>
    </main>