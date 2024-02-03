import { AdvApi } from "../AdvApi";
import { Inventory } from "../clientScripts/inventory";
import { advAPIResponse } from "../types/Responses/AdvResponse";
import { GoldCostElement } from "../utilities/GoldCostElement";

const workforceLodgeModule: IWorkforceLodgeModule = {
    activeInfoSectionName: null,
    efficiencyLevelInfoElement: <HTMLElement>document.getElementById("upgrade-info-efficiency-level-el"),
    minerEfficiencyLevelElement: <HTMLElement>document.getElementById("miner-efficiency-level-el"),
    farmerEfficiencyLevelElement: <HTMLElement>document.getElementById("farmer-efficiency-level-el"),
    init() {
        let currentChecked = document.querySelector('input[name="efficiency-upgrade-profiency"]:checked');
        let val = <ActiveInfoSection>currentChecked.getAttribute("data-efficiency-upgrade-profiency");
        this.setActiveInfoSection(val);
        let radios = document.getElementsByName("efficiency-upgrade-profiency");
        radios.forEach(element => {
            element.addEventListener('change', (e) => {
                let element = <HTMLInputElement>e.currentTarget;
                if (element.checked) {
                    this.setActiveInfoSection(val);
                    this.setInfo(element);
                }
            });
        });

        document.getElementById("efficiency-upgrade-form").addEventListener("submit", () => this.upgradeEffiency());
    },
    setInfo(element: HTMLInputElement) {
        let upgradeCost = <ActiveInfoSection>element.getAttribute("data-efficiency-upgrade-cost");
        let efficiencyLevel = element.getAttribute("data-efficiency-level");
        this.setCurrentEfficiencyLevel(efficiencyLevel);
        let goldcastElementWrapper = <HTMLElement>document.getElementById("efficiency-upgrade-info").querySelectorAll(".gold-cost-wrapper")[0];
        let goldcostElement = new GoldCostElement(goldcastElementWrapper);
        goldcostElement.setGoldCost(parseInt(upgradeCost));

    },
    setActiveInfoSection(val: ActiveInfoSection) {
        this.activeInfoSectionName = val;
    },
    setCurrentEfficiencyLevel(level: string) {
        this.efficiencyLevelInfoElement.innerHTML = level;

        if (this.activeInfoSectionName === "miner") {
            this.minerEfficiencyLevelElement.innerHTML = level;
        } else {
            this.farmerEfficiencyLevelElement.innerHTML = level;
        }
    },
    upgradeEffiency() {
        const data: UpgradeEfficiencyRequest = {
            skill: this.activeInfoSectionName
        };

        let goldcastElementWrapper = <HTMLElement>document.getElementById("efficiency-upgrade-info").querySelectorAll(".gold-cost-wrapper")[0];
        let goldcostElement = new GoldCostElement(goldcastElementWrapper);

        AdvApi.post<UpgradeEfficiencyResponse>('/workforcelodge/efficiency/upgrade', data).then((response) => {
            Inventory.update();
            goldcostElement.setGoldCost(response.data.new_efficiency_price);
            this.setCurrentEfficiencyLevel(response.data.efficiency_level.toString());
        }).catch((error) => false);

    }
}

interface UpgradeEfficiencyRequest {
    skill: ActiveInfoSection;
}
export interface UpgradeEfficiencyResponse extends advAPIResponse {
    data: {
        efficiency_level: number,
        new_efficiency_price: number
    }
}

interface IWorkforceLodgeModule {
    activeInfoSectionName: ActiveInfoSection;
    efficiencyLevelInfoElement: HTMLElement;
    farmerEfficiencyLevelElement: HTMLElement;
    minerEfficiencyLevelElement: HTMLElement;
    init(): void;
    setInfo(element: HTMLInputElement): void;
    setCurrentEfficiencyLevel(level: string): void;
    setActiveInfoSection(val: ActiveInfoSection): void;
    upgradeEffiency(): void;
};

type ActiveInfoSection = "miner" | "farmer";
export default workforceLodgeModule