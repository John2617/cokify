<?php
session_start();
include('db.php');

// Check if user_id exists in the users table
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    echo "Invalid user ID. Please log in again.";
    exit; // Stop the execution if user doesn't exist
}

// Recipe Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $ingredients = $_POST['ingredients'];
    
    // Collect all steps from the submitted form
    $instructions = implode("\n", $_POST['steps']); // Join all steps with newline
    
    $image = $_FILES['image']['name'];
    
    // Validate if image is uploaded successfully
    if (move_uploaded_file($_FILES['image']['tmp_name'], 'images/' . $image)) {
        // Insert recipe data into the database
        $stmt = $pdo->prepare("INSERT INTO recipes (name, ingredients, instructions, image, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $ingredients, $instructions, $image, $_SESSION['user_id']]);
        echo "Recipe added successfully!";
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
    <title>Chef Dashboard - Cookify</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // JavaScript to dynamically add more instruction steps
        function addStep() {
            var stepDiv = document.createElement('div');
            stepDiv.classList.add('step');
            stepDiv.innerHTML = `<label for="step">Step ${document.querySelectorAll('.step').length + 1}:</label>
                                 <textarea name="steps[]" required></textarea>`;
            document.getElementById('steps-container').appendChild(stepDiv);
        }
    </script>
</head>
<body>
    <header>
        <h2>Welcome, Chef <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <a href="logout.php">Logout</a>
    </header>
    
    <h3>Add Recipe</h3>
    <form action="chef_dashboard.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Recipe Name" required>
        <textarea name="ingredients" placeholder="Ingredients" required></textarea>
        
        <div id="steps-container">
            <div class="step">
                <label for="step">Step 1:</label>
                <textarea name="steps[]" required></textarea>
            </div>
        </div>
        
        <button type="button" onclick="addStep()">Add Step</button><br><br>
        
        <input type="file" name="image" required>
        <button type="submit">Add Recipe</button>
    </form>
</body>
</html>
