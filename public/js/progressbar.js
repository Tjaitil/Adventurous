progressBar = {
    calculateProgress: function(progressBarElement, currentValue, maxValue, valuesInserted = true) {
        /* progressBarElement = div parent container for progressbar
         * currentValue = current value used in calculating progress
         * maxValue = max value that represent 100 %
         * valuesInserted = bolean value that determines wether currentValue / maxValue is already present in DOM or not
         */
        if(progressBarElement == null) {
            return false;
        }
        if(valuesInserted == false) {
            progressBarElement.querySelectorAll(".progressBar_currentValue")[0].innerHTML = currentValue;
            progressBarElement.querySelectorAll(".progressBar_maxValue")[0].innerHTML = maxValue;
        }
        else {
            currentValue = progressBarElement.querySelectorAll(".progressBar_currentValue")[0].innerHTML;
            maxValue = progressBarElement.querySelectorAll(".progressBar_maxValue")[0].innerHTML;
        }
        let width = (parseInt(currentValue) / parseInt(maxValue)) * 100;
        progressBarElement.querySelectorAll(".progressBarOverlay")[0].style.width = width + "%";
    }
};