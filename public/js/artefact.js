function artefactAnimation() {
    let img1 = new Image(48, 48);
    let img2 = new Image(48, 48);
    let img3 = new Image(48, 48);
    let img4 = new Image(48, 48);
    let img5 = new Image(48, 48);
    img1.setAttribute("id", "img1");
    img1.setAttribute("class", "artefact_img");
    img2.setAttribute("id", "img2");
    img2.setAttribute("class", "artefact_img");
    img3.setAttribute("id", "img3");
    img3.setAttribute("class", "artefact_img");
    img4.setAttribute("id", "img4");
    img4.setAttribute("class", "artefact_img");
    img5.setAttribute("id", "img5");
    img5.setAttribute("class", "artefact_img");
    let elements = document.getElementsByClassName("artefact_img");
    for(var i = 0; i < elements.length; i++) {
        elements[i].style.position = "absolute";
    }
    
    let conversationContainer = document.getElementById("conversation");
    conversationContainer.insertBefore(img1, conversationContainer.children[0]);
    conversationContainer.insertBefore(img2, conversationContainer.children[0]);
    conversationContainer.insertBefore(img3, conversationContainer.children[0]);
    conversationContainer.insertBefore(img4, conversationContainer.children[0]);
    conversationContainer.insertBefore(img5, conversationContainer.children[0]);
    conversationContainer.querySelectorAll("ul")[0].style.visibility = "none";
    conversationContainer.querySelectorAll("button")[0].style.visibility = "none";
}
