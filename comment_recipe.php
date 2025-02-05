<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recipe_id']) && isset($_POST['comment'])) {
    $recipe_id = $_POST['recipe_id'];
    $comment = $_POST['comment'];

    // Insert the comment into the database for the specific recipe
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $recipe_id, $comment]);
}

header('Location: user_dashboard.php');  // Redirect back to the user dashboard
exit();
?>
