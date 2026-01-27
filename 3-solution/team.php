<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokemon Basics</title>

    <!-- ✅ chemins ABSOLUS depuis /3-solution -->
    <link rel="stylesheet" href="/3-solution/css/main.css">
    <link href="https://fonts.cdnfonts.com/css/g-guarantee" rel="stylesheet">
    <script src="/3-solution/js/code.jquery.com_jquery-3.7.1.min.js"></script>
</head>

<body>
<header>
    <img class="logo" src="/3-solution/img/logo.png" alt="Pokémon">
    <h1>3. Ajax dynamic content Exercises</h1>
</header>

<nav hidden>
    <ul>
        <li><a id="logout" href="#">Logout</a></li>

        <!-- ✅ pages absolues -->
        <li><a href="/3-solution/team.php">My Team</a></li>
        <li><a href="/3-solution/explore.php">Explore</a></li>
        <li><a href="/3-solution/arena.php">Arena</a></li>
        <li><a href="/3-solution/pokedex.php">Pokedex</a></li>
         <li><a href="/3-solution/sendMsg.php">Message</a></li>
    </ul>
</nav>

<main hidden>
    <h2>Exercise 2: Loading Items from a database</h2>
    <h3>My Team</h3>

    <div id="pokemonDataDiv" class="flexed"></div>

    <script>
        $(document).ready(function () {
            $("nav").hide().removeAttr("hidden").fadeIn(250);
            loadTeam();
        });

        $("#logout").on("click", function(e){
            e.preventDefault();

            $.post("/3-solution/php/doLogout.php")
                .done(function(){
                    window.location.href = "/3-solution/index.php";
                })
                .fail(function(xhr){
                    alert("Logout error: " + xhr.status + " " + xhr.responseText);
                });
        });

        function loadTeam() {
            $.get("/3-solution/php/getTeam.php")
                .done(function (html) {
                    $("#pokemonDataDiv").html(html);
                    $("main").hide().removeAttr("hidden").fadeIn(250);
                })
                .fail(function (xhr) {
                    alert("getTeam error: " + xhr.responseText);
                });
        }
    </script>
</main>
</body>
</html>
