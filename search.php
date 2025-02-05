<?php
session_start();
include('db.php');

$recipes = [];
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    

    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE ingredients LIKE ? OR instructions LIKE ? OR id IN (SELECT recipe_id FROM recipes WHERE chef_id IN (SELECT id FROM users WHERE username LIKE ?))");
    $stmt->execute(["%$searchQuery%", "%$searchQuery%", "%$searchQuery%"]);
    $recipes = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Search</title>
</head>
<body>
    <h2>Search for Recipes</h2>
    <form action="search.php" method="get">
        <input type="text" name="search" placeholder="Search by recipe, ingredient, or chef" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if (count($recipes) > 0): ?>
        <h3>Search Results:</h3>
        <?php foreach ($recipes as $recipe): ?>
            <div>
                <img src="uploads/<?php echo $recipe['image']; ?>" alt="Recipe Image" width="100">
                <h3><?php echo htmlspecialchars($recipe['ingredients']); ?></h3>
                <p><?php echo htmlspecialchars($recipe['instructions']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No recipes found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
    <?php endif; ?>
</body>
</html>
