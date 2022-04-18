    if(document.getElementById("news_content").children[3] != null) {
        // Add events to item elements
        itemTitle.addItemClassEvents();
    }


    function make(item, element) {
        var parent = element.parentNode;
        var amount = parent.children[0].value;
        parent.children[0].value = '';
        
        if(amount == 0) {
            alert("Please enter a valid quantity");
            return false;
        }
        var data = "model=Bakery" + "&method=makeMeal" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, function(response) {
            console.log(response[0]);
            if(response[0] != false) {
                updateInventory('bakery');
            }
        });
    }
    // const bakeryModule = {
    //     init() {
    //         itemTitle.addItemClassEvents();
    //     },
    //     make(item, element) {
    //         var parent = element.parentNode;
    //         var amount = parent.children[0].value;
    //         parent.children[0].value = '';
            
    //         if(amount == 0) {
    //             alert("Please enter a valid quantity");
    //             return false;
    //         }
    //         var data = "model=Bakery" + "&method=makeMeal" + "&item=" + item + "&amount=" + amount;
    //         ajaxP(data, function(response) {
    //             console.log(response[0]);
    //             if(response[0] != false) {
    //                 updateInventory('bakery');
    //             }
    //         });
    //     }
    // };
    // export default bakeryModule;