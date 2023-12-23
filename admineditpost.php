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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1, h3 {
            color: #007bff;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            color: #007bff;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
        }
        h1{
            color: #18392B;
        }
        .mt-4{
            color: #18392B;
        }
        .logo-container {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
             justify-content: center; 
             align-items: center;
            background-color: #18392B; /* Green background */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            color: #fff; /* White text */
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
    <div class="container">
        <h1 class="mt-4 mb-4">Edit Meal</h1>

        <form method="post" action="admineditpostprocess.php">
            <input type="hidden" name="meal_id" value="<?php echo $meal_id; ?>">

            <div class="form-group">
                <label for="meal_name">Meal Name:</label>
                <input type="text" class="form-control" name="meal_name" value="<?php echo $meal['meal_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="video_link">Video Link:</label>
                <input type="text" class="form-control" name="video_link" value="<?php echo $meal['video_link']; ?>" required>
            </div>

            <div class="form-group">
                <label for="image_link">Image Link:</label>
                <input type="text" class="form-control" name="image_link" value="<?php echo $meal['image_link']; ?>" required>
            </div>

            <h3 class="mt-4">Instructions</h3>
            <div class="form-group">
                <label for="all_steps">All Steps:</label>
                <textarea class="form-control" name="all_steps" rows="10"><?php
                    foreach ($instructions as $instruction) {
                        echo $instruction['step_description'] . "\n";
                    }
                ?></textarea>
            </div>

            <h3 class="mt-4">Ingredients</h3>
            <div class="form-group">
                <label for="all_ingredients">All Ingredients:</label>
                <textarea class="form-control" name="all_ingredients" rows="10"><?php
                    foreach ($ingredients as $ingredient) {
                        echo $ingredient['ingredient_name'] . "\n";
                    }
                ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" name="update_recipe">Update Recipe</button>
        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
