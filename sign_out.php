<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sign_out'])) {
    $_SESSION['role'] = "";
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<html>
    <head>
        <link rel="stylesheet" href="_styles.css">
    </head>
    <body>
        <form method="POST" action="index.php">
            <input type="submit" name="sign_out">
        </form>
    </form>
    </body>
</html>