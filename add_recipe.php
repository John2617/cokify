<?php
session_start();

// Include database connection
include('db.php');

// Check if the chef is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $recipe_name = $_POST['recipe_name'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $chef_id = $_SESSION['user_id'];
    
    // Upload Image
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_path = 'uploads/' . $image;

    if (move_uploaded_file($image_tmp_name, $image_path)) {
        // Insert recipe into the database
        $stmt = $pdo->prepare("INSERT INTO recipes (recipe_name, ingredients, instructions, chef_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$recipe_name, $ingredients, $instructions, $chef_id, $image_path]);

        header('Location: chef_dashboard.php'); // Redirect to the dashboard after successful addition
        exit();
    } else {
        echo "Error uploading image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Recipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Add New Recipe</h2>
    
    <form action="add_recipe.php" method="POST" enctype="multipart/form-data">
        <label for="recipe_name">Recipe Name:</label><br>
        <input type="text" name="recipe_name" required><br>

        <label for="ingredients">Ingredients:</label><br>
        <textarea name="ingredients" required></textarea><br>

        <label for="instructions">Instructions:</label><br>
        <textarea name="instructions" required></textarea><br>

        <label for="image">Recipe Image:</label><br>
        <input type="file" name="image" required><br>

        <button type="submit">Add Recipe</button>
    </form>

    <br><a href="chef_dashboard.php">Back to Dashboard</a>
</body>
</html>
        