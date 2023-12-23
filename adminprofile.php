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
    header("Location: 1login.php");
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
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ededed;
            padding: 20px;
            margin: 0 auto;
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

        .dashboard {
            margin-left: 1100px;
            align-items: center;
            justify-items: center;
            width: 100%;
        }

        h3 {
            margin-top: 70px;
            color: #18392B;
            font-weight: bold;
            margin-left: 350px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }

        img {
            max-width: 110%;
            height: auto;
            border-radius: 8px;
            justify-items: center;
            
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: none;
            color: #0056b3;
        }

        .back-to-dashboard {
            margin-top: 20px;
        }

        .btn-secondary {
            background-color: #4caf50;
            color: #fff;
        }

        li, h2 {
            color: #4caf50;
        }
        p{
            color: #3e3e36;
            font-size: 15px;
        }

        .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .image-container img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
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
        <p><a href="5admin.php" class="btn btn-secondary">Admin Dashboard</a></p>
    </div>
</div>

<h3>Admin Profile</h3>
<div class="container">
    <ul>
        <?php foreach ($userRecipes as $recipe) { ?>
            <li>
            <h2><?php echo $recipe['meal_name']; ?></h2>
                <p>Category: <?php echo getCategoryName($pdo, $recipe['category_id']); ?></p>
                <p>Video Link: <a href="<?php echo $recipe['video_link']; ?>" target="_blank">Watch Video</a></p>
                <div class="image-container">
                    <p> <img src="<?php echo $recipe['image_link']; ?>" alt="Recipe Image"></p>
                </div>
                <div class = "date"><p>Date Created: <?php echo $recipe['date_created']; ?></p></div>
                <p><a href="adminposts.php?meal_id=<?php echo $recipe['meal_id']; ?>">View Details</a></p>
            </li>
        <?php } ?>
    </ul>

    <p><a href="4logout.php" class="btn btn-secondary">Logout</a></p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
function getCategoryName($pdo, $category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    return $category ? $category['category_name'] : 'Unknown';
}
?>

