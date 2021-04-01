window.addEventListener("load", () => setTimeout(updateCountdownTab, 120000));

function updateCountdownTab() {
    let data = "model=SidebarUpdater" + "&method=calculateCountdowns";
    ajaxJS(data, function(response) {
        if(response[0] !== false) {
            document.getElementById("tab_2").innerHTML = response[1];
        }
    });
    setTimeout(updateCountdownTab, 120000);
}
function updateDiplomacyTab() {
    let data = "model=SidebarUpdater" + "&method=getDiplomacy";
    ajaxJS(data, function(response) {
        if(response[0] !== false) {
            let responseText = JSON.parse(response[1]);
            console.log(responseText);
            let trs = document.getElementById("tab_3").querySelectorAll("tbody")[0].querySelectorAll("tr");
            for(let i = 0; i < trs.length; i++) {
                let tdText = trs[i].querySelectorAll("td")[1].innerHTML;
                let numberDifference = parseFloat(tdText) - parseFloat(responseText[i]);
                trs[i].querySelectorAll("td")[1].innerText = responseText[i] + " (" + numberDifference + ")";
            }
        }
    });
}