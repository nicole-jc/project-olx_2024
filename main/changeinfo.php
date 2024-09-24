<?php
include('config.php');
session_start(); 

if (!isset($_SESSION["user-name"])) {
    // if user is not logged in
    header("Location: login.php");
    exit;
}
$user_logged = isset($_SESSION["user-id"]) ? intval($_SESSION["user-id"]) : 0;
 // Upload profile pic SQL
 if(isset($_POST['pic-btn'])){
    if(isset($_FILES['new-pic'])){
        $file = $_FILES['new-pic'];
        if($file['error'] === UPLOAD_ERR_OK){
            $folder = "../profilepic/";
            $namefile = $file['name'];
            $newfilename = uniqid();
            $extension = strtolower(pathinfo($namefile, PATHINFO_EXTENSION));
            $destinypath = $folder . $newfilename . '.' . $extension;

            $query_verify_pic = "SELECT * FROM profilepic where user_id = '$user_logged'";
            $result_verify_pic = mysqli_query($con, $query_verify_pic);

            if(mysqli_num_rows($result_verify_pic) > 0){
                $query_delete_old_pic = "DELETE FROM profilepic where user_id = '$user_logged'";
                $result_delete_old = mysqli_query($con, $query_delete_old_pic);

                if (!$result_delete_old) {
                    echo '<script>alert("Error deleting old profile picture: ' . mysqli_error($con) . '");</script>';
                }
            }

            if (move_uploaded_file($file['tmp_name'], $destinypath)) {
                $query_upload_pic = "INSERT INTO profilepic (user_id, profile_path) VALUES ('$user_logged', '$destinypath')";
                $result_upload = mysqli_query($con, $query_upload_pic);

                if ($result_upload) {
                    echo '<script>alert("Profile pic updated!");</script>';
                    header("Location: user.php");
                } else {
                    echo '<script>alert("Error: ' . mysqli_error($con) . '");</script>';

        }
    
    }
}
}
    }

// Change username
if(isset($_POST['user-btn'])){
    $newuser = $_POST['new-user'];

     // verify if username is already in use
     $query_verify_username = "SELECT * FROM user WHERE username = '$username'";
     $result_verify_username = mysqli_query($con, $query_verify_username);
 
     if (mysqli_num_rows($result_verify_username) > 0) {
         echo '<script>alert("Sorry, this username is already in use :(.");</script>';
     } else {

    $query_change_username = $con->prepare("UPDATE user SET username = ? WHERE userid = ?");
    $query_change_username->bind_param("si", $newuser, $user_logged);
    
    if($query_change_username->execute()){
        echo '<script>alert("Username changed!")</script>';
        header("Location: user.php");
    } else {
        echo "Failed: " . htmlspecialchars($query_change_username->error);
    }

    }
}


 // Update E-mail   
 if(isset($_POST['email-btn'])){
    $newemail = $_POST['new-email'];

    $query_verify_email = "SELECT * FROM user WHERE email = '$email'";
        $result_verify_email = mysqli_query($con, $query_verify_email);

        if (mysqli_num_rows($result_verify_email) > 0) {
            echo '<script>alert("Sorry, this e-mail is already in use :(.");</script>';
        } else {
        $query_change_username = $con->prepare("UPDATE user SET email = ? WHERE userid = ?");
        $query_change_username->bind_param("si", $newemail, $user_logged);
    
        if($query_change_username->execute()){
        echo '<script>alert("E-mail changed!")</script>';
        header("Location: user.php");
        } else {
        echo "Failed: " . htmlspecialchars($query_change_username->error);
      }

    }
}

if($user_logged > 0){
    $query_details_user = $con->prepare("SELECT username, email FROM user WHERE userid = ?");
    $query_details_user->bind_param("i", $user_logged);
    $query_details_user->execute();
    $result_user = $query_details_user->get_result();

    if($user_details = $result_user->fetch_assoc()){
        $username = $user_details['username'];
        $email = $user_details['email'];

    }

    $query_profile_pic = $con->prepare("SELECT pic_id, profile_path FROM profilepic WHERE user_id = ?");
    $query_profile_pic->bind_param("i", $user_logged);
    $query_profile_pic->execute();
    $result_pic = $query_profile_pic->get_result();
        
        if ($result_pic->num_rows > 0) {
            $user_pic = $result_pic->fetch_assoc();
            $profile_path = $user_pic['profile_path'];
        } else {
            // if profile is not set
            $profile_path = "../resources/anony.png";
        }
    }


?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Settings</title>
    <link rel="icon" href="../resources/melody.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <style>
               .user-section {
    background-color: #1e1e1e;
    padding: 30px;
    border-radius: 8px;
    width: 500px;
    height: 550px;
    padding: 10px;
    margin: 0;
    position: relative;
    left: 450px;
    top: 50px;
  
}
.profile-pic {
    display: block;
    margin: 0 auto;
    border-radius: 50%;
    width: 200px;
    transition: 0.3s;
}

.profile-pic:hover{
    transform: scale(1.1);
}
.user-section input[type="text"], input[type="file"]{
    width: calc(70% - 25px);
    padding: 10px;
    margin: 10px 0;
    margin-left: 25px;
    border-radius: 5px;
    border: 2px solid #717171;
    background-color: #343434;
    color:#d5d5d5;
    font-size: 15px;
    box-shadow: 8px 8px 8px rgba(0, 0, 0, 0.2);
    font-family: 'Work Sans', sans-serif;
}
.user-section h2{
    font-family: 'Dancing script', cursive;
    margin-left: 35%;
    color: #d5d5d5;
} 

 .user-section a:hover{
    transform: scale(1.1);
 }
 
 .profile-item {
    position: relative;
    display: inline-block;
}

.profile-img {
    transition: transform 1s ease;
}

.profile-img:hover {
    transform: rotate(360deg);
}

.dropdown-content-logout {
    display: none;
    position: absolute;
    top: 70px;
    left: 0;
    max-width: 60px;
    border-radius: 8px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    padding: 5px;
    background-color: #1e1e1e;
    font-family: 'Work Sans', sans-serif;
    font-size: 15px;
    color: #d5d5d5;
    transition: background-color 0.3s, color 0.3s, transform 0.5s;
   text-align: center;
}

.dropdown-content-logout:hover {
    transform: scale(1.1);
    background-color: #1e1e1e;
}

.profile-item:hover .dropdown-content-logout {
    display: block;
}
    </style>
    </head>
<body>
      <!--HEADER-->
<header id="headmenu">
    <div class="left-section">
        <p><a href="main.php"><img src="../resources/melodyfull.png" style="width: 50px; height: 50px;"></a></p>
    </div>
    <div class="center-section">
        <form action="#" method="get" class="search-form">
        <?php
        if (isset($_GET['search'])) {
            $searching = mysqli_real_escape_string($con, $_GET['search']);
            header("Location: main.php?search=$searching");
        exit();

        }
            ?>
            <input type="text" name="search" placeholder="Search..." class="search-input">
            <button type="submit" class="search-button">
            <img src="../resources/search.png" alt="Search Icon" class="search-icon"></button>
        </form>
    </div>
    <div class="right_section">
        <nav>
        <ul>
            <?php
            // verify if user is already logged.
            if (isset($_SESSION["user-name"])) {
                echo '<li class="profile-item">
                        <a href="user.php">';
                
                        if ($profile_path) {
                            echo '<img src="' . $profile_path . '" class="profile-img" style="width: 50px; height: 50px; margin-top: 0px; margin-left: 5px; border-radius: 30px;">
                          </a>
                          <div class="dropdown-content-logout">
                              <a href="logout.php">Logout</a>
                          </div>
                      </li>';
                } else {
                    echo '<img src="../resources/user.jpg" class="profile-img" style="width: 50px; height: 50px; margin-top: 0px; margin-left: 5px; border-radius: 30px;">
                          </a>
                          <div class="dropdown-content-logout">
                              <a href="logout.php">Logout</a>
                          </div>
                      </li>';
                }
            } else {
                echo '<li><a href="login.php"><img src="../resources/login.png" style="width: 35px; height: 35px;"></a></li>';
            }
            ?>
            
            </ul>
        </nav>
    </div>
</header>
<!--ASIDE-->
<aside>
    <nav>
    <ul>
        <li><a href="main.php"><img src="../resources/home.png" style="width: 38px; height: 38px; margin-left: 6px; margin-top: 100px;"></a></li>
        <?php

    if($_SESSION["hierarchy"] == 1){
    echo '<li><a href="adm.php"><img src="../resources/check.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 6px;"></a></li>';
   
    } else {
    echo '<li><a href="changeinfo.php"><img src="../resources/settingss.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 5px;"></a></li>';
    }
?>
        <li><a href="create_ad.php"><img src="../resources/add.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 5px;"></a></li>
        <li><a href="bag.php"><img src="../resources/bags.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 5px;"></a></li>
        <li><a href="wishlist.php"><img src="../resources/favourite.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 5px;"></a></li>
        <?php
        if(isset($_SESSION["user-name"])){
            echo '<li><a href="logout.php"><img src="../resources/logout.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 8px;"></a></li>';
        }
?>
        </ul>
    </nav>
</aside>
<div class="main-container">
<main>
    <div class="user-section">
    <a href="user.php" id="return"><img src="../resources/back.png" style="width: 20px; height: 20px; margin-top: 10px; margin-left: 20px;"></a>
    <form action="#" method="POST" enctype="multipart/form-data">
    <h2>User settings</h2>
    <?php 
    if ($profile_path) {
        echo '<img id="profilePreview" src="' . $profile_path . '" class="profile-pic" alt="Profile Picture">';
    } else {
    echo '<img id="profilePreview" src="../resources/user.jpg" class="profile-pic" alt="Profile Picture">';
    }
?>
    <label style="color: white">Profile pic: </label>
    <br>
    <input type="file" name="new-pic" accept="image/*" onchange="previewImage(event)">
    <button type="submit" class="search-button" name="pic-btn">
        <img src="../resources/up.png" alt="Search Icon" class="search-icon"></button>
    <br>
    <label style="color: white">Username: </label><br><input type="text" name="new-user" placeholder=<?php echo $username;?>>
    <button type="submit" class="search-button" name="user-btn">Submit</button>
    <br>
    <label style="color: white">E-mail: </label><br><input type="text" name="new-email" placeholder=<?php echo $email;?>>
    <button type="submit" class="search-button" name="email-btn">Submit</button>
    </form>
    </div>
    
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const profileImg = document.getElementById('profilePreview');
                profileImg.src = e.target.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>