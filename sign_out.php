<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sign_out']))
{
    $_SESSION['role'] = "";
    session_destroy();
    header("Location: " . 'index.php');
    exit;
}

echo '
<form method="POST" action="index.php">
    <button type="submit" name="sign_out" value="1">Sign Out</button>
</form>';
?>