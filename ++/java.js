    function fill () {
    	document.getElementById("demo").innerHTML=" hello";
        color();
    }

    function color () {
    	document.getElementById("demo").style.backgroundColor ="red";
    }
    
    function checkCookie () {
    if (document.cookie.length != 0) {
        var nameValueArray = document.cookie.split(/\W+/);
        var hello = nameValueArray.indexOf("lastname");
        document.getElementById("demo").innerHTML = "Name is now" + " " + hello;
    }
    
    else {
        alert("No cookie detected");
    }
}
    function setCookie ()
    {
        var firstName = document.getElementById("name_1").value;
        if (firstName.length != 0)
        {
            document.cookie = "firstname=" + firstName + ";expires=Fri, 5 Aug 2020 01:00:00 UTC;";
        }
        else {
            alert("Please enter valid information");
        }
    }

    function SetyetCookie () {
        var lastName = document.getElementById("name_2").value;
        if (lastName.length != 0)
        {
            document.cookie = "lastname=" + lastName + ";expires=Fri, 5 Aug 2020 01:00:00 UTC;";
        }
        else {
            alert("Please enter valid information");
        }
    }
      
    function check_array () {    
        
        
        
        /*var city = ["Oslo", "London", "New York", "La Paz"];
        var form_value = document.getElementById("input_1").value;
        var array_check = false;
        for (var x = 0; x <city.length; x++ ) {
            if (city[x] === form_value ) {
                array_check = true;
            }
        }
        if (array_check) {
            alert("The site includes" + " " + form_value);
        }
        else {
            alert("The site doesnt include" + " " + form_value);
        } */
    }
    function change_table () {
  
        var cars = [];
        for (i = 0; i < 12; i++) {
            cars[i] = i+1;
        }
        /* var hello = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"]; */
        for (i = 0; i < 12 ; i++ ) {
            document.getElementsByTagName("td")[0+i].innerHTML = cars[i];
        }
    }