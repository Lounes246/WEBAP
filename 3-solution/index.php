<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokemon Basics</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.cdnfonts.com/css/g-guarantee" rel="stylesheet">
    <!-- import jquery Library -->
    <script type="text/javascript" src="js/code.jquery.com_jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
</head>

<body>
    <header>
        <img class="logo" src="img/logo.png" alt="Pokémon">
        <h1>3. Ajax dynamic content Exercises</h1>
    </header>
    <?php
    session_start();
    if (isset($_SESSION['id'])) {
        header("Location: team.php");
    }
    ?>
    <main>
        <h2>Exercise 1: Loading Php Sources asyncronously</h2>
        <h3>Login</h3>
        <form id="login">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <p id="Warn" class="warning"></p>

            <input type="submit" value="Login">
        </form>
        <p>I don't have an account:
            <a href="register.html"><button>Register</button></a>
        </p>

        <script>
            // Exercise 1 Instructions:
            // The User provides their credentials.
            // Send the login data asynchronously to the server script for checking. 
            // If the login was successfull send the User to their profile page.
            // If the username or password was incorrect, notify the user in the uWarn or PWarn fields

            $(document).ready(function() {
                $("#login").submit(function(e) {
                    e.preventDefault();
                    $.post("php/doLogin.php", $(this).serialize())
                        .done(function(data) {
                            $("#login").html("Logout");
                            $("main").fadeOut(function(){
                                window.location.replace("team.php?effect");
                            })
                            
                        }).fail(function(data) {
                            $("#Warn").html("Username or Password incorrect!")
                                .effect("shake", {
                                    times: 2,
                                    distance: 6
                                }, 300);
                        });
                });
            });
        </script>
    </main>
</body>

</html>