    let map = {
            rows: 9,
            columns: 10,
            tiles: [
                    0, 0, 0, 0, 0, 0, 0, 1, 0, 0,
                    1, 1, 1, 0, 0, 0, 1, 1, 1, 1,
                    1, 0, 1, 0, 0, 1, 1, 0, 0, 1,
                    0, 0, 1, 1, 1, 1, 0, 0, 0, 1,
                    0, 0, 1, 0, 0, 0, 0, 0, 1, 1,
                    1, 1, 1, 0, 0, 0, 0, 0, 0, 0,
                    1, 0, 1, 1, 0, 0, 0, 0, 0, 0,
                    1, 0, 0, 1, 1, 0, 0, 0, 0, 0,
                    1, 1, 0, 1, 0, 0, 0, 0, 0, 0],
            getTile: function(col, row) {
                return this.tiles[row * map.columns + col];    
            },
            drawMap: function() {
                for(c = 0; c < map.columns; c++) {
                    for(r = 0; r < map.rows; r++) {
                        if(map.tiles[r * map.columns + c] === 0) {
                            game.properties.ctx.fillStyle = "black";
                        }
                        else {
                            game.properties.ctx.fillStyle = "white";
                        }
                        game.properties.ctx.fillRect(c * 32, r * 32, 32, 32);
                               
                    }
                }
            }
        };
    let game = {
        load: function() {
            map.drawMap();
            game.player.render(0 * 32 + 8, 1 * 32 + 8);
            game.controls();
            game.properties.requestId = window.requestAnimationFrame(game.update);
        },
        update: function() {
            let newX = 0;
            let newY = 0;
            let diameterxPlayer = 0;
            let diameteryPlayer = 0;
            if(game.controls.left == true) {
                 newX = -1;
            }
            if(game.controls.right == true) {
                newX = 1;
                diameterxPlayer = 16;
            }
            if(game.controls.up == true) {
                newY = - 1;
            }
            if(game.controls.down == true) {
                newY = 1;
                diameteryPlayer = 16;
            }
            if(newX != 0 || newY != 0) {
                let collision = game.checkCollision(game.player.x + newX + diameterxPlayer, game.player.y + newY + diameteryPlayer);
                if(collision == false) {
                    map.drawMap();
                    game.player.render(game.player.x + newX, game.player.y + newY);    
                }
            }
            game.properties.requestId = window.requestAnimationFrame(game.update);
        },
        checkCollision: function(newxPos, newyPos) {
            let column = Math.floor(newxPos / 32);
            let row = Math.floor(newyPos / 32);
            let tile = map.getTile(column, row);
            if(tile === 1) {
                return false;
            }
            else {
                return true;
            }
        }
    };
    game.controls = function() {
        window.addEventListener('keydown', function (e) {
            if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
                e.preventDefault();
            }
            switch(e.keyCode) {
                case 37:
                    game.controls.left = true;
                    break;
                case 38:
                    game.controls.up = true;
                    break;
                case 39:
                    game.controls.right = true;
                    break;
                case 40:
                    game.controls.down = true;
                    break;
            }
            
        }, false);
        window.addEventListener('keyup', function (e) {
            switch(e.keyCode) {
                case 37:
                    controls.playerLeft = false;
                    break;
                case 38:
                    game.controls.up = false;
                    break;
                case 39:
                    game.controls.right = false;
                    break;
                case 40:
                    game.controls.down = false;
                    break;
            }
        }, false);
    };
    game.player = {
        x: 0,
        y: 0,
        render: function(x, y) {
            game.player.x = x;
            game.player.y = y;
            game.properties.ctx.fillStyle = "red";
            game.properties.ctx.fillRect(game.player.x, game.player.y, 16, 16);
        }
    };
    game.properties = {
        ctx: document.getElementById("test_canvas").getContext("2d")
    };
    window.addEventListener("load", game.load);
    
let opacity2 = 1;
let column = 0;
let rows = 0;
/*var x = setInterval(function() {
	var c = document.getElementById("test_canvas");
	var ctx = c.getContext("2d");
	ctx.clearRect(column * 32, rows * 32, 32, 32)
    ctx.fillStyle = 'rgba(0, 0, 0,' + opacity + ')';
    ctx.fillRect(column * 32, rows * 32, 32, 32);
    opacity-= 0.2;
    console.log(opacity);
    if(opacity < 0) {
    	opacity = 1;
        column += 1;
        if(column == 10) {
        	column = 0;
            rows += 1;
        }
    }
    if(rows == 5) {
   		clearInterval(x); 
    }
}, 20);*/


function canvasOpacity() {
    x = setInterval(function() {
      var c = document.getElementById("myCanvas");
      var ctx = c.getContext("2d");
      ctx.clearRect(column * 32, rows * 32, 32, 32)
      ctx.fillStyle = 'rgba(0, 0, 0,' + opacity + ')';
      ctx.fillRect(column * 32, rows * 32, 32, 32);
      opacity-= 0.2;
      if(opacity < 0) {
      	opacity = 1;
        clearInterval(x); 
      }
    }, 50);
}

/*window.addEventListener("keydown", function(e) {
    if(e.keyCode == 39) {
   		column += 1;
        canvasOpacity();
    }
});*/

