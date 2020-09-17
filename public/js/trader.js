    function newAssignment(id) {
        var data = "model=SetAssignment" + "&method=newAssignment" + "&assignment_id=" + id;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                // Oppdatere assignment;
            }
        });
    }
    function pickUp() {
        var data;
        if(document.getElementsByClassName("page_title")[0].innerText == "Townhall" != -1) {
            data = "model=Trader" + "&method=pickUp" + "&favor=true";
        }
        data = "model=Trader" + "&method=pickUp";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                var responseText = response[1].split("|");
                gameLog(responseText[0]);
                var substrings = document.getElementById("assignment").children[0].innerHTML.split(" ");
                substrings[2] = responseText[1];
                document.getElementById("assignment").children[0].innerHTML = substrings.join(" ");
            }
        });
    }
    function deliver() {
        var data;
        if(document.getElementsByClassName("page_title")[0].innerText == "Townhall" != -1) {
            data = "model=Trader" + "&method=deliver" + "&favor=true";
        }
        data = "model=Trader" + "&method=deliver";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                var responseText = response[1].split("|");
                console.log(responseText);
                var assignmentDiv = document.getElementById("assignment");
                var substrings;
                if(response[1].indexOf("finished") == -1) {
                    gameLog(responseText[4]);
                    /*show_xp('trader', responseText[3]);*/
                    // Change the paragraphs in assignment div
                    substrings = assignmentDiv.children[0].innerHTML.split(" ");
                    substrings[2] = "0" + substrings[2].slice(substrings[2].indexOf("/"));
                    document.getElementById("assignment").children[0].innerHTML = substrings.join(" ");
                    var str = assignmentDiv.children[1].innerHTML;
                    assignmentDiv.children[1].innerHTML = str.slice(0, str.indexOf("delivered") + 9) + " " + responseText[3];
                }
                else {
                    gameLog(responseText[0]);
                    gameLog(responseText[5]);
                    if(typeof responseText[6] !== 'undefined') {
                        gameLog(responseText[6]);
                    }
                    show_xp('trader', parseInt(responseText[1]) + parseInt(responseText[4]));
                    // Change the paragraphs in assignment div
                    substrings = assignmentDiv.children[0].innerHTML.split(" ");
                    substrings[2] = "0" + substrings[2].slice(substrings[2].indexOf("/"));
                    document.getElementById("assignment").children[0].innerHTML = substrings.join(" ");
                    assignmentDiv.children[1].innerHTML = "Current Assignment: none";
                }
                if(response[1].search("levelup") != -1) {
                    let index = response[1].search("levelup");
                    newLevel.findSkill(response[1].slice(index));
                }
             }
        });
    }