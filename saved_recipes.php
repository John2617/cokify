<?php
session_start();

// Include database connection
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch saved recipes for the user
$stmt = $pdo->prepare("SELECT r.*, u.username AS chef_name FROM saved_recipes sr
                       JOIN recipes r ON sr.recipe_id = r.id
                       JOIN users u ON r.user_id = u.id
                       WHERE sr.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$saved_recipes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Recipes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Saved Recipes</h2>

    <!-- Display saved recipes -->
    <?php if (count($saved_recipes) > 0): ?>
        <?php foreach ($saved_recipes as $recipe): ?>
            <div class="recipe-card">
                <h3><?php echo htmlspecialchars($recipe['ingredients']); ?></h3>
                <p><strong>Chef:</strong> <?php echo htmlspecialchars($recipe['chef_name']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>

                <!-- Display the recipe image -->
                <?php if ($recipe['image']): ?>
                    <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="Recipe Image" style="max-width: 300px; max-height: 300px;">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You haven't saved any recipes yet.</p>
    <?php endif; ?>

</body>
</html>
