# About Game:

Adventurous (http://adventurous.no) is an RPG in open world. The graphics and game cameratakes inspiration from early gameboy pixel art games with top down look, especially pokemon games. 
In the game players must chose one profiency from four skills which are farmer, miner, trader and warrior. Players can level up all skills but profiency skill will
unlock certain advantages.
Further the player can explore the open world fighting monsters (daqloons) or go on adventures or trade items with players/NPC. Adventures can give rewards that has
limited access in the normal game. Players are incentivized to go on adventures.

# Screenshots from the game:
![Skjermbilde 2021-03-06 kl  15 40 19](https://user-images.githubusercontent.com/52608380/110211195-bfeae000-7e95-11eb-9aa4-aca35317c6dc.png)
![Skjermbilde 2021-03-06 kl  15 32 24](https://user-images.githubusercontent.com/52608380/110211196-c0837680-7e95-11eb-8be2-99381499d849.png)
![Skjermbilde 2021-03-06 kl  15 29 00](https://user-images.githubusercontent.com/52608380/110211198-c11c0d00-7e95-11eb-9b94-29d37838d9c9.png)
![Skjermbilde 2021-03-06 kl  15 27 39](https://user-images.githubusercontent.com/52608380/110211200-c11c0d00-7e95-11eb-813a-00c4d1beb28f.png)



# Coding structure:

The backend structure is build on a version of Model-View-Controller (MVC) called Model-View-Presenter (MVP). There isn't used a specific framework to achieve this but rather a own designed framework. The frontend page especially JS doesn't follow a specific framework.

When you go to a page in the adventurous domain you will be redirected to index.php site where the site will try and provide with the controller matching the site
you are trying to match. If no result are found the error controller will be loaded.
The controller load the default view page with a name for the requested page as a parameter which will be required. 

For updating the page AJAX calls are used. Different AJAX calls are sent
to different handlers which calls the provided models. The handlers are organized by their main purpose. For example GET request and POST request have different
handlers. There is a own errorhandler for AJAX calls.

The backend consisting of PHP doesn't use static methods but rather try and stay true to dependency injection principle. Mostly for debugging purposes and to include only necessary files.
For the SQL, the code uses PDO to interact with database.
The game is hosted by domeneshop.no and FTP is used for publishing the code.


