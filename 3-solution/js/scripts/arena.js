$(document).ready(function () {
    if (window.location.href == "http://localhost/2-Base/team.php?effect") {
        $("nav").slideDown("slow");
    } else {
        $("nav").show();
    }
    $("main").fadeIn("slow");
    $("#logout").click(function () {
        $.post("php/doLogout.php")
            .done(function () {
                window.location.replace("index.php");
            });
    });


    // Function to retrieve and display battles
    function updateBattles() {
        var battleList = $("#battles");
        $.getJSON("php/getBattles.php", function (battles) {
            if (battles.length === 0) {
                battleList.html("No battles found.");
                return;
            }
            let html = ""
            $.each(battles, function (index, battle) {
                html += '<div battleid="' + battle.idBattle + '" class="section">'
                html += "User " + battle.challenger_username + " @ " + battle.arena + "<br>";
                if (battle.turn == 0 && battle.idPokemonDefendant ==null) {
                    html += '<button id="acceptBattle" class="btnAttack">Accept</button>';
                    html += '<button id="rejectBattle" class="btnAttack">Reject</button>';
                }
                html += '<button id="detailsBattle" class="btnAttack">Details</button>';
                html += "</div>"
            });
            battleList.html(html);
            $("#rejectBattle").click(function(){
                $.post("php/rejectChallenge.php",{"idBattle":$(this).parent().attr("battleid")})
                updateBattles();
            });
            $("#acceptBattle").click(function(){
                var battleId=$(this).parent().attr("battleid");
                $.getJSON("php/getTeam.php",function(team){
                    let output= $("#chooseDefendant .flexed");
                    if (team.length === 0) {
                        output.html("No battles found.");
                        return;
                    }
                    let html = ""
                    $.each(team, function (index, member) {
                        html += '<div class="section" pokeId="'+member.idPokemon+'">';
                        html += '<img src="assets/pokedata/thumbnails/'+member.idSpecies.toString().padStart(3, '0')+'.png">';
                        html += '<h2 >'+member.nickname+'</h2>';
                        html += '<h3 > Level: '+member.level+'</h3><br>';
                        html += '<h3 > Health: '+member.level+'/100</h3><br>';
                        html += '<button class="btnAttack choose">Choose</button>';
                        html += "</div>"
        
                    });
                    output.append(html);
                    //$("#chooseDefendant").show();
                    document.getElementById("chooseDefendant").showModal();

                    $(".btnAttack.choose").click(function(){
                        $.post("php/acceptFight.php",{"pokemon":$(this).parent().attr("pokeId"),"battle":battleId},function(data){
                            alert(data);
                        })
                    })

                });
                
            });

        });
    }
    updateBattles();


    // Set up an interval to refresh the data every 6 seconds
    setInterval(function () {
        updateBattles();
    }, 6000);

    
       // Function to retrieve and display opponents ready for battle
    
        var opponentList = $("#opponents");
        $.getJSON("php/getOpponents.php", function (opponents) {
            if (opponents.length === 0) {
                opponentList.html("No battles found.");
                return;
            }
            let html = ""
            $.each(opponents, function (index, opponent) {
                html += '<div id="' + opponent.idTrainer + '" class="section">'
                html += " " + opponent.username + " : "+ opponent.pokemonCount +" Pokemon <br>"
                html += '<button id="challenge" class="btnAttack">Challenge</button>';
                html += "</div>"

            });
            opponentList.html(html);

        });
    
});






/*
    let fighter;
    let opponentID= Cookies.get("encounter");
    let opponentimg="pokedata/images/"+opponentID.padStart(3, '0')+".png";
    let opponentname;
    $.getJSON("pokedata/pokedex.json",function(pokedex){
        $("#opponentname").hide();
        opponentname= pokedex[opponentID-1].name.english;
        $("#opponentname").html(opponentname);
    });
            $("#opponentimg").fadeOut(function(){
                $("#opponentimg").attr("src",opponentimg);
                $("#opponentname").fadeIn();
                $("#opponentimg").fadeIn();
            });
    document.getElementById("choose").showModal();
  //  var team=JSON.parse(Cookies.get("team"));
    for (let index = 0; index < team.length; index++) {
            member=team[index];
        $("#choose").append('<button class="btnChoose" type="button">'+member+'</button>');
    }

    $("#choose").on("click",".btnChoose",function(){
        fighter=$(this).html();
        $.post("php/getPokemonID.php",{"pokemon_name":fighter},function(pokemonID){
            let playerimg="pokedata/images/"+pokemonID.padStart(3, '0')+".png";
            $("#playerimg").fadeOut(function(){
                $("#playername").hide();
                $("#playerimg").attr("src",playerimg);
                $("#playername").html(fighter);
                $("#playername").fadeIn();
                $("#playerimg").fadeIn();
            });
            $(".current-hp").html()
            document.getElementById("choose").close("done");
        })
    })
})
*/

