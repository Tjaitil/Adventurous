# Introductory comments
This a project I started on when first learning PHP and mysql. At the same time my JavaScript knowledge was basic. Bear this in mind while looking at the code as some modern features are missing. Back then I had little knowledge about PHP/JS frameworks and I also wanted to learn apart from any frameworks. This taught me a lot and also learned me to appreciate all the out-of-the-box solutions frameworks give you from the get-go. I have plans to migrate the project to existing frameworks. However with limited sparetime and other projects I want to work on the timeline is uncertain.
TODO list
- Migrate all of JS to typescript
- Implement better backend api structure
- Restructure models and controller to better follower MVC pattern
- Implement better security for routes
- Migrate to Laravel

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) ![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E) ![TypeScript](https://img.shields.io/badge/typescript-%23007ACC.svg?style=for-the-badge&logo=typescript&logoColor=white) ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)

# About Game:

Adventurous (http://adventurous.no) is an RPG in open world. The graphics and game cameratakes inspiration from early gameboy pixel art games with top down look.
The player must chose one profiency from four skills which are farmer, miner, trader and warrior. Players can level up all skills but profiency skill will
unlock certain advantages.
Further the player can explore the open world fighting monsters (daqloons) or go on adventures or trade items with players/NPC. Adventures can give rewards that has limited access in the normal game.

# Screenshots from the game!
![Skjermbilde 2022-09-26 kl  21 35 32](https://user-images.githubusercontent.com/52608380/192365577-af62fbb7-a9a9-48d5-abf6-742e9836209e.png)
![Skjermbilde 2022-09-26 kl  21 38 58](https://user-images.githubusercontent.com/52608380/192365583-2fee4b73-246f-4641-ba73-888b079ee0d6.png)
![Skjermbilde 2022-09-26 kl  21 42 37](https://user-images.githubusercontent.com/52608380/192365586-c2cd5dcd-ee49-4bc1-af4a-caeae8a4c48e.png)


# Coding structure:

The backend structure is semi-build on a version of Model-View-Controller (MVC) called Model-View-Presenter (MVP) coded in PHP. The backend consisting of PHP doesn't use static methods but rather try and stay true to dependency injection principle. Mostly for debugging purposes and to include only necessary files. Im using a mysql database and PDO in PHP to do CRUD operations.

For updating the page the ajax calls are made with JS to backend to different handlers which calls the provided models. The handlers are organized by their main purpose. For example GET request and POST request have different handlers.
