<?php
require_once('connect.php');
$login_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    $getUserQuery = "SELECT * FROM `user` WHERE `user_name` = :username";
    $getUserStatement = $db->prepare($getUserQuery);
    $getUserStatement->bindValue(':username', $username, PDO::PARAM_STR);
    $getUserStatement->execute();

    $user = $getUserStatement->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = "Invalid username or password.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $login_message = "Welcome Admin";
    } elseif ($_SESSION['role'] === 'user') {
        $login_message = "Welcome " . $_SESSION['username'];    
    }
}
?>

<html>
    <head>
        <link rel="stylesheet" href="_styles.css">
    </head>
    <body>
        <nav class="navigation">
            <p><?php echo $login_message; ?></p> 
            <h1>Welcome to Quarks</h1>
            <h2>Browse My Extensive Range of Holo-Programs</h2>
            
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <nav class="admin_bar">
                        <form method="GET" action="index.php">
                            <input type="submit" class="button" value="Main Page">
                        </form>
                        <form method="GET" action="admin_overview.php">
                            <input type="submit" class="button" value="Overview">
                        </form>
                        <form method="GET" action="user_overview.php">
                            <input type="submit" class="button" value="Users">
                        </form>
                        <form method="GET" action="add_category.php">
                            <input type="submit" class="button" value="Categories">
                        </form>
                        <form method="GET" action="upload_image.php">
                            <input type="submit" class="button" value="Upload Image">
                        </form>
                        <form method="GET" action="delete_image.php">
                            <input type="submit" class="button" value="Delete Image">
                        </form>
                        <form method="GET" action="post.php">
                            <input type="submit" class="button" value="New Program">
                        </form>
                    </nav>
                <?php elseif ($_SESSION['role'] === 'user'): ?>
                <?php endif; ?>

                    <form method="post" class="button" action="index.php?logout=true">
                        <br>
                        <input type="submit" value="Sign Out">
                    </form>
            <?php else: ?>

                <form method="post" action="index.php">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="login" required>
                    <label for="password">Password</label>
                    <input type="password" name="password" class="login" required>
                    <input type="submit" name="login_submit" value="Login">
                </form>

                <?php if (isset($errorMessage)): ?>
                    <p style="color:red;"><?php echo $errorMessage; ?></p>
                <?php endif; ?>

                <form method="post" action="create_user.php">
                    <input type="submit" value="Sign Up">
                </form>
            <?php endif; ?>
        </nav>
    </body>
</html>