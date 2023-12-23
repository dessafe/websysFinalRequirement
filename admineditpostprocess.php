<?php
session_start();
require("0conn.php");

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_recipe'])) {
    $meal_id = $_POST['meal_id'];

    $meal_name = $_POST['meal_name'];
    $video_link = $_POST['video_link'];
    $image_link = $_POST['image_link'];
    $all_steps = $_POST['all_steps'];
    $all_ingredients = $_POST['all_ingredients'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $updateMealStmt = $pdo->prepare("UPDATE meals SET meal_name = ?, video_link = ?, image_link = ? WHERE meal_id = ?");
        $updateMealStmt->execute([$meal_name, $video_link, $image_link, $meal_id]);

        $deleteInstructionsStmt = $pdo->prepare("DELETE FROM instructions WHERE meal_id = ?");
        $deleteInstructionsStmt->execute([$meal_id]);
    
        // Insert updated instructions
        $insertInstructionsStmt = $pdo->prepare("INSERT INTO instructions (meal_id, step_number, step_description) VALUES (?, ?, ?)");
        $instructionsArray = explode("\n", $all_steps);
        foreach ($instructionsArray as $stepNumber => $step) {
            $insertInstructionsStmt->execute([$meal_id, $stepNumber + 1, trim($step)]);
        }
    
        // Delete existing ingredients for the meal
        $deleteIngredientsStmt = $pdo->prepare("DELETE FROM ingredients WHERE meal_id = ?");
        $deleteIngredientsStmt->execute([$meal_id]);
    
        // Insert updated ingredients
        $insertIngredientsStmt = $pdo->prepare("INSERT INTO ingredients (meal_id, ingredient_name) VALUES (?, ?)");
        $ingredientsArray = explode("\n", $all_ingredients);
        foreach ($ingredientsArray as $ingredientId => $ingredient) {
            $insertIngredientsStmt->execute([$meal_id, trim($ingredient)]);
        }
        header("Location: admineditpost.php?meal_id=$meal_id");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: 5admin.php");
    exit();
}
?>
