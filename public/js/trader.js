    function newAssignment(id) {
        let data = "model=Trader" + "&method=newAssignment" + "&assignment_id=" + id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1];
                updateHunger(responseText.newHunger);
                updateCountdownTab();
                document.getElementById("traderAssignment_current").innerHTML = responseText['html'];
                progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);
            }
        });
    }
    function pickUp() {
        let data = "model=Trader" + "&method=pickUp";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                let responseText = response[1];
                updateCountdownTab();
                document.getElementById("traderAssignment_cart_amount").innerHTML = responseText.cartAmount;
            }
        });
    }
    function deliver() {
        let data = "model=Trader" + "&method=deliver";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                let responseText = response[1];
                updateCountdownTab();
                if(responseText.assignment_finished == true) {
                    // Update trader assigment div with new assignment
                    document.getElementById("traderAssignment_current").innerHTML = responseText.html;
                    updateDiplomacyTab();
                    updateInventory();
                }
                else {
                    document.getElementById("traderAssignment_cart_amount").innerHTML = 0;
                    // Calculate progressBar
                    progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), 
                                                    responseText.delivered, false, true);
                    }
                }
        });
    }