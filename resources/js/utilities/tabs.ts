export function setUpTabList() {
    const tabLists = [...document.querySelectorAll('[data-is-setup="false"], [role="tablist')];

    tabLists.forEach((tabList: HTMLElement) => {
        let tabs = tabList.querySelectorAll('[role="tab"]');
        tabs.forEach((tab: HTMLElement) => {
            tab.addEventListener('click', (e) => changeTabs(e));
            if (tab.getAttribute("aria-selected") === "true") {
                let tabPanelId = tab.getAttribute("aria-controls");
                console.log(tabPanelId);
                console.log(document.querySelector(`#${tabPanelId}`));
                let tabPanel = getTabPanel(tab);
                toggleTabPanel(tabPanel, false);
            }
        });

        tabList.setAttribute('data-is-setup', 'true');
    });
}


function getTabPanel(tab: HTMLElement) {
    if (!tab) {
        console.log("Tab not found", tab);
    }
    let tabPanelId = tab.getAttribute("aria-controls");
    let tabPanel = <HTMLElement>document.querySelector(`#${tabPanelId}`);
    return tabPanel;
}

function toggleTabPanel(tabPanel: HTMLElement, hide: boolean) {
    if (hide) {
        tabPanel.classList.add("hidden");
        tabPanel.classList.add("absolute");
    } else {
        tabPanel.classList.remove("hidden");
        tabPanel.classList.remove("absolute");
    }
}

function changeTabs(e) {
    let tab = <HTMLElement>e.target;
    let parent = tab.parentElement;

    parent.querySelectorAll('[aria-selected="true"]').forEach((tab: HTMLElement) => {
        tab.setAttribute("aria-selected", "false")
        let tabPanelId = tab.getAttribute("aria-controls");
        let tabPanel = getTabPanel(tab);
        toggleTabPanel(tabPanel, true);
    });

    if (tab.ariaSelected === "true") {
        tab.style
        return;
    }

    tab.setAttribute("aria-selected", "true");
    let tabPanelId = tab.getAttribute("aria-controls");
    let tabPanel = getTabPanel(tab);
    toggleTabPanel(tabPanel, false);
}

export function getSelectedTabInGroup() {

}

class Tab {

    private tabList: HTMLElement;

    private tabs: NodeListOf<HTMLElement>;

    private tabListLabel: string;

    constructor(tablist: HTMLElement) {
        this.tabList = tablist;
        this.tabs = this.tabList.querySelectorAll('.tab');
        this.tabListLabel = this.tabList.ariaLabel;
    }
}