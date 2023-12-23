<?php
session_start();
require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
if (isset($_GET["category_id"])) {
    $category_id = $_GET["category_id"];

    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $category_name = $category["category_name"];
    } else {
      
        $category_name = "Category Not Found";
    }
} else {
    
    $category_name = "Category Not Selected";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #18392B;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 60px;
            padding: 20px;
            width: auto;
            margin-right: 10px;
        }

        .logo h1 {
            font-family: cursive;
            font-size: 24px;
            margin: 0;
            color: #fff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-top: 70px;
        }

        h2, h3, p {
            margin: 10px 0;
            font-weight: bold;
        }

        .dashboard {
            margin-left: 20px;
            align-items: center;
            justify-items: center;
        }

        .btn-secondary {
            background-color: #4caf50;
            color: #fff;
        }

        .recipe-list {
            margin-top: 20px;
            font-size: 20px;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #4caf50;
            color: #FFF;
            font-weight: bold;
            text-align: center;
        }

        .list-group {
            background-color: #f8f9fa; 
        }

        .list-group-item {
            border-radius: 5px;
            margin-bottom: 10px;
            color: #000;
            background-color:  rgba(76, 175, 80, 0.2); 
            border: 2px solid #4caf50\; 
    

        }
        .category-details{

            font-size: 70;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="logo">
            <img src="logo.png" alt="Tastebud Logo">
            <h1>Tastebud</h1>
        </div>
        <div class="dashboard">
            <a href="5admin.php" class="btn btn-secondary">Admin Dashboard</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card-header">
            <h2 class="mb-3"> <?php echo $category_name; ?></h2>
        </div>
       

        <div class="recipe-list">
            <p class="mb-3">Recipes</p>
            
            <?php
            $stmt = $pdo->prepare("SELECT * FROM meals WHERE category_id = ?");
            $stmt->execute([$category_id]);
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($recipes) > 0) {
                echo '<div class="list-group">';
                foreach ($recipes as $recipe) {
                    echo '<a href="7recipe_details.php?recipe_id=' . $recipe['meal_id'] . '" class="list-group-item list-group-item-action">' . $recipe['meal_name'] . '</a>';
                }
                echo '</div>';
            } else {
                echo "<p>No recipes found in this category.</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
