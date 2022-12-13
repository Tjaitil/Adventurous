export function getClientPageTitle(): string {
    let pageTitle = <HTMLElement>document.getElementsByClassName("page_title")[0];
    return pageTitle.innerText;
}
/* TOOD: Replace same calls with this */