<?php
include('config.php');
session_start(); 

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

// Processa o clique no botão "Add to Wishlist"
if (isset($_POST['add-wish']) && $user_logged > 0) {
    // Obtém o ID do anúncio da URL, que deveria ser passado corretamente
    if (isset($_GET['id'])) {
        $ad_id = intval($_GET['id']);
        
        // Protege contra SQL Injection usando prepared statements
        $stmt = $con->prepare("INSERT INTO wishlist (user_id, ad_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_logged, $ad_id);

        if ($stmt->execute()) {
            echo '<script>alert("You added this item to your Whishlist!");</script>';
            header("Location: wishlist.php");
        } else {
            echo '<script>alert("Sorry, something went wrong: ' . $stmt->error . '");</script>';
        }

        $stmt->close();
    } else {
        echo '<script>alert("Ad ID not found.");</script>';
    }
}

if (isset($_POST['add-bag']) && $user_logged > 0){
    if (isset($_GET['id'])){
        $ad_id = intval($_GET['id']);

        $stmt = $con->prepare("INSERT INTO bag (user_id, ad_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_logged, $ad_id);

        if($stmt->execute()){
            echo '<script>alert("You added this item to your Bag!;</script>';
            header("Location: bag.php");
        }
         else {
            echo '<script>alert("Sorry, something went wrong: ' . $stmt->error . '");</script>';
        }
        $stmt->close();
    } else {
        echo '<script>alert("Ad ID not found.");</script>';
    }
}

// Verifica se o 'id' está presente na URL
if (isset($_GET['id'])) {
    $ad_id = intval($_GET['id']);
    
    // Prepare statement para evitar SQL injection
    $query_details = $con->prepare("SELECT owner_id, adtitle, adprice, description, adcategory, condit, ad_image_path, status FROM ads WHERE ad_id = ?");
    $query_details->bind_param("i", $ad_id);
    $query_details->execute();
    $result_details = $query_details->get_result();
    
    if ($ad_details = $result_details->fetch_assoc()) {
        $owner_id = $ad_details['owner_id'];
        $page_title = $ad_details['adtitle'];
        $price = $ad_details['adprice'];
        $ad_description = $ad_details['description'];
        $ad_category = $ad_details['adcategory'];
        $ad_condit = $ad_details['condit'];
        $upload = $ad_details['ad_image_path'];
        $status = $ad_details['status'];

        if($ad_condit = 1){
            $ad_condit = "New";
        } else {
            $ad_condit = "Used";
        }

        // Busca o nome do dono do anúncio
        $query_owner = $con->prepare("SELECT username FROM user WHERE userid = ?");
        $query_owner->bind_param("i", $owner_id);
        $query_owner->execute();
        $result_owner = $query_owner->get_result();
        
        if ($owner = $result_owner->fetch_assoc()) {
            $owner_name = $owner['username'];
        } else {
            $owner_name = "Usuário não encontrado";
        }

        // Busca o nome da categoria
        $query_category = $con->prepare("SELECT catname FROM categories WHERE categoryid = ?");
        $query_category->bind_param("i", $ad_category);
        $query_category->execute();
        $result_category = $query_category->get_result();

        if ($category = $result_category->fetch_assoc()) {
            $category_name = $category['catname'];
        } else {
            $category_name = "Category not found";
        }
    } else {
        $page_title = "Ad not found";
        $owner_name = null;
        $category_name = null;
    }
} else {
    $page_title = "ID not found";
    $owner_name = null;
    $category_name = null;
}

?>

<!DOCTYPE html>
<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
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
        <form action="" method="get" class="search-form">
        <?php
            if (isset($_GET['search'])) {
                $search = $_GET['search'];
                header("Location: main.php?search=" . $search); 
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
if(isset($_SESSION["user-name"])){
if($_SESSION["hierarchy"] == 1){
    echo '<li><a href="adm.php"><img src="../resources/check.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 6px;"></a></li>';
   
} else {
    echo '<li><a href="changeinfo.php"><img src="../resources/settingss.png" style="width: 38px; height: 38px; margin-top: 18px; margin-left: 5px;"></a></li>';
} 
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
<div class="ad-pagesection">
    <a href="main.php">
        <img src="../resources/back.png" style="width: 20px; height: 20px; margin-top: 10px; margin-left: 20px;">
    </a>
    <main>
        <div id="ad-container">
            <img id="ad-image" src="<?php echo htmlspecialchars($upload); ?>" alt="image">
            <div id="ad-details">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <p><strong>Price:</strong><br> R$ <?php echo htmlspecialchars($price); ?></p>
                <p><strong>Description:</strong><br> <?php echo htmlspecialchars($ad_description); ?></p>
                <p><strong>Condition:</strong><br> <?php echo htmlspecialchars($ad_condit); ?></p>
                <p><strong>Category:</strong><br> <?php echo htmlspecialchars($category_name); ?></p>
                <p><strong><a class="ad-link" href="profile.php?id=<?php echo $owner_id; ?>">Publish by:</a></strong> <?php echo htmlspecialchars($owner_name); ?></p>
            </div>
        </div>
        <form method="post" action="" name="form-ad" class="ad-form">
            <input type="hidden" name="ad-id" value="<?php echo htmlspecialchars($ad_id); ?>">
            <div class="form-buttons">
                <button type="submit" class="search-button" name="add-bag">
                    <img src="../resources/bags.png" alt="Add to Bag" class="search-icon">
                </button>
                <button type="submit" class="search-button" name="add-wish">
                    <img src="../resources/favourite.png" alt="Add to Wishlist" class="search-icon">
                </button>
            </div>
        </form>
    </main>
</div>
</body>