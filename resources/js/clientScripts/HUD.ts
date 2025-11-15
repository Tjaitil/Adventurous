import { tutorial } from './tutorial';
import { ProgressBar } from '../progressBar';
import viewport from './viewport';

export const HUD = {
  container: null,
  elements: {
    // controlText: new HTMLElement(),
    controlText: null,
    control_text_building: null as HTMLElement,
    control_text_conversation: null as HTMLElement,
    // hungerProgressBar: new HTMLElement(),
    hungerProgressBar: null as ProgressBar,
    // healthProgressBar: new HTMLElement(),
    healthProgressBar: null as ProgressBar,
    // helpContainer: new HTMLElement(),
    helpContainer: null as HTMLElement,
    // huntedIcon: new HTMLElement(),
    huntedIcon: null as HTMLElement,
    // huntedLocator: new HTMLElement(),
    huntedLocator: null as HTMLElement,
    // iconContainer: new HTMLElement(),
    iconContainer: null as HTMLElement,
    tutorialProgressBar: null as ProgressBar,
  },
  makeTutorialHUD() {
    const tutorial_progressContainer = document.createElement('div');
    tutorial_progressContainer.setAttribute('id', 'tutorial_progressContainer');
    tutorial_progressContainer.style.top = 60 + 'px';
    tutorial_progressContainer.style.width = viewport.width * 0.75 + 'px';
    tutorial_progressContainer.style.height = 50 + 'px';
    tutorial_progressContainer.classList.add(
      'absolute',
      'right-0',
      'left-0',
      'text-white',
      'mx-auto',
    );
    tutorial_progressContainer.classList.add('right-0');
    const title = document.createElement('p');
    title.innerText = 'Tutorial progress';
    const under_title = document.createElement('p');
    under_title.innerText = '0. Introduction';

    // Create new progressBar and append in to progressContainer
    this.elements.tutorialProgressBar = new ProgressBar(
      document.getElementById('tutorial_progressBar'),
      {
        currentValue: tutorial.step,
        maxValue: tutorial.lastStep,
      },
    );
    tutorial_progressContainer.appendChild(title);
    tutorial_progressContainer.appendChild(
      this.elements.tutorialProgressBar.progressBarElement,
    );
    tutorial_progressContainer.appendChild(under_title);
    document.getElementById('game_hud').appendChild(tutorial_progressContainer);

    // Make exit button
    const button = document.createElement('button');
    button.appendChild(document.createTextNode('Skip tutorial'));
    button.addEventListener('click', tutorial.skipTutorial);
    button.style.position = 'absolute';
    button.style.top = 130 + 'px';
    button.style.left =
      document.getElementById('game_canvas').offsetLeft + 'px';
    button.style.zIndex = '' + 4;
    document.getElementById('game_hud').appendChild(button);
  },
  hideTutorialHUD() {
    document
      .getElementById('game_hud')
      .removeChild(document.getElementById('tutorial_progressContainer'));
  },
};
