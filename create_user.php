<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_user']) && $_POST['create_user'] == 'true') {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['email'])) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);
            $email = trim($_POST['email']);

            if (empty($username) || empty($password) || empty($confirmPassword) || empty($email)) {
                echo "<p>Please fill in all fields.</p>";
            } elseif ($password !== $confirmPassword) {
                echo "<p>Passwords do not match. Please try again.</p>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<p>Invalid email format. Please enter a valid email address.</p>";
            } else {
                $checkQuery = "SELECT COUNT(*) FROM `user` WHERE `user_name` = :username";
                $stmt = $db->prepare($checkQuery);
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                if ($stmt->fetchColumn() > 0) {
                    echo "<p>User already exists. Please choose a different username.</p>";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $insertQuery = "INSERT INTO `user` (`user_name`, `password`,`email`, `role`) VALUES (:username, :password, :email, 'user')";
                    $stmt = $db->prepare($insertQuery);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);

                    if ($stmt->execute()) {
                        echo "<p>User created successfully!</p>";
                    } else {
                        echo "<p>Error creating user. Please try again later.</p>";
                    }
                }
            }
        }
    } elseif (isset($_POST['go_back']) && $_POST['go_back'] == 'true') {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
</head>
<body>
    <h2>Create a New User</h2>
    <form method="post" action="">
        <input type="hidden" name="create_user" value="true">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br><br>
        <input type="submit" value="Create User">
    </form>
    <form method="post" action="" name="back">
        <input type="hidden" name="go_back" value="true">
        <input type="submit" value="Go Back">
    </form>
</body>
</html>