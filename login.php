<!-- [IMPORTANT] Database name = "kanban" -->

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_login.css">
    <title>Log In Panel</title>
</head>
<body>
    <header>
        
    </header>
    <main>
        <section class="form">
            <form action="" method="post" id="logowanie">
                <p class="logowanie">KANBAN Log In</p>
                <table>
                <tr>
                    <td><label for="login">Login: </label></td>
                    <td><input type="text" name="login" id="login" require></td>
                </tr>
                <tr>
                    <td><label for="password">Password: </label></td>
                    <td><input type="password" name="password" id="password" require></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Log In"><span> </span><a href="register.php">Register</a></td>
                </tr>
                </table>
            </form>

            <?php
                error_reporting(0);

                // Checking if inputs aren't empty
                if((isset($_POST["login"])) && (isset(($_POST['password'])))){

                    // Establishing cennection to MySQL server                    
                    $con = mysqli_connect('localhost','root','','kanban');

                    // Password encryption
                    $hash = sha1($_POST['password']);
                    $login = $_POST['login'];

                    // Query for user
                    $zap = "SELECT * FROM users WHERE login='$login' AND pass='$hash'";
                    $req = mysqli_query($con, $zap);

                    // Action if user and password are correct
                    if(mysqli_num_rows($req)>0){
                        session_start();
                        ob_start();
                        $_SESSION['user'] = $login;
                        $_SESSION['start'] = true;
                        header("Location: ./index.php");
                    }

                    // Error message
                    else{
                        echo "<p class='err'>Incorrect login or password</p>";
                    }
                }
                else{
                    echo "";
                }
            
            ?>
        </section>
    </main>
    <footer>
        
    </footer>
</body>
</html>