        function inputcheck(element) {
            
            if(element.value.length > 0 || element.value == '') {
                element.style = "border: 1px solid red";
            }
            else {
                element.style= "border:none";
            }
        }