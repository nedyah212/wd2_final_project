<nav>
    <h1>Visit Quarks Holosuites Today</h1>
    <h2>Browse My Extensive Range of Holoprograms</h2>

    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        echo '<p>Welcome Admin</p>';
        include('nav.php');
    }
    elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
        echo '<p>Welcome User</p>';
    } 
    else {
        echo '
            <form method="post" action="login.php">
                <label for="username">Username</label>
                <input type="text" name="username" class="login" required>
                <label for="password">Password</label>
                <input type="password" name="password" class="login" required>
                <input type="submit" value="Login">
            </form>
        ';
    }
    ?>
</nav>