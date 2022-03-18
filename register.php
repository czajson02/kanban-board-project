<!-- [IMPORTANT] Database name = "kanban" -->

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_login.css">
    <title>Sign In Panel</title>
</head>
<body>
    <header>
        
    </header>
    <main>
        <section class="form">
            <form action="" method="post" id="rejestracja">
                <p class="logowanie">KANBAN Create Account</p>
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
                    <td><label for="password2">Re-password: </label></td>
                    <td><input type="password" name="password2" id="password2" require></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Register"><span> </span><a href="login.php">Log In</a></td>
                </tr>
                </table>
            </form>
            <?php
            
                error_reporting(0);
                
                // Checking if both password inputs match each other and if login is not empty
                if((isset($_POST["login"])) && ($_POST['password']==$_POST['password2'])){
                    if(($_POST['login']=="") || ($_POST['password']=="")){
                        // Error message if inputs are empty
                        echo "<p class='err'>One or more inputs are empty.</p>";
                    }
                    else{
                    // Establishing cennection to MySQL server
                    $con = mysqli_connect('localhost','root','','kanban');
                    // Password encryption
                    $hash = sha1($_POST['password']);
                    $login = $_POST['login'];

                    // Checking if user already exist
                    $zap = "SELECT * FROM users WHERE login='$login'";
                    $req = mysqli_query($con, $zap);
                        if(mysqli_num_rows($req)>0){
                            // Error message
                            echo "User already exist";
                        }
                        else{
                            // Creating new user
                            $zap2 = "INSERT INTO users (id,login,pass) VALUES (NULL,'$login','$hash')";
                            $req2 = mysqli_query($con, $zap2);

                            echo "<p class='err'>User <span class='login'>".$login."</span> has been added.</p>";

                            // Creating new table for user
                            $zap3 = "CREATE TABLE `$login` (`id` int(11) NOT NULL AUTO_INCREMENT,`task` varchar(65) DEFAULT NULL,`type` varchar(45) DEFAULT NULL,`dif` varchar(65) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                            $req3 = mysqli_query($con, $zap3);

                            echo "<p class='err'>Board for <span class='login'>".$login."</span> has been created</p>";
                        }
                    
                    }
                }
                else if($_POST['password']!=$_POST['password2']){
                    // Error message
                    echo "<p class='err'>Passwords are not the same</p>";
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