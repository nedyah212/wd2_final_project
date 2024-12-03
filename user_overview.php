<?php 
require('connect.php');
session_start();
include('header.php');

if (isset($_GET['user_name'])) { 
    $deleteUserName = $_GET['user_name'];
    
    $deleteQuery = "DELETE FROM `user` WHERE `user_name` = :user_name";
    $deleteStatement = $db->prepare($deleteQuery);
    $deleteStatement->bindParam(':user_name', $deleteUserName, PDO::PARAM_STR);

    try {
        $deleteStatement->execute();
        header('Location: ' . $_SERVER['PHP_SELF']); 
        exit;
    } catch (PDOException $e) {
        echo "Error deleting user: " . htmlspecialchars($e->getMessage());
    }
}

$query = "SELECT * FROM `user` WHERE `role` = 'user'";
$queryStatement = $db->prepare($query);
$queryStatement->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Overview</title>
</head>
<body>
    <h1>User Overview</h1>
    <ul>
        <?php while ($user = $queryStatement->fetch(PDO::FETCH_ASSOC)): ?>
            <li>
                <p>
                    <?php echo htmlspecialchars($user['user_name']); ?>
                    <a href="?user_name=<?php echo urlencode($user['user_name']); ?>" 
                       onclick="return confirm('Are you sure you want to delete this user?');">Delete User</a>
                </p>
            </li>
        <?php endwhile; ?>
    </ul>
    <br>
    <a href="index.php">Back to Index</a>
</body>
</html>