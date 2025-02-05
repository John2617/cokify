<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit;
}

include('db.php');

// Fetch recipes
$stmt = $pdo->prepare("SELECT * FROM recipes");
$stmt->execute();
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle like
if (isset($_POST['like'])) {
    $recipe_id = $_POST['recipe_id'];
    // Check if the user has already liked the post
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    $like = $stmt->fetch();

    if ($like) {
        // User has already liked, so we remove the like
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    } else {
        // User hasn't liked, so we add the like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    }
}

// Handle comment
if (isset($_POST['comment'])) {
    $recipe_id = $_POST['recipe_id'];
    $comment_text = $_POST['comment_text'];

    // Insert comment into the database
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $recipe_id, $comment_text]);
}

// Handle save
if (isset($_POST['save'])) {
    $recipe_id = $_POST['recipe_id'];

    // Check if the recipe is already saved
    $stmt = $pdo->prepare("SELECT * FROM saved_posts WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    $saved = $stmt->fetch();

    if (!$saved) {
        // Add to saved posts
        $stmt = $pdo->prepare("INSERT INTO saved_posts (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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

    <!-- Welcome message -->
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

    <h3>Recipes</h3>

    <?php if (isset($recipes) && !empty($recipes)): ?>
        <?php foreach ($recipes as $recipe): ?>
            <div class="recipe">
                <h4><?php echo htmlspecialchars($recipe['name']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
                <img src="images/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Recipe Image" width="200">

                <!-- Like button -->
                <?php
                $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND recipe_id = ?");
                $stmt->execute([$_SESSION['user_id'], $recipe['id']]);
                $liked = $stmt->fetch();
                ?>
                <form action="user_dashboard.php" method="POST">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    <button type="submit" name="like">
                        <?php echo $liked ? 'Liked' : 'Like'; ?>
                    </button>
                </form>

                <!-- Save button -->
                <?php
                $stmt = $pdo->prepare("SELECT * FROM saved_posts WHERE user_id = ? AND recipe_id = ?");
                $stmt->execute([$_SESSION['user_id'], $recipe['id']]);
                $saved = $stmt->fetch();
                ?>
                <form action="user_dashboard.php" method="POST">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    <button type="submit" name="save">
                        <?php echo $saved ? 'Saved' : 'Save'; ?>
                    </button>
                </form>

                <!-- Comments -->
                <h5>Comments:</h5>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM comments WHERE recipe_id = ?");
                $stmt->execute([$recipe['id']]);
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($comments as $comment): ?>
                    <p><strong><?php echo htmlspecialchars($comment['user_id']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
                <?php endforeach; ?>

                <form action="user_dashboard.php" method="POST">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    <textarea name="comment_text" placeholder="Write a comment" required></textarea><br>
                    <button type="submit" name="comment">Comment</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No recipes available.</p>
    <?php endif; ?>

</body>
</html>
