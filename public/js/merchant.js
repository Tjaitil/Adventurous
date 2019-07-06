    
    function buyItem(item, quantity) {
        this.item = item;
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
    
    function pickUp() {
        var data = "model=trader" + "&method=pickUp";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                gameLog(this.responseText);
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function deliver() {
        console.log("deliver");
        var data = "model=trader" + "&method=deliver";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    var responseText = this.responseText;    
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
                    }
                    else {
                        alert(this.responseText);
                    }
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