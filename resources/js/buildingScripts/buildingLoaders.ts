import { AdvApi } from '@/AdvApi';
import type {
  ArcheryShopDataLoader,
  ArmoryDataLoader,
  MineDataLoader,
  SmithyDataLoader,
  BakeryDataLoader,
  TravelbureauDataLoader,
  ZinsStoreDataLoader,
  CropsDataLoader,
} from '@/types/BuildingDataLoader';

export const smithyDataLoader: SmithyDataLoader = {
  store_items: () => AdvApi.get('/smithy/store'),
};

export const armoryDataLoader: ArmoryDataLoader = {
  warriors: () => AdvApi.get('/armory/soldiers'),
};

export const travelbureauDataLoader: TravelbureauDataLoader = {
  store_items: () => AdvApi.get('/travelbureau/store'),
};

export const bakeryDataLoader: BakeryDataLoader = {
  store_items: () => AdvApi.get('/bakery/store'),
};

export const archeryShopDataLoader: ArcheryShopDataLoader = {
  store_items: () => AdvApi.get('/archeryshop/store'),
};

export const zinsStoreDataLoader: ZinsStoreDataLoader = {
  store_items: () => AdvApi.get('/zinsstore/store'),
};

export const mineDataLoader: MineDataLoader = {
  action_items: () => AdvApi.get('/mine/data'),
  countdown: () => AdvApi.get('/mine/countdown'),
};

export const cropsDataLoader: CropsDataLoader = {
  action_items: () => AdvApi.get('/crops/data'),
  countdown: () => AdvApi.get('/crops/countdown'),
};
