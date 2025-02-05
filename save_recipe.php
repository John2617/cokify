<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recipe_id'])) {
    $recipe_id = $_POST['recipe_id'];

    // Check if the user has already saved the recipe
    $stmt = $pdo->prepare("SELECT * FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);

    if ($stmt->rowCount() == 0) {
        // Insert the saved recipe into the database
        $stmt = $pdo->prepare("INSERT INTO saved_recipes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    }
}

header('Location: saved_recipes.php');  // Redirect to the saved recipes page
exit();
?>
