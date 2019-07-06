    var canvas  = {
        location: document.getElementById("city"),
        ctx: canvas.getContext("2d"),
    };
    
    var ctxs = {
        fillstyle : canvas.ctx.fillStyle = "red",
        fillrect: canvas.ctx.fillRect(0, 0, 150, 75),
        draw: canvas.ctx.drawImage,
        
    };
    
    var startup = {
        start: function () {
            
        },
        link: function (name, url, width, height, x, y) {
            this.name = name;
            this.url = url;
            this.width = width;
            this.height = height;
            this.x = x;
            this.y = y;    
            context = myGameArea.context;
            context.fillStyle = color;
            context.fillRect(this.x, this.y, this.width, this.height);
        },
    };
    var farm = startup.link(farm, farm.php, 10, 10, 200, 300);
    
        
