
<?php
session_start();
require("0conn.php");

if (isset($_GET['meal_id'])) {
    $meal_id = $_GET['meal_id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    $stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $meal = $stmt->fetch(PDO::FETCH_ASSOC);

    $instructionsStmt = $pdo->prepare("SELECT * FROM instructions WHERE meal_id = ? ORDER BY step_number");
    $instructionsStmt->execute([$meal_id]);
    $instructions = $instructionsStmt->fetchAll(PDO::FETCH_ASSOC);

    $ingredientsStmt = $pdo->prepare("SELECT * FROM ingredients WHERE meal_id = ?");
    $ingredientsStmt->execute([$meal_id]);
    $ingredients = $ingredientsStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: 5admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_recipe'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM meals WHERE meal_id = ?");
    $deleteStmt->execute([$meal_id]);
    header("Location: 5admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_recipe'])) {
    header("Location: admineditpost.php?meal_id=$meal_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ededed;
            padding: 20px;
            margin: 0 auto;
            font-family: 'Arial', sans-serif;
            color: #18392B;
        }

        .logo-container {
            position: fixed;
            top: 0;
            left: 0; 
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #18392B;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
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
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1, h2, h3 {
            color: #4caf50;
        }

        img {
    display: block;
    margin: 0 auto;
    width: 1000px;
    height: auto; 
    margin-bottom: 20px;
    border-radius: 6px;
}
        ol, ul {
            margin-bottom: 15px;
        }

        a {
            color: #4caf50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: #18392B;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            margin-right: 10px;
            border-radius: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-to-dashboard {
            margin-top: 20px;
            color: #4caf50;
        }

        h3 {
            margin-top: 80px;
            color: #18392B;
            font-weight: bold;
            margin-left: 350px;
        }
        h4{
            font-weight: bold;
            color:  #4caf50;
        }
        .dashboard {
            margin-left: 1100px;
            align-items: center;
            justify-items: center;
            width: 100%;
           color: #4caf50;
        }
        .btn-secondary {
            background-color: #4caf50;
            color: #fff;
        }

        .dashboard{
           margin-left: 1100px;
        align-items: center;
        justify-items: center;
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


<h3>Meal Details</h3>
<div class="container">
    <h4> <?php echo $meal['meal_name']; ?></h4>
    <p>Video Link: <a href="<?php echo $meal['video_link']; ?>" target="_blank">Watch Video</a></p>
    <img src="<?php echo $meal['image_link']; ?>" alt="Recipe Image" style="max-width: 50%;">

    <h4>Instructions</h4>
    <ol>
        <?php foreach ($instructions as $instruction) { ?>
            <li><?php echo $instruction['step_description']; ?></li>
        <?php } ?>
    </ol>

    <h4>Ingredients</h4>
    <ul>
        <?php foreach ($ingredients as $ingredient) { ?>
            <li><?php echo $ingredient['ingredient_name']; ?></li>
        <?php } ?>
    </ul>

    <form method="post" action="" class="recipe-form">
    <button type="submit" name="edit_recipe" class="btn btn-primary">Edit</button>
    <button type="submit" name="delete_recipe" onclick="return confirm('Are you sure you want to delete this recipe?')" class="btn btn-danger">Delete</button>
</form>

    
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

