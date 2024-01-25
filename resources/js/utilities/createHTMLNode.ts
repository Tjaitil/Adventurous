const createHTMLNode = (htmlString: string): Node => {
    const element = document.createElement("div");

    if(htmlString === null || htmlString === undefined || htmlString.length === 0) {
        return element;
    }
    // Create an dummy element

    element.innerHTML = htmlString;

    return element.children[0];
}

export default createHTMLNode;