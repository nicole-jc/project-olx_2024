<?php
include('config.php');
session_start(); 

if($_SESSION["hierarchy"] == 2){
    header("Location: main.php");
    exit;
}

$user_logged = isset($_SESSION["user-id"]) ? intval($_SESSION["user-id"]) : 0;
$user_logged = isset($_SESSION["user-id"]) ? intval($_SESSION["user-id"]) : 0;
$query_profile_pic = $con->prepare("SELECT pic_id, profile_path FROM profilepic WHERE user_id = ?");
    $query_profile_pic->bind_param("i", $user_logged);
    $query_profile_pic->execute();
    $result_pic = $query_profile_pic->get_result();
        
        if ($result_pic->num_rows > 0) {
            $user_pic = $result_pic->fetch_assoc();
            $profile_path = $user_pic['profile_path'];
        } else {
            // if profile is not set
            $profile_path = "nothing yet.";
        }

?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>myMelody</title>
    <link rel="icon" href="../resources/melody.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <style>
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
<div class="adsection">
    <h4>Pending ads</h4>
    <div class="ad-items">
    <?php
        
        $query_search = "SELECT * FROM ads WHERE status = 2";
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
                echo '<a href="pending.php?id=' . $row_ad['ad_id'] . '" class="ad-link">';
                echo '<div class="ad-preview">';
                echo '<img src="' . $row_ad['ad_image_path'] . '" alt="' . $row_ad['adtitle'] . '">';
                echo '<div class="dropdown-content">';
                echo '<img src="' . $row_ad['ad_image_path'] . '" alt="' . $row_ad['adtitle'] . '">';
                echo '<div class="desc"><strong>' . $row_ad['adtitle'] . '</strong></div>';
                echo '</div>';
                echo '<h4>' . $row_ad['adtitle'] . '</h4>';
                echo '<p><strong>R$:</strong>' . $row_ad['adprice'] . '</p>'; 
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