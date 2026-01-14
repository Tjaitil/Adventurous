import { AdvApi } from '@/AdvApi';
import type { ArmoryWarrior } from '@/types/Warrior';
import { isCropLocation, isMineLocation } from '@/utilities/GameLocations';
import { gameEventBus } from '@/gameEventsBus';
import {
  archeryShopDataLoader,
  bakeryDataLoader,
  cropsDataLoader,
  mineDataLoader,
  smithyDataLoader,
  travelbureauDataLoader,
  zinsStoreDataLoader,
} from '@/buildingScripts/buildingLoaders';
import type {
  ArcheryShopDataLoaderResponse,
  ArmoryDataLoaderResponse,
  BakeryDataLoaderResponse,
  CropsDataLoaderResponse,
  MineDataLoaderResponse,
  SmithyDataLoaderResponse,
  TravelbureauDataLoaderResponse,
  ZinsStoreDataLoaderResponse,
} from '@/types/BuildingDataLoader';

type CachedData = {
  armory: {
    cached_at: number;
  } & ArmoryDataLoaderResponse;
  workforcelodge: {
    cached_at: number;
    view: string;
  };
  bakery: {
    cached_at: number;
  } & BakeryDataLoaderResponse;
  stockpile: {
    cached_at: number;
    view: string;
  };
  travelbureau: {
    cached_at: number;
  } & TravelbureauDataLoaderResponse;
  smithy: {
    cached_at: number;
    view: string;
  } & SmithyDataLoaderResponse;
  mine: {
    cached_at: number;
    script: 'mine';
  } & MineDataLoaderResponse;
  crops: {
    cached_at: number;
    script: 'crops';
  } & CropsDataLoaderResponse;
  zinsstore: {
    cached_at: number;
    script: 'zinsstore';
  } & ZinsStoreDataLoaderResponse;
  archeryshop: {
    cached_at: number;
  } & ArcheryShopDataLoaderResponse;
};

class BuildingDataPreloader {
  private cache: Partial<CachedData> = {};
  private readonly CACHE_TTL = 5 * 60 * 1000;

  constructor() {
    gameEventBus.subscribe('CHANGED_LOCATION', ({ locationName }) => {
      if (isCropLocation(locationName)) {
        this.invalidateCache('crops');
        void this.preloadCrops();
      } else if (isMineLocation(locationName)) {
        this.invalidateCache('mine');
        void this.preloadMine();
      }
    });
  }

  private isDataValid(cacheKey: keyof CachedData): boolean {
    const cached = this.cache[cacheKey];
    if (!cached) return false;

    return Date.now() - cached.cached_at < this.CACHE_TTL;
  }

  private invalidateCache(cacheKey: keyof CachedData): void {
    this.cache[cacheKey] = undefined;
  }

  async preloadArmory(): Promise<void> {
    if (this.getBuildingCache('armory')) {
      return;
    }

    try {
      const warriors = await AdvApi.get<ArmoryWarrior[]>('/armory/soldiers');

      this.cache.armory = {
        cached_at: Date.now(),
        warriors,
      };
    } catch {
      return;
    }
  }

  async preloadWorkforceLodge(): Promise<void> {
    if (this.getBuildingCache('workforcelodge')) {
      return;
    }

    try {
      const viewResponse = await AdvApi.get<string>('/workforcelodge');

      this.cache.workforcelodge = {
        cached_at: Date.now(),
        view: viewResponse,
      };
    } catch {
      return;
    }
  }

  async preloadSmithy(): Promise<void> {
    if (this.isDataValid('smithy')) {
      return;
    }

    try {
      const [viewResponse, storeResponse] = await Promise.all([
        AdvApi.get<string>('/smithy'),
        smithyDataLoader.store_items(),
      ]);

      this.cache.smithy = {
        cached_at: Date.now(),
        view: viewResponse,
        ...storeResponse['data'],
      };
    } catch {
      return;
    }
  }

  async preloadBakery(): Promise<void> {
    if (this.isDataValid('bakery')) {
      return;
    }

    try {
      const storeResponse = await bakeryDataLoader.store_items();

      this.cache.bakery = {
        cached_at: Date.now(),
        ...storeResponse['data'],
      };
    } catch {
      return;
    }
  }

  async preloadArcheryShop(): Promise<void> {
    if (this.isDataValid('archeryshop')) {
      return;
    }

    try {
      const storeResponse = await archeryShopDataLoader.store_items();

      this.cache.archeryshop = {
        cached_at: Date.now(),
        ...storeResponse['data'],
      };
    } catch {
      return;
    }
  }

  async preloadTravelBureau(): Promise<void> {
    if (this.isDataValid('travelbureau')) {
      return;
    }

    try {
      const storeResponse = await travelbureauDataLoader.store_items();

      this.cache.travelbureau = {
        cached_at: Date.now(),
        ...storeResponse['data'],
      };
    } catch {
      return;
    }
  }

  async preloadZinsStore(): Promise<void> {
    if (this.isDataValid('zinsstore')) {
      return;
    }

    try {
      const storeResponse = await zinsStoreDataLoader.store_items();

      this.cache.zinsstore = {
        cached_at: Date.now(),
        script: 'zinsstore',
        ...storeResponse['data'],
      };
    } catch {
      return;
    }
  }

  async preloadCrops(): Promise<void> {
    if (this.isDataValid('crops')) {
      return;
    }

    try {
      const [skillActionDataResponse, countdownResponse] = await Promise.all([
        cropsDataLoader.action_items(),
        cropsDataLoader.countdown(),
      ]);

      this.cache.crops = {
        cached_at: Date.now(),
        script: 'crops',
        countdown: countdownResponse,
        action_items: skillActionDataResponse,
      };
    } catch {
      return;
    }
  }

  async preloadMine(): Promise<void> {
    if (this.isDataValid('mine')) {
      return;
    }

    try {
      const [skillActionDataResponse, countdownResponse] = await Promise.all([
        mineDataLoader.action_items(),
        mineDataLoader.countdown(),
      ]);

      this.cache.mine = {
        cached_at: Date.now(),
        script: 'mine',
        countdown: countdownResponse,
        action_items: skillActionDataResponse,
      };
    } catch {
      return;
    }
  }

  async preloadStockpile(): Promise<void> {
    if (this.getBuildingCache('stockpile')) {
      return;
    }

    try {
      const viewResponse = await AdvApi.get<string>('/stockpile');

      this.cache.stockpile = {
        cached_at: Date.now(),
        view: viewResponse,
      };
    } catch {
      return;
    }
  }

  getBuildingCache<T extends keyof CachedData>(
    string: T,
  ): CachedData[T] | undefined {
    if (!this.isDataValid(string)) {
      return undefined;
    }

    return this.cache[string];
  }

  getArmoryData() {
    if (!this.getBuildingCache('armory')) {
      return null;
    }
    return this.cache.armory;
  }

  async preloadAll(): Promise<void> {
    const preloadPromises = [
      this.preloadArmory(),
      this.preloadWorkforceLodge(),
      this.preloadSmithy(),
      this.preloadBakery(),
      this.preloadArcheryShop(),
      this.preloadTravelBureau(),
      this.preloadZinsStore(),
      this.preloadStockpile(),
      this.preloadCrops(),
      this.preloadMine(),
    ];

    await Promise.allSettled(preloadPromises);
  }

  preloadBuildings(buildings: (keyof CachedData)[]) {
    void buildings.map(building => {
      switch (building) {
        case 'armory':
          return this.preloadArmory();
        case 'workforcelodge':
          return this.preloadWorkforceLodge();
        case 'smithy':
          return this.preloadSmithy();
        case 'bakery':
          return this.preloadBakery();
        case 'archeryshop':
          return this.preloadArcheryShop();
        case 'travelbureau':
          return this.preloadTravelBureau();
        case 'zinsstore':
          return this.preloadZinsStore();
        case 'stockpile':
          return this.preloadStockpile();
        case 'crops':
          return this.preloadCrops();
        case 'mine':
          return this.preloadMine();
        default:
      }
    });
  }

  clearCache(): void {
    this.cache = {};
  }
}

export const buildingDataPreloader = new BuildingDataPreloader();
