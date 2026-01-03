export function setUpTabList() {
  const tabLists = [
    ...document.querySelectorAll('[data-is-setup="false"] [role="tablist'),
  ];
  tabLists.forEach((tabList: HTMLElement) => {
    const tabs = tabList.querySelectorAll('[role="tab"]');
    tabs.forEach((tab: HTMLElement) => {
      tab.addEventListener('click', e => { changeTabs(e); });
      if (tab.getAttribute('aria-selected') === 'true') {
        const tabPanelId = tab.getAttribute('aria-controls');
        console.log(tabPanelId);
        console.log(document.querySelector(`#${tabPanelId}`));
        const tabPanel = getTabPanel(tab);
        toggleTabPanel(tabPanel, false);
      }
    });

    tabList.setAttribute('data-is-setup', 'true');
  });
}

function getTabPanel(tab: HTMLElement) {
  if (!tab) {
  }
  const tabPanelId = tab.getAttribute('aria-controls');
  const tabPanel = <HTMLElement>document.querySelector(`#${tabPanelId}`);
  return tabPanel;
}

export function toggleTabPanel(tabPanel: HTMLElement, hide: boolean) {
  if (hide) {
    tabPanel.classList.add('hidden');
    tabPanel.classList.add('absolute');
  } else {
    tabPanel.classList.remove('hidden');
    tabPanel.classList.remove('absolute');
  }
}

function changeTabs(e) {
  const tab = <HTMLElement>e.target;
  const parent = tab.parentElement;

  parent
    .querySelectorAll('[aria-selected="true"]')
    .forEach((tab: HTMLElement) => {
      tab.setAttribute('aria-selected', 'false');
      const tabPanelId = tab.getAttribute('aria-controls');
      const tabPanel = getTabPanel(tab);
      toggleTabPanel(tabPanel, true);
    });

  if (tab.ariaSelected === 'true') {
    tab.style;
    return;
  }

  tab.setAttribute('aria-selected', 'true');
  const tabPanelId = tab.getAttribute('aria-controls');
  const tabPanel = getTabPanel(tab);
  toggleTabPanel(tabPanel, false);
}
