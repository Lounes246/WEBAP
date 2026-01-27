
            // Exercise 1 Instructions:
            // The User provides their credentials.
            // Send the login data asynchronously to the server script for checking. 
            // If the login was successfull send the User to their profile page.
            // If the username or password was incorrect, notify the user in the uWarn or PWarn fields

            $(document).ready(function() {
                if (window.location.href == "http://localhost/2-Base/team.php?effect") {
                    $("nav").slideDown("slow");
                } else {
                    $("nav").show();
                }
                $("main").fadeIn("slow");
                $("#logout").click(function() {
                    $.post("php/doLogout.php")
                        .done(function() {
                            window.location.replace("index.php");
                        });
                });



                $("#runawayButton").hide();
                var spinning = false;
                var resultDiv = $('#result');
                var pokemonCount = 100; // Number of Pokémon to display
                var winner = -1;
                // Function to add Pokémon images to the spinner
                function fillSpinner() {

                    $.get("php/genNextWin.php", function(data) {
                        var pokemonImagesContainer = $('#pokemonImages');
                        winner = data;
                        // spinner always stops at 91

                        for (var i = 1; i <= pokemonCount; i++) {
                            if (i == 91) {
                                var pokemonImage = $('<img id="winner" src="assets/pokedata/thumbnails/' + winner.toString().padStart(3, '0') + '.png" alt="Pokémon">');
                            } else {
                                var pokemonImage = $('<img src="assets/pokedata/thumbnails/' + Math.floor(Math.random() * (808) + 1).toString().padStart(3, '0') + '.png" alt="Pokémon">');
                            }

                            pokemonImagesContainer.append(pokemonImage);

                        };
                    });

                }
                // Preload images on page load
                fillSpinner();

                $.easing.myCustomEasing = function(x) {
                    // You can use any easing logic here
                    // The input 'x' represents the animation progress between 0 and 1
                    // The function should return a new value to adjust the progress
                    // For example, you can use a cubic easing equation
                    return 1 - Math.pow(130, -1.3 * x);
                };
                // Handle button click event
                $('#spinButton').click(function() {
                    if ($(this).html() === "Spin") {
                        $(this).prop('disabled', true);
                        $(".prices").animate({
                            scrollLeft: '+=13000'
                        }, 10000, "myCustomEasing", function() {
                            $(".prices").find("#winner").addClass("blinking-border");
                            $(".prices").find(":not(#winner)").addClass("greyed-out");
                            $("#spinButton").text("Take to Arena");
                            $("#spinButton").prop('disabled', false);
                            $("#runawayButton").show();
                            $.getJSON("assets/pokedata/pokedex.json", function(data) {
                                console.log(winner);
                                $("#result").html("You face a wild " + data[winner - 1].name.english);
                            });
                        });
                    } else {
                        $.post("php/demandFight.php",{"opponent":"bot","fight":"true"},function(message){
                       
                       alert(message);
                         
                       });
                    }


                });

                $("#runawayButton").click(function(){
                    $.post("php/demandFight.php",{"opponent":"bot","fight":"false"},function(message){
                       
                    alert(message);
                      
                    });
                })

                function shuffleArray(array) {
                    for (let i = array.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (808 + 1));
                        [array[i], array[j]] = [array[j], array[i]];
                    }
                }



            });
    