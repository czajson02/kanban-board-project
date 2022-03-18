<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <link rel="stylesheet" href="style_login.css">
</head>
<body>
    <header>

    </header>
    <main>
        <section class="form">
            <?php

                // Killing session variables 
                session_start();
                unset($_SESSION["start"]);
                unset($_SESSION["user"]);
                echo '<p> </p>';
                echo 'Logging out...';

                // Redirection to login page
                header('Refresh: 1; URL = index.php');
            ?>
        </section>
    </main>
    <footer>
        
    </footer>
</body>
</html>