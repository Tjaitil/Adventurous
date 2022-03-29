function testObjectImg() {
    console.log(gamePieces.objects);
    for (var i = 0; i < gamePieces.objects.length; i++) {
        try {
            if(gamePieces.objects[i].visible === true && 
                ["desert_dune", "nc_object", "figure"].includes(gamePieces.objects[i].type) == false && 
                gamePieces.objects[i].src.length > 1) {
                    game.properties.context4.imageSmoothingEnabled = false;
                    game.properties.context4.drawImage(gamePieces.objects[i].img,
                        gamePieces.objects[i].drawX - (gamePieces.player.xMovement * viewport.scale),
                        gamePieces.objects[i].drawY - (gamePieces.player.yMovement * viewport.scale));
                    }
                } catch(error) {
                    console.log('ERROR, could not load', error);
                }
        console.log(gamePieces.objects[i]);
    }
}

function returnObject(element, id) {
    return (element.id === id);
}