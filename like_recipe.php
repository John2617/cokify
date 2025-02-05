<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle the like toggle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recipe_id'])) {
    $recipe_id = $_POST['recipe_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already liked the recipe
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $existing_like = $stmt->fetch();

    if ($existing_like) {
        // If the like exists, remove it (unlike)
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
    } else {
        // If no like exists, add a like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $recipe_id]);
    }
}

header('Location: user_dashboard.php');  // Redirect back to the user dashboard
exit();
