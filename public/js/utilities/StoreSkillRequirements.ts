import { SkillRequirementResource } from "../types/SkillRequirementResource";

export class StoreSkillRequirements {

    private SkillRequirements: SkillRequirementResource[] = [];
    private SkillRequirementsContainer: HTMLElement;

    constructor(SkillRequirementsContainer: HTMLElement, SkillRequirements: SkillRequirementResource[]) {
        this.SkillRequirementsContainer = SkillRequirementsContainer;
        this.SkillRequirements = SkillRequirements;
    }

    public generateContainer() {

        this.SkillRequirements.forEach(element => {
            // Create wrapper for each element
            let wrapper = document.createElement("div");
            wrapper.classList.add("skill-requirements-list-item");

            let img = document.createElement("img");
            img.src = "public/images/" + element.skill + " icon.png";
            img.width = 32;
            img.height = 32;
            wrapper.appendChild(img);

            let span = document.createElement("span");
            span.innerHTML = element.level.toString();
            wrapper.appendChild(span);
            this.SkillRequirementsContainer.appendChild(wrapper);
        });
    }

    public clearContainer() {
        while (this.SkillRequirementsContainer.firstChild) {
            this.SkillRequirementsContainer.removeChild(this.SkillRequirementsContainer.firstChild);
        }
    }
}