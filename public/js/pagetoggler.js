var pages = {
    market : 0    
}

function tooglePage() {
    var data;
    var table;
    var data_number;
    switch(type) {
        case 'farmer':
            data = "model=highscores" + "&method=";
            break;
        case 'offers':
            data = "";
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