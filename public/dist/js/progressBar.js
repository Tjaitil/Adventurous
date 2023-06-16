export class ProgressBar {
    progressBarElement;
    currentValue;
    currentValueElement;
    progressIndicator;
    progressElement;
    maxValue;
    maxValueElement;
    progressBarOverlayShadow;
    finishedClassToggled;
    /**
     *
     * @param element Representing by a HTMLElement or a id string
     * @param createElement If not false create a element with an id based on provided string
     * @param initialValues Object containing to set initial values
     */
    constructor(element, initialValues, createElement = false) {
        if (typeof createElement === "string") {
            this.progressBarElement = this.createProgressBar(createElement);
        }
        else {
            if (element instanceof HTMLElement) {
                this.progressBarElement = element;
            }
            else {
                let domElement = document.getElementById(element);
                this.progressBarElement = domElement;
            }
        }
        if (initialValues.finishedclass !== undefined) {
            this.finishedClassToggled = initialValues.finishedclass;
        }
        this.progressElement = this.progressBarElement.querySelectorAll(".progressBarOverlay")[0];
        this.progressBarOverlayShadow = (this.progressBarElement.querySelectorAll(".progressBarOverlayShadow")[0]);
        this.currentValueElement = (this.progressBarElement.querySelectorAll(".progressBar_currentValue")[0]);
        this.maxValueElement = this.progressBarElement.querySelectorAll(".progressBar_maxValue")[0];
        this.currentValue = initialValues.currentValue;
        this.maxValue = initialValues.maxValue;
        this.calculateProgress();
    }
    get isFinished() {
        return this.progressIndicator === 100;
    }
    setCurrentValue(newVal) {
        this.currentValueElement.innerHTML = newVal + "";
        this.currentValue = newVal;
        this.calculateProgress();
    }
    setMaxValue(newVal) {
        this.maxValue = newVal;
        this.maxValueElement.innerHTML = newVal + "";
        this.calculateProgress();
    }
    toggleFinishedClassValue(toggled) {
        this.finishedClassToggled = toggled;
    }
    calculateProgress() {
        if (this.progressBarElement == null)
            return false;
        this.progressBarElement.querySelectorAll(".progressBar")[0].classList.remove("progressFinished");
        this.progressIndicator = (this.currentValue / this.maxValue) * 100;
        if (this.progressIndicator > 100) {
            this.progressIndicator = 100;
        }
        let shadowLength = this.progressIndicator + 0.5;
        this.progressElement.getBoundingClientRect();
        if (this.progressIndicator >= 100 && this.finishedClassToggled === true) {
            this.progressIndicator = 100;
            this.progressBarElement.querySelectorAll(".progressBar")[0].classList.add("progressFinished");
        }
        // Update values
        this.progressElement.style.width = this.progressIndicator + "%";
        this.progressBarOverlayShadow.style.width = shadowLength + "%";
    }
    /**
     *
     * @param progressBarID Id to be given to the progressbar
     * @returns HTMLElement representing Progressbar
     */
    createProgressBar(progressBarID) {
        // Create container
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
}
