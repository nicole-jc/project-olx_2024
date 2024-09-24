<?php
include('config.php');
session_start(); 

if (!isset($_SESSION["user-name"])) {
    // if user is not logged in
    header("Location: login.php");
    exit;
}

$user_logged = isset($_SESSION["user-id"]) ? intval($_SESSION["user-id"]) : 0;

if (isset($_POST["delete-btn"]) && isset($_POST["ad_id"])) {
    $ad_id = intval($_POST["ad_id"]);

    if ($user_logged > 0) {
        // Verify ad
        $query_check_ad = $con->prepare("SELECT owner_id FROM ads WHERE ad_id = ?");
        $query_check_ad->bind_param("i", $ad_id);
        $query_check_ad->execute();
        $result_check_ad = $query_check_ad->get_result();

        if ($result_check_ad && $result_check_ad->num_rows > 0) {
            $ad_owner_id = $result_check_ad->fetch_assoc()['owner_id'];

            if ($ad_owner_id == $user_logged) {
                
                $query_delete_ad = $con->prepare("DELETE FROM ads WHERE ad_id = ?");
                $query_delete_ad->bind_param("i", $ad_id);
                if ($query_delete_ad->execute()) {
                    echo '<script>alert("successfully deleted!").</alert>';
                } else {
                    echo '<script>alert("Gone wrong!.")</script>';
                }
            }
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
    <title><?php echo htmlspecialchars($username);?>'s page</title>
    <link rel="icon" href="../resources/melody.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <style>
.user-display {
    background-color: #1e1e1e;
    padding: 30px;
    border-radius: 8px;
    width: 500px;
    margin: 50px auto;
    display: flex;
    flex-direction: column;
    align-items: center;
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
.label-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.label-row {
    display: flex;
    align-items: center;
    margin: 10px 0;
}

.label1, .label2 {
    margin-top: 10px;
    width: 300px;
    padding: 10px;
    border-radius: 5px;
    border: 2px solid #717171;
    background-color: #343434;
    color: #d5d5d5;
    font-size: 15px;
    margin-right: 10px;
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
    <form action="#" method="POST" enctype="multipart/form-data">
    <div class="user-display">
        <form action="#" method="get">
            <?php
   if (!empty($profile_path)) {
                    echo '<img src="' . $profile_path . '" class="profile-pic" alt="Profile Picture">';
    } else {
    echo '<img src="../resources/user.jpg" class="profile-pic" alt="Profile Picture">';
    }
?>
    <button type="button" class="search-button" onclick="window.location.href='changeinfo.php';">
            <img src="../resources/redirect.png" alt="Search Icon" class="search-icon">
            </button>
            </button>
    <div class="label-container">
        <div class="label-row">
            <label class="label1">Username: @<?php echo $username; ?></label>
            <button type="button" class="search-button" onclick="window.location.href='changeinfo.php';">
                <img src="../resources/redirect.png" alt="Search Icon" class="search-icon">
            </button>
        </div>
        
        <div class="label-row">
            <label class="label2">Email: <?php echo $email; ?></label>
            <button type="button" class="search-button" onclick="window.location.href='changeinfo.php';">
            <img src="../resources/redirect.png" alt="Search Icon" class="search-icon">
            </button>
        </div>
    </div>
    </form>
</div>
<div class="adsection">
    <h4>Your ads</h4>
    <div class="ad-items">
    <?php
        
        $query_search = "SELECT * FROM ads WHERE owner_id = '$user_logged'";
        $result_search = mysqli_query($con, $query_search);

        // Verify if is a search term
        if (isset($_GET['search'])) {
            
            $searching = mysqli_real_escape_string($con, $_GET['search']);
            // SQL consult to search by title only
            $sql_code = "SELECT * FROM ads WHERE adtitle LIKE '%$searching%'";
            $result_search = mysqli_query($con, $sql_code);
        }

        // Verify if there is ads
        if (mysqli_num_rows($result_search) > 0) {
            while ($row_ad = mysqli_fetch_assoc($result_search)) {
                $ad_id = $row_ad['ad_id'];
                echo '<a href="ad.php?id=' . $row_ad['ad_id'] . '" class="ad-link">';
                echo '<div class="ad-preview">';
                echo '<img src="' . $row_ad['ad_image_path'] . '" alt="' . $row_ad['adtitle'] . '">';
                echo '<div class="dropdown-content">';
                echo '<img src="' . $row_ad['ad_image_path'] . '" alt="' . $row_ad['adtitle'] . '">';
                echo '<div class="desc"><strong>' . $row_ad['adtitle'] . '</strong></div>';
                echo '</div>';
                echo '<h4>' . $row_ad['adtitle'] . '</h4>';
                echo '<p><strong>R$:</strong>' . $row_ad['adprice'] . '</p>'; 
                echo '<form method="post" action="" name="delete">';
                echo '<input type="hidden" name="ad_id" value="' . $ad_id . '">';
                echo '<button type="submit" class="search-button" name="delete-btn">
                <img src="../resources/delete.png" style="width: 20px;""></button>';
                echo '</form>';
                echo '</div></a>'; 
            }
        } else {
            echo '<p style="color: white;">Nothing here now .</p>';
        }
    ?>
</div>
</div>
    </main>
    </div>
</body>