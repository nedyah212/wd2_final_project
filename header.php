<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $getUserQuery = "SELECT * FROM `user` WHERE `user_name` = :username";
    $getUserStatement = $db->prepare($getUserQuery);
    $getUserStatement->bindValue(':username', $username, PDO::PARAM_STR);
    $getUserStatement->execute();

    $user = $getUserStatement->fetch(PDO::FETCH_ASSOC);
    
    if ($user) 
    {
        if (password_verify($password, $user['password'])) {
            $role = $user['role'];
            $_SESSION['role'] = $role;
            echo "<p>Login successful. Welcome, " . htmlspecialchars($username) . "!</p>";
        } else {
            echo "<p>Incorrect password, please try again.</p>";
        }
    } else 
    {
        echo "<p>User not found, please try again.</p>";
    }
}
?>

<nav>
    <h1>Visit Quarks Holosuite's Today</h1>
    <h2>Browse My Extensive Range of Holo Programs</h2>
    <?php

    if (!isset($_SESSION['role']) || $_SESSION['role'] === '') {
        echo '<form method="post" action="create_user.php">
            <input type="submit" value="Create User">
          </form><br>';
    }

    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'admin') {
            echo '<nav>
                    <h1>Admin Menu</h1>
                    <br>
                    <a href="index.php">Home</a>
                    <a href="upload_image.php">Upload Image</a>
                    <a href="delete_image.php">Delete Image</a>
                    <a href="post.php">New Program</a>
                    <br><br>
                  </nav>';

            include('sign_out.php');
        } elseif ($_SESSION['role'] === 'user') {
            include('sign_out.php');
        }
    } else {
        echo '
            <form method="post" action="index.php" name="log_in">
                <label for="username">Username</label>
                <input type="text" name="username" class="login" required>
                <label for="password">Password</label>
                <input type="password" name="password" class="login" required>
                <input type="submit" value="Login">
            </form>';
    }
    ?>
</nav>