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
    <link rel="stylesheet" href="_styles.css">
    <title>User Overview</title>
</head>
<body class = 'admin'>
    <h1>User Overview</h1>
    <ul>
        <?php while ($user = $queryStatement->fetch(PDO::FETCH_ASSOC)): ?>
            <li>
            <p>              
    <form method="GET" action="user_overview.php">
                <form action="GET" action="user_overview.php">    
                    <?php echo htmlspecialchars($user['user_name']); ?>
                    <input type="hidden" name="user_name" value="<?php echo $user['user_name']; ?>" />
                    <input type="submit" value="Delete User" onclick="return confirm('Are you sure you want to delete this user?');">
                </form>
            </p>
            </li>
        <?php endwhile; ?>
    </ul>
    <br>
    <a href="index.php">Back to Index</a>
</body>
</html>