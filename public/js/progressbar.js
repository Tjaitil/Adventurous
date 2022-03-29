const progressBar = {
    calculateProgress(progressBarElement, currentValue, maxValue, valuesInserted = true, finishClass = false) {
        /* progressBarElement = div parent container for progressbar
         * currentValue = current value used in calculating progress
         * maxValue = max value that represent 100 %
         * valuesInserted = bolean value that determines wether currentValue / maxValue is already present in DOM or not
         */
        if(progressBarElement == null) {
            return false;
        }
        // Fetch values from arguments
        if(valuesInserted == false) {
            progressBarElement.querySelectorAll(".progressBar_currentValue")[0].innerHTML = currentValue;
            progressBarElement.querySelectorAll(".progressBar_maxValue")[0].innerHTML = maxValue;
        }
        else {
            if(currentValue === false) {
                currentValue = progressBarElement.querySelectorAll(".progressBar_currentValue")[0].innerHTML;
            } else {
                progressBarElement.querySelectorAll(".progressBar_currentValue")[0].innerHTML = currentValue;
            }
            maxValue = progressBarElement.querySelectorAll(".progressBar_maxValue")[0].innerHTML;
        }
        let width = (parseInt(currentValue) / parseInt(maxValue)) * 100;
        let shadowLength = width + 0.5;
        progressBarElement.querySelectorAll(".progressBarOverlay")[0].style.width = "0%";
        progressBarElement.querySelectorAll(".progressBarOverlayShadow")[0].style.width = "0%";
        progressBarElement.querySelectorAll(".progressBarOverlay")[0].getBoundingClientRect();
        if(width > 100) {
            width = 100;
            shadowLength = 100;
            if(finishClass === true) progressBarElement.querySelectorAll(".progressBar")[0].classList.add("progressFinished");
        } else if(finishClass === false) {
            console.log(progressBarElement.querySelectorAll(".progressBar")[0]);
            progressBarElement.querySelectorAll(".progressBar")[0].removeAttribute(".progressFinished");
        }
        progressBarElement.querySelectorAll(".progressBarOverlay")[0].style.width = width + "%";
        progressBarElement.querySelectorAll(".progressBarOverlayShadow")[0].style.width = shadowLength + "%";
        return width;
    },
    createProgressBar(progressBarID) {
        let progressBarContainer = document.createElement("div");
        progressBarContainer.setAttribute("class", "progressBarContainer");
        progressBarContainer.setAttribute("id", progressBarID);
        progressBarContainer.style.width = "100%";
        let progressBarOverlayShadow = document.createElement("div");
        progressBarOverlayShadow.setAttribute("class", "progressBarOverlayShadow");
        
        let progressBarOverlay = document.createElement("div");
        progressBarOverlay.setAttribute("class", "progressBarOverlay");
        let progressBar = document.createElement("div");
        progressBar.setAttribute("class", "progressBar");
        let progressBar_currentValue = document.createElement("span");
        progressBar_currentValue.setAttribute("class", "progressBar_currentValue");
        let progressBar_maxValue = document.createElement("span");
        let space = document.createTextNode("\u00A0");
        let slash = document.createTextNode(" /");
        progressBar_maxValue.setAttribute("class", "progressBar_maxValue");
        // append children of progressbar
        progressBar.appendChild(progressBar_currentValue);
        progressBar.appendChild(space);
        progressBar.appendChild(space);
        progressBar.appendChild(slash);
        progressBar.appendChild(space);
        progressBar.appendChild(space);
        progressBar.appendChild(progressBar_maxValue);
        
        // Append children of progressBarContainer
        progressBarContainer.appendChild(progressBarOverlayShadow);
        progressBarContainer.appendChild(progressBarOverlay);
        progressBarContainer.appendChild(progressBar);

        return progressBarContainer;
    }
};