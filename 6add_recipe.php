<?php
session_start();

require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    echo "You must login as an admin to access this page.";
    header("Refresh: 3; Location: 5admin.php");
    exit();
}

$recipe_preview = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION["username"];
    

    if (
        isset($_POST["recipe_name"]) &&
        isset($_POST["category_id"]) &&
        isset($_POST["video_link"]) &&
        isset($_POST["instructions"]) &&
        isset($_POST["ingredients"]) &&
        isset($_POST["image_link"])
    ) {
        // Check if the username exists in the users table
        $userCheckStmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $userCheckStmt->execute([$username]);
        $userExists = $userCheckStmt->fetch();

        if ($userExists) {
            // Insert the new meal
            $recipe_name = $_POST["recipe_name"];
            $category_id = $_POST["category_id"];
            $video_link = $_POST["video_link"];
            $image_link = $_POST["image_link"];

            $stmt = $pdo->prepare("INSERT INTO meals (meal_name, category_id, video_link, image_link, date_created, username) VALUES (?, ?, ?, ?, NOW(), ?)");
            $stmt->execute([$recipe_name, $category_id, $video_link, $image_link, $username]);

            $meal_id = $pdo->lastInsertId();

            $instructions = explode("\n", $_POST["instructions"]);
            foreach ($instructions as $step_number => $step_description) {
                $stmt = $pdo->prepare("INSERT INTO instructions (meal_id, step_number, step_description) VALUES (?, ?, ?)");
                $stmt->execute([$meal_id, $step_number + 1, trim($step_description)]);
            }

            // Insert ingredients
            $ingredients = explode("\n", $_POST["ingredients"]);
            foreach ($ingredients as $ingredient_name) {
                $stmt = $pdo->prepare("INSERT INTO ingredients (meal_id, ingredient_name) VALUES (?, ?)");
                $stmt->execute([$meal_id, trim($ingredient_name)]);
            }

            // Rest of your code...
        } else {
            // Handle the case when the username doesn't exist in the users table
            echo "Error: User does not exist.";
        }
    }
}

function generateRecipePreview($pdo, $meal_id) {
    $stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $recipe = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM instructions WHERE meal_id = ? ORDER BY step_number");
    $stmt->execute([$meal_id]);
    $instructions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $preview = "<h2>Recipe Preview</h2>";
    $preview .= "<h3>{$recipe['meal_name']}</h3>";
    $preview .= "<p>Video Link: {$recipe['video_link']}</p>";
    $preview .= "<p>Image Link: {$recipe['image_link']}</p>";
    $preview .= "<p>Category: {$recipe['category_id']}</p>";
    
    $preview .= "<img id='recipe-image' src='' alt='Recipe Image' style='max-width: 100%; display: none;'>";

    $preview .= "<h3>Instructions</h3>";
    $preview .= "<ol>";
    foreach ($instructions as $instruction) {
        $preview .= "<li>{$instruction['step_description']}</li>";
    }
    $preview .= "</ol>";

    $preview .= "<h3>Ingredients</h3>";
    $preview .= "<ul>";
    foreach ($ingredients as $ingredient) {
        $preview .= "<li>{$ingredient['ingredient_name']}</li>";
    }
    $preview .= "</ul>";

    return $preview;
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Add Recipe</title>
    <style>
        #form-section {
            display: block;
        }

        #preview-section {
            display: none;
        }

        #buttons {
            text-align: center;
            margin-top: 20px;
        }
        
    </style>
    <script>
        function togglePreview() {
            var formSection = document.getElementById("form-section");
            var previewSection = document.getElementById("preview-section");
            var previewButton = document.getElementById("preview-button");
            var addButton = document.getElementById("add-button");
            var editButton = document.getElementById("edit-button");

            if (formSection.style.display === "block") {
                formSection.style.display = "none";
                previewSection.style.display = "block";
                previewButton.innerText = "Edit";
                addButton.style.display = "none";
                editButton.style.display = "inline";
                toggleReadOnly(true);
                displayReadonlyInputs();
                displayImage();
            } else {
                formSection.style.display = "block";
                previewSection.style.display = "none";
                previewButton.innerText = "Preview";
                addButton.style.display = "inline";
                editButton.style.display = "none";
                toggleReadOnly(false);
            }
        }

        function toggleReadOnly(readonly) {
            var inputs = document.getElementsByTagName("input");
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].readOnly = readonly;
            }
            var selects = document.getElementsByTagName("select");
            for (var i = 0; i < selects.length; i++) {
                selects[i].disabled = readonly;
            }
            var textareas = document.getElementsByTagName("textarea");
            for (var i = 0; i < textareas.length; i++) {
                textareas[i].readOnly = readonly;
            }
        }

        function displayReadonlyInputs() {
            var readonlyInputs = document.getElementsByClassName("readonly-input");
            var inputs = document.getElementsByTagName("input");
            for (var i = 0; i < inputs.length; i++) {
                var value = inputs[i].value;
                readonlyInputs[i].innerText = value;
            }
            var selects = document.getElementsByTagName("select");
            for (var i = 0; i < selects.length; i++) {
                var value = selects[i].options[selects[i].selectedIndex].text;
                readonlyInputs[inputs.length + i].innerText = value;
            }
            var textareas = document.getElementsByTagName("textarea");
            for (var i = 0; i < textareas.length; i++) {
                var value = textareas[i].value;
                readonlyInputs[inputs.length + selects.length + i].innerText = value;
            }
        }

        function displayImage() {
            const imageLink = document.querySelector(".image-link").textContent;
            const recipeImage = document.getElementById("recipe-image");
            if (imageLink.trim() !== "") {
                recipeImage.src = imageLink;
                recipeImage.style.display = "block";
            }
        }
        
        function showPopupMessage(message) {
            var popup = document.getElementById("popup");
            var popupMessage = document.getElementById("popup-message");
            popupMessage.innerText = message;
            popup.style.display = "block";
            setTimeout(function () {
                popup.style.display = "none";
            }, 5000);
        }
    </script>
</head>
<body>

<style>

body {
           
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('foodBackground.jpeg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(15px);
            z-index: -1;
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

        h3 {
            /* margin-top: 70px; */
            color: #fff; /* White text */
            font-weight: bold;
            padding: 20px;
        }

        .recipe-name {
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 20px;
            margin-top: 5px;
        }

        .container2 {
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 20px;
            margin-top: 5px;

        }

        h3 {
            color:  #4caf50; 
            font-weight: bold;
        }

        .btn-secondary {
            background-color: #4caf50; /* Green background */
            color: #fff; /* White text */
        }

        #form-section,
        #preview-section {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff; /* White background */
            border-radius: 10px;
            /* box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); */
            margin-top: 30px; 
        }

        #buttons {
            text-align: center;
            margin-top: 20px;
        }

        #preview-button,
        #edit-button {
            background-color: #badabf; 
            color: #388e3c; /* White text */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            outline: #4caf50;
        }

        #add-button {
            background-color: #388e3c; /* Darker green for Add Recipe button */
            color: #fff; 
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            outline: #4caf50;      }
       
        .container{
            margin-top: 80px;
        } 
        .dashboard{
           margin-left: 1100px;
        align-items: center;
        justify-items: center;
           
        }
</style>
<div class="logo-container">
        <div class="logo">
            <img src="logo.png" alt="Tastebud Logo">
            <h1>Tastebud</h1>
        </div>
        <div class="dashboard">
    <a href="5admin.php" class="btn btn-secondary">Admin Dashboard</a>
</div>
    </div>

    <div class = "container" >
    
    <div id="form-section">
    <h3>Add New Recipe</h3>
        <form method="post" onsubmit="showPopupMessage('Meal added successfully');">
            <div class = "recipe-name">
                <label for="recipe_name">Recipe Name:</label>
                <input type="text" name="recipe_name" id="recipe_name"  class = "form-control" required>
            </div>
          
            <div class = "container2">
            <div class="form-group">
    <label for="category_id">Category:</label>
    <select name="category_id" id="category_id" class="form-control" required>
        <?php
             $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category) {
            echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
             }
                 ?>
            </select>
        </div>

        <div class= "form-group">
            <div>
                <label for="video_link">Video Link:</label>
                <input type="text" name="video_link" id="video_link" class = "form-control" required>
            </div>
            <div>
                <label for="instructions">Instructions (one step per line):</label>
                <textarea name="instructions" id="instructions" rows="5" class = "form-control" required></textarea>
            </div>
            <div>
                <label for="ingredients">Ingredients (one ingredient per line):</label>
                <textarea name="ingredients" id="ingredients" rows="5"  class = "form-control"required></textarea>
            </div>
            <div>
                <label for="image_link">Image Link:</label>
                <input type="text" name="image_link" id="image_link" class = "form-control" required>
            </div>
            <div class="text-center" id="buttons">
    <button id="preview-button" type="button" class="btn btn-primary" onclick="togglePreview()">Preview</button>
    <button id="add-button" type="submit" class="btn btn-success">Add Recipe</button>
    <button id="edit-button" type="button" class="btn btn-warning" style="display: none;">Edit</button>
</div>

            </div>
        </form>
    </div>
    </div>
    </div>
    
    <div id="popup" style="display: none;">
        <p id="popup-message" style="background-color: #4CAF50; color: white; text-align: center; padding: 10px;"></p>
    </div>
    <div id="preview-section">
        <div id="readonly-section">
            <h3>Recipe Name: <span class="readonly-input"></span></h3>
            <p>Video Link: <span class="readonly-input"></span></p>
            <p>Image Link: <span class="readonly-input image-link"></span></p>
            <p>Category: <span class="readonly-input"></span></p>
            <img id="recipe-image" src="" alt="Recipe Image" style="max-width: 100%; display: none;">
            <h3>Instructions</h3>
            <ol class="readonly-input">
            </ol>
            <h3>Ingredients</h3>
            <ul class="readonly-input"></ul>
        </div>
        <div id="buttons">
            <button id="preview-button" type="button" onclick="togglePreview()">Edit</button>
            <button id="add-button" type="submit">Add Recipe</button>
        </div>
    </div>


   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
