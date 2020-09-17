    if(document.getElementById("news_content").children[2] != null) {
        let buttons = document.getElementById("news_content_main_content").querySelectorAll("button");
        buttons[0].addEventListener("click", function() {
            game.fetchBuilding('tavern'); 
        });
        buttons[1].addEventListener("click", function() {
            game.fetchBuilding('citycentre');
        });
    }