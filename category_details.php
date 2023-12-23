<?php
require("0conn.php");

// Check if category_id is set in the URL
if (isset($_GET['category_id'])) {
    // Retrieve category_id from the URL
    $selectedCategoryId = $_GET['category_id'];

    // Fetch category details from the database based on category_id
    $sqlCategory = "SELECT * FROM categories WHERE category_id = $selectedCategoryId";
    $resultCategory = $conn->query($sqlCategory);

    // Check if the category is found
    if ($resultCategory && $resultCategory->num_rows > 0) {
        $categoryDetails = $resultCategory->fetch_assoc();

        // Fetch meals associated with the category
        $sqlMeals = "SELECT * FROM meals WHERE category_id = $selectedCategoryId";
        $resultMeals = $conn->query($sqlMeals);

        // Check if meals are found
        if ($resultMeals && $resultMeals->num_rows > 0) {
            $meals = $resultMeals->fetch_all(MYSQLI_ASSOC);
        } else {
            // Handle the case when no meals are found for the category
            $meals = [];
        }
    } else {
        // Handle the case when the category is not found
        $categoryDetails = null;
        $meals = [];
    }
} else {
    // Handle the case when category_id is not set in the URL
    $categoryDetails = null;
    $meals = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png">
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

        .button-primary {
            background-color: #16b978;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .button-primary:hover {
            background-color: #128a61;
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

        .container {
            width: 100%;
            min-height: 100vh;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-direction: column;
            align-items: center;
            text-align: center;
            overflow-y: auto;
        }

        h2 {
            font-size: 20px;
            margin-left: 60px;
            margin-top: 20px;
            margin-bottom: 20px;
            color: black;
        }

        h3 {
            color: #16b978;
            font-size: 23px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            margin: 10px 0;
        }

        .meals-container {
            border: 3px solid whitesmoke;
            padding: 10px;
            border-radius: 5px;
            margin-left: 120px;
            margin-right: 120px;
        }

        .meal-name {
            font-size: 18px; 
            display: block;
            margin: 10px 0; 
            color: #16b978;
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

        .button-secondary {
            margin-top: 160px;
            margin-left: 25px;
            color: gray;
            padding: 8px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            border: none;
            font-size: 20px;
            background-color: transparent;
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
    <div class="container">
    <button class="button-secondary" onclick="window.location.href='view_categories.php'">
            <i class="fas fa-arrow-left"></i> </button>
        <h2>Category Details</h2>
        <?php if ($categoryDetails): ?>
            <h3><?php echo $categoryDetails['category_name']; ?></h3>

            <div class="meals-container">
                <?php if (!empty($meals)): ?>
                    <?php foreach ($meals as $meal): ?>
                        <span class="meal-name">
                            <a href="meal_details.php?meal_id=<?php echo $meal['meal_id']; ?>">
                                <?php echo $meal['meal_name']; ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No meals found for this category.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p>Category not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>