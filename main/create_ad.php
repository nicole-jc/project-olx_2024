<?php
include('config.php');
session_start();

if (!isset($_SESSION["user-name"])) {
    // if user is not logged in
    header("Location: login.php");
    exit;
}
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
            $profile_path = "../resources/anony.png";
        }
    

if (isset($_POST['ad-button'])) {
    if (isset($_POST['ad-title'], $_POST['ad-price'], $_POST['ad-description'], $_POST['condition'], $_POST['category'])) {
        $ownerid = $_SESSION["user-id"];
        $title = $_POST['ad-title'];
        $price = $_POST['ad-price'];
        $description = $_POST['ad-description'];
        $condition = $_POST['condition'];
        $category = $_POST['category'];

        if (isset($_FILES['imageupload'])) {
            $file = $_FILES['imageupload'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $folder = "../upload/";
                $namefile = $file['name'];
                $newfilename = uniqid();
                $extension = strtolower(pathinfo($namefile, PATHINFO_EXTENSION));
                $destinypath = $folder . $newfilename . '.' . $extension;

                if (move_uploaded_file($file['tmp_name'], $destinypath)) {
                    $query_create_ad = "INSERT INTO ads (owner_id, adtitle, adprice, description, adcategory, condit, ad_image_path, status) VALUES ('$ownerid', '$title', '$price', '$description', '$category', '$condition', '$destinypath', '2')";
                    $result_ad = mysqli_query($con, $query_create_ad);

                    if ($result_ad) {
                        echo '<script>alert("Ad created successfully!");</script>';
                    } else {
                        echo '<script>alert("Error: ' . mysqli_error($con) . '");</script>'; 
                    }
                } else {
                    echo '<script>alert("Error uploading file.");</script>';
                }
            } else {
                echo '<script>alert("File upload error: ' . $file['error'] . '");</script>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>myPink: create an ad</title>
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
    <script language="JavaScript">
function goToUrl(selObj, goToLocation){
    eval("document.location.href = '" + goToLocation + "&category=" + selObj.options[selObj.selectedIndex].value + "'");
}
</script>
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
        <div class="create-section">
    <a href="main.php">
        <img src="../resources/back.png" style="width: 20px; height: 20px; margin-top: 10px; margin-left: 20px;">
    </a>
    <form action="#" method="post" enctype="multipart/form-data" name="form-create">
        <div class="form-container">
            <div class="left-column">
                <label style="color: white;">Title:</label><br>
                <input type="text" name="ad-title" placeholder="Title..." required><br>
                <label style="color: white;">Price:</label><br>
                <input type="text" name="ad-price" placeholder="Price..." required><br>
                <label style="color: white;">Description:</label><br>
                <textarea name="ad-description" rows="4" cols="30" placeholder="Description..."></textarea>
            </div>
            <div class="center-column">
                <label style="color: white;">Condition:</label><br>
                <select name="condition">
                    <option value="">Select &#x2764;</option>
                    <option value="1">New</option>
                    <option value="2">Used</option>
                </select><br>
                <label style="color: white;">Category:</label><br>
                <select name="category" onChange="goToUrl(this,'create_ad.php?pag=create_ad')">
                    <?php
                        $query = "SELECT categoryid, catname FROM categories ORDER BY catname ASC";
                        $result = mysqli_query($con, $query);
                    ?>
                    <option value="">Select &#x2764;</option>
                    <?php
                        $category = isset($_GET['category']) ? $_GET['category'] : '';
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <option value="<?php echo $row['categoryid']; ?>" <?php echo $row['categoryid'] == $category ? "selected" : ""; ?>>
                                <?php echo $row['catname']; ?>
                            </option>
                            <?php
                        }
                    ?>
                </select><br>
                <label for="image" style="color: white;">Upload an image:</label><br>
                <input type="file" name="imageupload" accept="image/*"><br>
                <input type="submit" name="ad-button" value="Submit">
            </div>
        </div>
    </form>
    <div class="right-image-container">
        <img src="../resources/melody.png" class="right-image">
    </div>
</div>

</body>
</html>
