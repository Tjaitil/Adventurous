import { tutorial } from './tutorial';
import { pauseManager } from './pause';
import { inputHandler } from './inputHandler';
import { Game } from '../advclient';
import { GameLogger } from '../utilities/GameLogger';
import { GamePieces } from './gamePieces';
import { useConversationStore } from '@/ui/stores/ConversationStore';

export const controls = {
  playerLeft: false,
  playerUp: false,
  playerRight: false,
  playerDown: false,
  actionText: 'Press x',
  enterText: 'E - Enter',
  enterButton: 'E -',
  personText: 'W - Talk to ',
  personButton: 'W -',
  device: 'pc',
  w: null,
  p: null,
  space: null,
  continueConversation: null,
  checkPlayerMovement() {
    GamePieces.player.speedX = 0;
    GamePieces.player.speedY = 0;
    if (this.playerLeft) {
      GamePieces.player.speedX = -GamePieces.player.speed;
    }
    if (this.playerRight) {
      GamePieces.player.speedX = GamePieces.player.speed;
    }
    if (this.playerUp) {
      GamePieces.player.speedY = -GamePieces.player.speed;
    }
    if (this.playerDown) {
      GamePieces.player.speedY = GamePieces.player.speed;
    }
  },
  e(eMouseX: string | boolean = false, eMouseY: string | boolean = false) {
    function enterBuilding() {
      for (let i = 0; i < GamePieces.buildings.length; i++) {
        const object = GamePieces.buildings[i];
        if (
          GamePieces.player.ypos > object.diameterUp &&
          GamePieces.player.ypos < object.diameterDown &&
          GamePieces.player.xpos > object.diameterLeft &&
          GamePieces.player.xpos < object.diameterRight &&
          Math.abs(GamePieces.player.ypos - object.diameterDown) < 32
        ) {
          if (!Game.properties.inBuilding) {
            inputHandler.fetchBuilding(object.src.split('.png')[0]);
          }
          break;
        }
      }
    }
    if (tutorial.onGoing) {
      GameLogger.addMessage(
        'This building can not be accessed on tutorial island',
      );
      GameLogger.logMessages();
    }
    if (!Game.properties.inBuilding && Game.properties.device == 'pc') {
      enterBuilding();
    } else if (
      !Game.properties.inBuilding &&
      Game.properties.device == 'mobile'
    ) {
      // console.log("check building");
      // let element = document.getElementById("text_canvas");
      // let ElementPos = element.getBoundingClientRect();
      // // Remove elementPos of the canvas so that 0.0 is in up-left corner
      // let mouseY = eMouseY - ElementPos.top;
      // let mouseX = eMouseX - ElementPos.left;
      // let x = mouseX + (GamePieces.player.xpos - viewport.width / 2 + 32);
      // let y = mouseY + (GamePieces.player.ypos - viewport.height / 2);
      // let result = false;
      // for (let i = 0; i < GamePieces.buildings.length; i++) {
      //     let object = GamePieces.buildings[i];
      //     if (
      //         y > object.diameterUp &&
      //         y < object.diameterDown &&
      //         x > object.diameterLeft &&
      //         x < object.diameterRight &&
      //         Math.abs(GamePieces.player.ypos - object.diameterDown) < 32
      //     ) {
      //         result = true;
      //         inputHandler.fetchBuilding(object.src.split(".png")[0]);
      //         break;
      //     }
      // }
      // return result;
    }
  },

  /* one tap -> if tap on building checkBuilding()->fetchBuilding else talk to character;
   * double tap -> enter next map
   * move and endmove for mobile
   */
  mobileControlButtonMove(event) {
    if (Game.properties.gameState === 'pause') {
      pauseManager.resumeGame();
    }
    const button = document.getElementById('control_button');
    const element = event.target.closest('#control');
    const elementPos = element.getBoundingClientRect();
    const buttonPos = button.getBoundingClientRect();
    const eventY = event.targetTouches[0].clientY - elementPos.y;
    const eventX = event.targetTouches[0].clientX - elementPos.x;
    // eventYTrigger/eventXTrigger is the minimum width the button gets moved before movement happens;
    const eventYTrigger = 50;
    const eventXTrigger = 50;
    // a is the distance from eventY to diameter
    // b is the distance from eventX to diaemter
    const a = Math.abs(eventY - elementPos.height / 2);
    const b = Math.abs(eventX - elementPos.width / 2);
    button.style.top =
      event.targetTouches[0].clientY - elementPos.y - 25 + 'px';
    button.style.left = event.targetTouches[0].clientX - 50 + 'px';

    if (a < 5 && b < 5) {
      this.playerLeft = false;
      this.playerUp = false;
      this.playerRight = false;
      this.playerDown = false;
      return false;
    }
    if (a > 110 || b > 110) {
      this.playerLeft = false;
      this.playerUp = false;
      this.playerRight = false;
      this.playerDown = false;
      return false;
    }
    let angle;
    if (eventX > eventXTrigger && eventY < eventYTrigger) {
      angle = (Math.atan(a / b) / (2 * 3.14)) * 360;
    }
    if (eventX < eventXTrigger && eventY < eventYTrigger) {
      angle = (Math.atan(b / a) / (2 * 3.14)) * 360 + 90;
    }
    if (eventX < eventXTrigger && eventY > eventYTrigger) {
      angle = (Math.atan(a / b) / (2 * 3.14)) * 360 + 180;
    }
    if (eventX > eventXTrigger && eventY > eventYTrigger) {
      angle = (Math.atan(b / a) / (2 * 3.14)) * 360 + 270;
    }
    if (337.5 < angle || angle < 22.5) {
      this.playerLeft = false;
      this.playerUp = false;
      this.playerRight = true;
      this.playerDown = false;
    }
    if (22.5 < angle && angle < 67.5) {
      this.playerLeft = false;
      this.playerUp = true;
      this.playerRight = true;
      this.playerDown = false;
    }
    if (67.5 < angle && angle < 112.5) {
      this.playerLeft = false;
      this.playerUp = true;
      this.playerRight = false;
      this.playerDown = false;
    }
    if (112.5 < angle && angle < 157.5) {
      this.playerLeft = true;
      this.playerUp = true;
      this.playerRight = false;
      this.playerDown = false;
    }
    if (157.5 < angle && angle < 202.5) {
      this.playerLeft = true;
      this.playerUp = false;
      this.playerRight = false;
      this.playerDown = false;
    }
    if (202.5 < angle && angle < 247.5) {
      this.playerLeft = true;
      this.playerUp = false;
      this.playerRight = false;
      this.playerDown = true;
    }
    if (247.5 < angle && angle < 292.5) {
      this.playerLeft = false;
      this.playerUp = false;
      this.playerRight = false;
      this.playerDown = true;
    }
    if (292.5 < angle && angle < 337.5) {
      this.playerLeft = false;
      this.playerUp = false;
      this.playerRight = true;
      this.playerDown = true;
    }
  },
  endMobileMove() {
    const button = document.getElementById('control_button');
    button.style.top = '25%';
    button.style.left = '25%';
    this.playerLeft = false;
    this.playerUp = false;
    this.playerRight = false;
    this.playerDown = false;
  },
  setup() {
    // Check for device type and bind events according to device
    if (
      window.screen.width > 830 ||
      !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent,
      )
    ) {
      document.getElementById('control').style.display = 'none';
      this.actionText = 'Press x';
      this.enterText = 'E - Enter building';
      this.enterButton = 'E -';
      this.personText = 'W - Talk to ';
      this.personButton = 'W -';
      this.device = 'pc';
    } else {
      this.actionText = 'Double tap';
      this.enterText = 'Tap on building to enter';
      this.enterButton = 'Tap on';
      this.personText = 'Tap on screen to talk';
      this.personButton = 'Tap on';
      this.device = 'mobile';
    }
    if (Game.properties.device === 'mobile') {
      // document.getElementById("text_canvas").addEventListener("click",
      //     function() {
      //         // If the conversation_container visibility is visible, conversation is happening. Prevent other actions
      //         if(conversation.checkConversation()) {
      //             return false;
      //         }
      //         // If game is loading, return false
      //         if(game.properties.gameState === 'loading') {
      //             return false;
      //         }
      //         // doubleClickDetect();
      //         let clickTimer;
      //         if(click == 1) {
      //             let clientX = event.clientX;
      //             let clientY = event.clientY;
      //             /*let clientX = event.touches[0].clientX;
      //             let clientY = event.touches[0].clientY;*/
      //             console.log(clientX);
      //             console.log(clientY);
      //             clickTimer = setTimeout(function() {
      //                 // Single tap
      //                 let check = inputHandler.checkBuilding(clientX, clientY);
      //                 if(check == false) {
      //                     inputHandler.checkCharacter();
      //                 }
      //             }, 300);
      //         }
      //         else {
      //             // Double tap
      //             clearTimeout(clickTimer);
      //             // If game is loading, return false
      //             if(game.properties.gameState === 'playing') {
      //                 game.getNextMap();
      //             }
      //         }
      //     });
      // document.getElementById("control").addEventListener("touchmove", controls.mobileControlButtonMove);
      // document.getElementById("control").addEventListener("touchend", controls.endMobileMove);
    }
    // Set controls
    controls.w = () => {
      inputHandler.interactCharacter();
    };
    controls.p = () => {
      pauseManager.togglePause();
    };

    // Prevent user from scrolling with arrow keys on site
    window.addEventListener(
      'keydown',
      e => {
        // space and arrow keys
        if (
          [32, 37, 38, 39, 40, 67].indexOf(e.keyCode) > -1 ||
          (e.keyCode == 32 && e.target == document.body)
        ) {
          e.preventDefault();
        }
      },
      false,
    );
    window.addEventListener(
      'keydown',
      e => {
        switch (e.keyCode) {
          case 32:
            controls.space();
            break;
          case 37:
            controls.playerLeft = true;
            break;
          case 38:
            controls.playerUp = true;
            break;
          case 39:
            controls.playerRight = true;
            break;
          case 40:
            controls.playerDown = true;
            break;
          case 65:
            // A
            if (GamePieces.player.cooldown <= 0 && GamePieces.player.combat) {
              GamePieces.player.combatActions.attack = true;
              GamePieces.player.attack = true;
            }
            break;
          case 81:
            // Q
            if (GamePieces.player.cooldowns.block <= 0) {
              GamePieces.player.combatActions.block = true;
            }
            break;
          case 67:
            // C
            GamePieces.player.combat = !GamePieces.player.combat;
            break;
          case 87:
            // W
            if (
              Game.properties.gameState === 'playing' &&
              !useConversationStore().isActive
            ) {
              controls.w();
            }
            break;
          case 69:
            // E
            if (
              Game.properties.gameState === 'playing' &&
              !useConversationStore().isActive
            ) {
              controls.e();
            }
            break;
          case 80:
            // P
            if (!useConversationStore().isActive) {
              controls.p();
            }
            break;
        }
      },
      false,
    );
    window.addEventListener(
      'keyup',
      function (e) {
        switch (e.keyCode) {
          case 37:
            controls.playerLeft = false;
            break;
          case 38:
            controls.playerUp = false;
            break;
          case 39:
            controls.playerRight = false;
            break;
          case 40:
            controls.playerDown = false;
            break;
        }
      },
      false,
    );
  },
};
