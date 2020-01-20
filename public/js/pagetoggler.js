var pages = {
    market : 0    
}

function tooglePage() {
    var data;
    var table;
    var data_number;
    var page;
    var type;
    if(event.target.indexOf("Next") != -1) {
        type = "Next";
    }
    else {
        type = "Prev";   
    }
    switch(page) {
        case 'farmer':
            data = "model=highscores" + "&method=";
            break;
        case 'market':
            data = "";
            table = document.getElementById("offers").children[0];
            break;
        case 'highscores':
            table = event.target.closest("div").id.trim();
            xp = table.children[1].lastChild.children[2].innerHTML;
            
            data = "model=highscores" + "&method=getNextPage" + "&skill=" + table + "&xp" + xp;
            break;
    }
    ajaxG(data, function(response) {
       if(response[0] != false) {
            var responseText = response[1].split("|");
            // If the response is below assigned amount
            if(responseText.length < data_number) {
                button = disabled;
            }
       }
    });

    
        
}