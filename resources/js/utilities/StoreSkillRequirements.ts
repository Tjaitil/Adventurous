import { AssetPaths } from '../clientScripts/ImagePath';
import { SkillRequirementResource } from '../types/SkillRequirementResource';

export class StoreSkillRequirements {
    private SkillRequirements: SkillRequirementResource[] = [];
    private SkillRequirementsContainer: HTMLElement;

    constructor(
        SkillRequirementsContainer: HTMLElement,
        SkillRequirements: SkillRequirementResource[],
    ) {
        this.SkillRequirementsContainer = SkillRequirementsContainer;
        this.SkillRequirements = SkillRequirements;
    }

    public generateContainer() {
        this.SkillRequirements.forEach(element => {
            // Create wrapper for each element
            const wrapper = document.createElement('div');
            wrapper.classList.add('skill-requirements-list-item');

            const img = document.createElement('img');
            img.src = AssetPaths.getImagePath(element.skill + ' icon.png');
            img.width = 48;
            img.height = 48;
            img.style.marginInline = 'auto';
            wrapper.appendChild(img);

            const span = document.createElement('span');
            span.innerHTML = element.level.toString();
            wrapper.appendChild(span);
            this.SkillRequirementsContainer.appendChild(wrapper);
        });
    }

    public clearContainer() {
        while (this.SkillRequirementsContainer.firstChild) {
            this.SkillRequirementsContainer.removeChild(
                this.SkillRequirementsContainer.firstChild,
            );
        }
    }
}
