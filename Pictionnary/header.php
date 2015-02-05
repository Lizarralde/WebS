<?php
    session_start();
    if (isset($_SESSION["email"])) {
        echo '<div><span>Bienvenue !<a href="logout.php">Logout</a></span></div>';
    } else {
        echo '<div id="header">
        <form id="search" action="req_login.php" method="post">
        <label>Email <input type="text" name="email" id="email"></label>
        <label>Mot de passe<input type="password" name="password" id="password"></label>
        <input type="submit" class="submit" value="Login">
        <a href="inscription.php">Inscription</a>
        </form>
        </div>';
    }
?>
