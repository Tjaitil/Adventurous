export const eventHandler = {
    events: [],
    eventOngoing: false,
    checkEvent() {
        return
        // if(this.eventOngoing == true) {
        //     return;
        // }
        // for(let i = 0; i < this.events.length; i++) {
        //     if(gamePieces.player.xpos >= this.events[i].xMin && gamePieces.player.xpos <= this.events[i].xMax &&
        //        gamePieces.player.ypos <= this.events[i].yMax && gamePieces.player.ypos >= this.events[i].yMin) {
        //         loadEvent(this.events[i].name);
        //         this.eventOngoing = true;
        //         break;
        //     }
        // }
        // function loadEvent(event) {
        //     let data = "event=" + event; 
        //     ajaxJS(data, function(response) {
        //         if(response[0] !== false) {
        //             let responseText = response[1];
        //             if(responseText.draw != false) {
        //                 let img = new Image(32, 32);
        //                 let img2;
        //                 responseText.draw.forEach(function(element) {
        //                     img2 = img.cloneNode();
        //                     img2.onload = function() {
        //                         game.properties.context3.drawImage(img2, game.properties.charX + element.x,
        //                                                   game.properties.charY + element.y);   
        //                     };
        //                     img2.src = "public/images/" + element.src;
        //                 });
        //             }
        //             conversation.loadConversation(responseText.con);
        //             this.currentEvent = true;
        //         }
        //     }, true, 'handler_e');
        // }
    }
};