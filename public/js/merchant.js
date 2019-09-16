    
    window.onload = function () {
        var trades = document.getElementById("items").querySelectorAll(".store_item");
        trades.forEach(function(element) {
            // Add eventListener to each node
            element.addEventListener('click', function() {
                selectTrade();
            });
        });
    };
    
    
    function selectTrade() {
        console.log(event.target.tagName);
        if(event.target.tagName == 'IMG') {
            return false;
        }
        var trade = event.target.closest(".store_item");
        document.getElementById("selected_trade").innerHTML = trade.innerHTML;
    }
    
    function buyItem() {
        this.item = event.target.closest(".store_item").querySelectorAll(".figcaption").innerHTML;
        this.quantity = quantity;
        var data = "model=Merchant" + "&method=buyItem" + "&item=" + item + "&quantity=" + quantity;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR:") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    updateStock();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function updateStock() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.open("GET", "handlers/handler_js.php?model=Merchant" + "&method=getData");
        ajaxRequest.send();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var data = this.responseText.split("|");
                document.getElementById("items").innerHTML = data[0];
            }
        };
    }
    
    function newAssignment(id) {
        var data = "model=setassignment" + "&method=newAssignment" + "&assignment_id=" + id;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    gameLog(this.responseText);
                }
                
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function pickUp(favor = false) {
        var data;
        if(favor === true) {
            data = "model=trader" + "&method=pickUp" + "&favor=true";
        }
        data = "model=trader" + "&method=pickUp";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    gameLog(this.responseText);
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function deliver(favor = false) {
        var data;
        if(favor == true) {
            data = "model=trader" + "&method=deliver" + "&favor=true";
        }
        data = "model=trader" + "&method=deliver";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    gameLog(this.responseText);
                    /*var responseText = this.responseText;    
                    if(responseText.indexOf("Assignment completed") !== -1) {
                        data = "model=updateassignment" + "&method=updateAssignment";
                        ajaxRequest = new XMLHttpRequest();
                        ajaxRequest.onload = function () {
                            if(this.readyState == 4 && this.status == 200) {
                                responseText += this.responseText;
                                gameLog(responseText);
                            }    
                        };
                        ajaxRequest.open('POST', "handlers/handler_p.php");
                        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        ajaxRequest.send(data);
                    }*/
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function getData(assignment = false) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(assignment = false) {
                    document.getElementById("assignment_status").innerHTML = this.responseText;
                }
                else {
                    document.getElementById("assignment").innerHTML = this.responseText;
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=" + "&method=" + "&assignment=" + assignment);
        ajaxRequest.send();
    }