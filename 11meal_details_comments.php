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

    $commentsStmt = $pdo->prepare("SELECT * FROM comments WHERE meal_id = ? ORDER BY created_at DESC");
    $commentsStmt->execute([$meal_id]);
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    header("Location: 9customer.php");
    exit();
}

$userLoggedIn = isset($_SESSION['username']);
$allowComments = $userLoggedIn; // Check if the user is logged in

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userLoggedIn) {
    // Check if the submitted form is for adding a new comment
    if (isset($_POST['comment'])) {
        $comment_text = $_POST['comment'];
        $insertStmt = $pdo->prepare("INSERT INTO comments (meal_id, user_name, comment_text) VALUES (?, ?, ?)");
        $insertStmt->execute([$meal_id, $_SESSION['username'], $comment_text]);
        header("Location: 11meal_details_comments.php?meal_id=$meal_id");
        exit();
    }

    // Check if the submitted form is for deleting a comment
    elseif (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['delete_comment'];

        // Fetch the comment to confirm the deletion
        $commentStmt = $pdo->prepare("SELECT * FROM comments WHERE comment_id = ?");
        $commentStmt->execute([$comment_id]);
        $commentToDelete = $commentStmt->fetch(PDO::FETCH_ASSOC);

        if ($commentToDelete && $_SESSION['username'] === $commentToDelete['user_name']) {
            // Display a confirmation dialog
            echo "<script>
                    let confirmDelete = confirm('Are you sure you want to delete your comment?');

                    if (confirmDelete) {
                        window.location.href = 'delete_comment.php?comment_id=$comment_id&meal_id=$meal_id';
                    }
                 </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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

        .container {
            width: 100%;
            margin-top: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: white;
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
        .meal-details-box {
            background-color: #fff;
            margin: 50px ;
            width: 100%;
            justify-content: center;
            text-align: center;
        }

        .comments-box {
            padding: 20px;
            margin: 20px ;
            width: 95%;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        img {
            width: 35%;
            height: 45vh;
        }

        ol, ul {
            margin-top: 10px;
        }

        li {
            margin-bottom: 5px;
        }

        a {
            color: #007BFF;
        }
        h1, h2, h3 {
        color: #04AA6D;
         }

        .comments-list {
            list-style-type: none;
            padding: 0;
            align-items: center;
        }

        .comment-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            text-align: left; 
        }

        .comment-text {
            margin-top: 5px;
        }

        .comment-info {
            font-size: 14px;
            color: #555;
        }


        form {
            margin-top: 20px;
            width: 100%;
            display: flex;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            background-color: #16b978;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-top: 10px; 
            margin-left: 10px;
        }

        .add-comment-link {
            color: #16b978;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 10px;
        }
        .button-secondary {
            margin-top: 40px;
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
        <div class="meal-details-box">
            <button class="button-secondary" onclick="window.location.href='15userposts.php'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h2><?php echo $meal['meal_name']; ?></h2>
            <img src="<?php echo $meal['image_link']; ?>" alt="Recipe Image"><br><br>
            <a href="<?php echo $meal['video_link']; ?>" target="_blank">Watch Video</a>
            <h3>Instructions</h3>
            <ol>
                <?php
                foreach ($instructions as $instruction) {
                    echo "<li>{$instruction['step_description']}</li>";
                }
                ?>
            </ol>

            <h3>Ingredients</h3>
            <ul>
                <?php
                foreach ($ingredients as $ingredient) {
                    echo "<li>{$ingredient['ingredient_name']}</li>";
                }
                ?>
            </ul>
        </div>
        <h3>Comments</h3>
    <div class="comments-box">
        <ul class="comments-list">
            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                    <li class="comment-item">
                        <p class="comment-text"><strong><?php echo $comment['user_name']; ?>:</strong> <?php echo $comment['comment_text']; ?></p>
                        <p class="comment-info"><?php echo $comment['created_at']; ?></p>

                        <!-- Add delete comment form -->
                        <?php if ($userLoggedIn && $_SESSION['username'] === $comment['user_name']): ?>
                            <form method="post" action="">
                                <input type="hidden" name="delete_comment" value="<?php echo $comment['comment_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments available.</p>
            <?php endif; ?>

            <!-- Your comment form goes here -->
            <?php if ($allowComments): ?>
                <form method="post" action="">
                    <textarea name="comment" placeholder="Write a comment..." id="comment" rows="3" required></textarea>
                    <button type="submit">Submit</button>
                </form>
            <?php else: ?>
                <p>Login to post comments.</p>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
