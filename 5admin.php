<?php
session_start();
require("0conn.php");

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("Location: 3login.php");
    exit();
}

$loggedInUsername = isset($_SESSION["username"]) ? $_SESSION["username"] : "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_categories"]) && is_array($_POST["selected_categories"])) {
        $selectedCategories = $_POST["selected_categories"];
        foreach ($selectedCategories as $categoryId) {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
            $stmt->execute([$categoryId]);
        }
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category_name"])) {
    $category_name = $_POST["category_name"];
    $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->execute([$category_name]);
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            color: #FFF;
        }

        .add-category-form {
            margin-bottom: 20px;
        }

        .list-group {
            text-align: left;
        }

        li {
            font-size: 18px;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #007BFF;
        }

        .delete-button {
            color: #fff;
            background-color: #dc3545;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        .btn-margin {
            margin-top: 20px;
        }

        h2{
           
            padding: 20px;
        }
        h3{
            color: #000;
            padding: 10px;
            margin-left: 20px;
        }
       .add{
        margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 20px;
            margin-top: 5px;
       }
       .categories{
        margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 20px;
            margin-top: 5px;
       }
      
       h3{
        margin-top:70px;
        Color: #18392B;
        font-weight: bold;
        
       }
      .manage{
         margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 20px;
            margin-top: 5px;
      }
       .userprofile{
        text-align: right;
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
   
    </div>
    

    <div class="container">
    <h3>Welcome, Admin!</h3>
    <div class = "userprofile">
    <p><a href="adminprofile.php" class="btn btn-secondary">Admin Profile</a></p>
    </div>
    <div class = "add">
        <p>Add Category</p>
        <form method="post">
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="category_name" placeholder="Category Name" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit">Add Category</button>
                </div>
            </div>
        </form>
        </div>

        <div class = "categories">
        <p>Categories</p>
        <form method="post" id="deleteForm">
            <ul class="list-group">
                <?php foreach ($categories as $category): ?>
                    <li class="list-group-item">
                        <input type="checkbox" id="category_<?php echo $category['category_id']; ?>" name="selected_categories[]" value="<?php echo $category['category_id']; ?>">
                        <span>
                            <a href="8category_page.php?category_id=<?php echo $category['category_id']; ?>">
                                <?php echo $category['category_name']; ?>
                            </a>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button type="submit" name="delete_selected" class="btn btn-danger mt-3" onclick="deleteSelectedCategories()">Delete Selected</button>
        </form>
        </div>

        <div class = "manage">
        <p>Manage Recipes</p>
        <p><a href="6add_recipe.php" class="btn btn-success">Add New Recipe</a></p>
        </div>

        <p><a href="4logout.php" class="btn btn-secondary">Logout</a></p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function deleteSelectedCategories() {
            const form = document.getElementById("deleteForm");
            const checkboxes = form.querySelectorAll('input[name="selected_categories[]"]');
            const selectedCategories = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            if (selectedCategories.length === 0) {
                alert("Please select categories to delete.");
                return;
            }

            if (confirm("Are you sure you want to delete the selected categories?")) {
                form.submit();
            }
        }
    </script>
</body>

</html>
