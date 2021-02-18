<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $name ?>.css" />
        <link rel='shortcut icon' type='image/x-icon' href='<?php echo constant('ROUTE_IMG') . 'favicon.ico';?>' />
    </head>
    <body>
        <div id="background_image_container">
            <img src="<?php echo constant('ROUTE_IMG') . "7.5m.png";?>" id="background_image"/>
        </div>
        <div id="login">
            <img src="<?php echo constant('ROUTE_IMG') . 'adventurous_logo.png';?>" />
            <form id="login_form" name="login" method="post" action="/login">
                <h3>Login to continue:</h3>
                </br>
                <label for="username">Username:</label> 
                <input id="username" type="text" name="username" minlength="4"/>
                <span class="login_error"><?php echo $this->error['userErr'];?></span></br>
                <label for="password">Password:</label>
                <input id="password" type="password" name="password" minlength="4"/>
                <span class="login_error"><?php echo $this->error['passErr'];?></span></br></br>
                <button type="submit">Login</button>
            </form>
            <span class="login_error"><?php echo $this->error['loginfail'];?></span></br>
            <a id="regi_link" href="/registration"> Dont have a account? Click here!</a>
        </div>
        <script src="<?php echo constant('ROUTE_JS') . 'login.js';?>"></script>
        <!--<div>
            <video autoplay>
                <source src="../public/img/adventurous video test.mov" type="video/mp4">
            </video>
        </div>-->
    </body>
</html>