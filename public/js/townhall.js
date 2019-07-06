    
    function doFavor() {
        /*var element = document.getElementById("favor");
        var item = element.children[0].innerHTML;
        var amount = element.children[1].innerHTML;
        
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/ajax.php?model=" + "&method=" + "&item=" + item + "&amount=" + amount);
        ajaxRequest.send();*/
        var data = "model=setassignment" + "&method=newAssignment" + "&favor=true" + "&assignment_id=" + 1;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                alert(this.responseText);
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function pickUp() {
        var data = "model=trader" + "&method=pickUp" + "&favor=true";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                alert(this.responseText);
            }
        };
        ajaxRequest.open('POST', "handlers/handler_g.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function deliver() {
        var data = "model=trader" + "&method=deliver" + "&favor=true";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var responseText = this.responseText;
                
                var test = responseText.indexOf("Assignment completed");
                console.log(test);
                
                if(test !== -1) {
                    data = "model=updateassignment" + "&method=updateAssignment" + "&favor=true";
                    ajaxRequest = new XMLHttpRequest();
                    ajaxRequest.onload = function () {
                        if(this.readyState == 4 && this.status == 200) {
                            responseText += this.responseText;
                            alert(responseText);
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
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    