    function newAssignment(id) {
        var data = "model=SetAssignment" + "&method=newAssignment" + "&assignment_id=" + id;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1].split("|");
                console.log(responseText);
                // Check xp message
                if(responseText[1].length > 0) {
                    gameLog(responseText[1]);
                }
                // Update trader assigment div with new assignment
                document.getElementById("current_assignment").querySelectorAll("tbody")[0].innerHTML = responseText[2];
                
                // Calculate progressBar
                progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);
                newLevel.searchString(response[1]);
                updateCountdownTab();
                /*newsContentSidebar.adjustMainContentHeight();*/
            }
        });
    }
    function pickUp() {
        var data = "model=Trader" + "&method=pickUp";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                var responseText = response[1].split("|");
                let gameInfo = JSON.parse(responseText[1]);
                gameLog(responseText[0]);
                let substrings = document.getElementById("current_assignment").querySelectorAll("td")[4].innerText.split(":")[1];
                console.log(substrings);
                let cartAmount = substrings.split("/")[1];
                // newRatio is the new ratio on how full the cart is
                newRatio = gameInfo.delivered + "/" + cartAmount;
                document.getElementById("current_assignment").querySelectorAll("td")[4].innerHTML =
                        'Cart capasity:  ' + '&nbsp' + '&nbsp' + newRatio;
                
            }
        });
    }
    function deliver() {
        var data = "model=Trader" + "&method=deliver";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                var responseText = response[1].split("|");
                gameLog(responseText[1]);
                let gameInfo = JSON.parse(responseText[2]);
                
                if(gameInfo.assignment_finished == true) {
                    gameLog(responseText[3]);
                    // Update trader assigment div with new assignment
                    document.getElementById("current_assignment").querySelectorAll("tbody")[0].innerHTML = responseText[4];
                    updateDiplomacyTab();
                    updateInventory();
                }
                else {
                    let substrings = document.getElementById("current_assignment").querySelectorAll("td")[4].innerText.split(":")[1];
                    console.log(substrings);
                    let cartAmount = substrings.split("/")[1];
                    // newRatio is the new ratio on how full the cart is
                    newRatio = 0 + "/" + cartAmount;
                    document.getElementById("current_assignment").querySelectorAll("td")[4].innerHTML =
                            'Cart capasity:  ' + '&nbsp' + '&nbsp' + newRatio;
                    // Calculate progressBar
                    document.getElementById("traderAssignment_progressBar").querySelectorAll(".progressBar_currentValue")[0].innerHTML =
                                                                                                                    gameInfo.delivered;
                    progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);
                }
                
                newLevel.searchString(response[1]);
             }
        });
    }