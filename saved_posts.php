<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit;
}

include('db.php');

// Fetch saved posts
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id IN (SELECT recipe_id FROM saved_posts WHERE user_id = ?)");
$stmt->execute([$_SESSION['user_id']]);
$saved_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle remove from saved posts
if (isset($_POST['remove_save'])) {
    $recipe_id = $_POST['recipe_id'];

    // Remove the recipe from saved posts
    $stmt = $pdo->prepare("DELETE FROM saved_posts WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    header('Location: saved_posts.php');  // Redirect after removing the saved post
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Posts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <nav>
            <ul>
                <li><a href="user_dashboard.php">Dashboard</a></li>
                <li><a href="saved_posts.php">Saved Posts</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Saved Recipes Section -->
    <h2>Your Saved Recipes</h2>

    <?php if (isset($saved_recipes) && !empty($saved_recipes)): ?>
        <?php foreach ($saved_recipes as $recipe): ?>
            <div class="recipe">
                <h4><?php echo htmlspecialchars($recipe['name']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
                <img src="images/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Recipe Image" width="200">

                <!-- Remove from Saved Posts Button -->
                <form action="saved_posts.php" method="POST">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    <button type="submit" name="remove_save">Remove from Saved</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You haven't saved any posts yet.</p>
    <?php endif; ?>

</body>
</html>
