<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $name ?>.css" />
    </head>
    <body>
        <div id="login">
            <form id="login_form" name="login" method="post" action="/login">
                <h3>Login to continue:</h3>
                </br>
                <label for="username">Username:</label> 
                <input id="username" type="text" name="username" /><span class="login_error"><?php echo $this->error['userErr'];?></span></br>
                <label for="password">Password:</label>
                <input id="password" type="password" name="password"/><span class="login_error"><?php echo $this->error['passErr'];?></span></br></br>
                <button type="submit">Login</button>
            </form>
            <span class="login_error"><?php echo $this->error['loginfail'];?></span></br>
            <a id="regi_link" href="/registration"> Dont have a account? Click here!</a>
        </div>
    </body>
</html>