<?php
session_start();
require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (!isset($_SESSION['username'])) {
    header("Location: 3login.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT * FROM meals WHERE username = ? ORDER BY date_created DESC");
$stmt->execute([$username]);
$userRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <title>User Profile</title>
    <style>

        body {
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            display: flex;
            flex-wrap: wrap;
        }


        .topnav {
            background-color: #16b978;
            overflow: hidden;
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            padding-top: 90px;
            transition: top 0.3s;
        }

        .topnav a {
            float: center;
            color: #f2f2f2;
            text-align: center;
            padding: 15px 25px;
            text-decoration: none;
            font-size: 17px;
            display: flex;
            align-items: center;
        }

        .topnav a:hover {
            background-color: #ddd;
            color: black;
        }

        .topnav a.active {
            background-color: #04AA6D;
            color: white;
        }
        .topnav a i {
            margin-right: 30px;
        }
        .add-recipe-container {     
            background-color: white;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 10px; 
            border-bottom: solid 3px whitesmoke;
            margin-top: 40px;
            align-items: left;
            justify-content: left;
        }
        .add-recipe-button {
            color: black;
            padding: 10px 50px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 20px;
            margin-left: 10px;
            transition: background-color 0.3s;
            margin-right: 20px;
        }

        .add-recipe-button:hover {
            background-color: whitesmoke;
            color: black;
            
        }
        .add-recipe-button i {
            margin-right: 10px;
        }
        h3{
            margin-left:20px;
            margin-top: 30px;
            margin-bottom: 30px;;
            color: #16b978; margin-right: 20px;
        }
      
        .container-1 {
            flex-grow: 1;
            width: 100%;
            background-color: #fff;
            position: static;
            overflow: auto;
        }

        .recipe-box {
            background-color: #fff;
            box-sizing: border-box;
            float: left;
            border-bottom: 3px solid whitesmoke;
            background: white;
            margin-bottom: 2px;
            width: calc(70% - 80px); 
            margin-left: 250px;
            
        }
        .recipe-box img {
            width: 100%; 
            height: 400px; 
            object-fit: cover; 
            border-radius: 10px; 
        }
        .recipe-list {
            list-style: none;
            padding: 0;
            margin: 0;
            justify-content: space-between;
            margin-left: 300px; 
        }
        h1 {
            color: #16b978;
            font-size: 30px;
            margin-left: 300px;
            margin-top: 110px;
            margin-bottom: 20px;
            }
    
        h2{
            margin-left:310px;
            color: black;
        }
        h3{
            color: black;
            margin-left: 2px;
        }


        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .logo-container {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            padding: 20px;
            width: auto;
            margin-right: 10px;
        }

        .logo h1 {
            font-family: cursive;
            font-size: 24px;
            margin: 0;
            color: #16b978;
        }

        .sidebar {
            margin-top: 140px;
            background-color: white;

            padding-left: 15px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            float: left;
            position: fixed;
            height: 100vh;
        }

    
        .recipe-box .view-details-button {
            display: inline-block;
            padding: 8px 16px;
            background-color: #04AA6D;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
        }

        .recipe-box .view-details-button:hover {
            background-color: #128a61;
        }
         a {
            color: #007BFF;
        }
       
       

    </style>
</head>
<body>

<div class="logo-container">
    <div class="logo">
        <img src="logo.png" alt="Tastebud Logo">
        <h1>Tastebud</h1>
    </div>
</div>

<div class="topnav">
        <a href="9customer.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="view_categories.php"><i class="fas fa-fw fa-user"></i>Categories</a>
        <a href="14chat.php"><i class="fa-solid fa-comment"></i>Chat</a>
        <a href="12user_profile.php"><i class="fas fa-fw fa-user"></i> Profile</a>
        <a href="4logout.php"><i class="fas fa-fw fa-sign-out"></i> Logout</a>
</div>

<div class="container-1">
<div class="sidebar">
<h3>Create your own Recipe</h3>
    <a href="13add_recipe.php" class="add-recipe-button">
    <i class="fa-solid fa-pen-to-square"></i>Create new</a>
</div>

<div class="add-recipe-container">
    <h1>Hi <?php echo $username; ?>!</h1>
    </div>


<div class="recipe-container">
    <h2>Timeline</h2>
    <ul class="recipe-list">
        <?php foreach ($userRecipes as $recipe) { ?>
            <li class="recipe-box">
                <h3><?php echo $recipe['meal_name']; ?></h3>
                <img src="<?php echo $recipe['image_link']; ?>" alt="Recipe Image"><br><br>
                <a href="<?php echo $recipe['video_link']; ?>" target="_blank">Video Tutorial</a>
                <p><a class="view-details-button" href="15userposts.php?meal_id=<?php echo $recipe['meal_id']; ?>">Show more</a></p>
                <p>Date: <?php echo $recipe['date_created']; ?></p>
            </li>
        <?php } ?>
    </ul>
</div>
</div>
</div>
</body>
</html>

<?php
function getCategoryName($pdo, $category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    return $category ? $category['category_name'] : 'Unknown';
}