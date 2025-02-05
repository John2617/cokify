<?php
session_start();
include('db.php');


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['chef_id'])) {
    $user_id = $_SESSION['user_id'];
    $chef_id = $_GET['chef_id'];


    $stmt = $pdo->prepare("SELECT * FROM follows WHERE user_id = ? AND chef_id = ?");
    $stmt->execute([$user_id, $chef_id]);
    $follow = $stmt->fetch();

    if (!$follow) {
      
        $stmt = $pdo->prepare("INSERT INTO follows (user_id, chef_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $chef_id]);

        echo "You are now following this chef!";
    } else {
        echo "You are already following this chef!";
    }
}
?>

