PRAGMA synchronous = OFF;
PRAGMA journal_mode = MEMORY;
BEGIN TRANSACTION;
CREATE TABLE `adventure` (
  `username` varchar(10000) NOT NULL
,  `adventure_id` integer NOT NULL DEFAULT '0'
,  `adventure_countdown` datetime NOT NULL DEFAULT current_timestamp
,  `adventure_status` integer NOT NULL DEFAULT '0'
,  `reward` integer NOT NULL
,  `notification` integer NOT NULL
);
CREATE TABLE `adventure_battles` (
  `adventure_id` integer NOT NULL
,  `daqloon_damage` integer NOT NULL
,  `warrior_damage` integer NOT NULL
,  `daqloon_wounded` integer NOT NULL
,  `warrior_wounded` integer NOT NULL
,  `daqloon_combo` integer NOT NULL
,  `warrior_combo` integer NOT NULL
);
CREATE TABLE `adventure_crystals` (
  `difficulty` varchar(10000) NOT NULL
,  `location` varchar(10000) NOT NULL
,  `min_chance` integer NOT NULL
,  `max_chance` integer NOT NULL
);
CREATE TABLE `adventure_req_items` (
  `item_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(1000) NOT NULL
,  `difficulty` integer NOT NULL
,  `amount_min` integer NOT NULL
,  `amount_max` integer NOT NULL
,  `role` varchar(10000) NOT NULL
,  UNIQUE (`item_id`)
,  CONSTRAINT `fk_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `adventure_requests` (
  `request_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `sender` varchar(10000) NOT NULL
,  `receiver` varchar(10000) NOT NULL
,  `adventure_id` integer NOT NULL
,  `role` varchar(1000) NOT NULL
,  `method` varchar(100) NOT NULL
,  `request_date` timestamp NOT NULL DEFAULT current_timestamp
,  `status` text NOT NULL
,  UNIQUE (`request_id`)
);
CREATE TABLE `adventure_requirements` (
  `adventure_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(10000) NOT NULL
,  `role` varchar(1000) NOT NULL
,  `required` varchar(1000) NOT NULL
,  `amount` decimal(10,0) NOT NULL
,  `provided` integer NOT NULL DEFAULT '0'
,  `status` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `adventure_rewards` (
  `role` varchar(1000) NOT NULL
,  `difficulty` varchar(1000) NOT NULL
,  `location` varchar(1000) NOT NULL
,  `item` varchar(1000) NOT NULL
,  `min_amount` integer NOT NULL
,  `max_amount` integer NOT NULL
);
CREATE TABLE `adventures` (
  `adventure_id` integer NOT NULL
,  `difficulty` varchar(1000) NOT NULL DEFAULT 'none'
,  `location` varchar(1000) NOT NULL DEFAULT 'none'
,  `adventure_leader` varchar(10000) NOT NULL
,  `farmer` varchar(1000) NOT NULL DEFAULT 'none'
,  `miner` varchar(1000) NOT NULL DEFAULT 'none'
,  `trader` varchar(1000) NOT NULL DEFAULT 'none'
,  `warrior` varchar(1000) NOT NULL DEFAULT 'none'
,  `adventure_status` integer NOT NULL DEFAULT '0'
,  `adventure_countdown` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
,  `battle_result` integer NOT NULL
,  `other_invite` integer NOT NULL
);
CREATE TABLE `adventures_data` (
  `location` varchar(10000) NOT NULL
,  `difficulty` varchar(10000) NOT NULL
,  `time` integer NOT NULL
,  `user_xp` integer NOT NULL
,  `warrior_xp_min` integer NOT NULL
,  `warrior_xp_max` integer NOT NULL
);
CREATE TABLE `adventures_farmer` (
  `adventure_id` integer NOT NULL
,  `username` varchar(1000) NOT NULL
,  `provided` varchar(10000) NOT NULL DEFAULT '0'
,  `status` integer NOT NULL DEFAULT '0'
,  `reward` integer DEFAULT '0'
);
CREATE TABLE `adventures_miner` (
  `adventure_id` integer NOT NULL
,  `username` varchar(1000) NOT NULL
,  `provided` text NOT NULL
,  `status` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `adventures_trader` (
  `adventure_id` integer NOT NULL
,  `username` varchar(1000) NOT NULL
,  `provided` text NOT NULL
,  `status` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `adventures_warrior` (
  `adventure_id` integer NOT NULL
,  `username` varchar(1000) NOT NULL
,  `provided` text NOT NULL
,  `status` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `archery_shop_items` (
  `id` integer NOT NULL
,  `item_id` integer NOT NULL
,  `item` varchar(254) NOT NULL
,  `item_multiplier` integer  DEFAULT NULL
,  `store_value` integer  NOT NULL
,  PRIMARY KEY (`id`)
,  UNIQUE (`item`)
,  CONSTRAINT `archery_shop_item_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`)
,  CONSTRAINT `archery_shop_item_FK_1` FOREIGN KEY (`item`) REFERENCES `items` (`name`)
);
CREATE TABLE `archery_shop_items_required` (
  `item_id` integer NOT NULL
,  `required_item` varchar(254) NOT NULL
,  `amount` integer NOT NULL
,  `id` integer NOT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `armory_items_data` (
  `item_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `item` varchar(254) NOT NULL
,  `mineral_required` integer NOT NULL
,  `wood_required` integer NOT NULL
,  `level` integer NOT NULL
,  `attack` integer NOT NULL DEFAULT '0'
,  `defence` integer NOT NULL DEFAULT '0'
,  `price` integer NOT NULL
,  `type` varchar(10000) NOT NULL DEFAULT 'none'
,  `warrior_type` varchar(100) NOT NULL DEFAULT 'all'
,  UNIQUE (`item_id`)
,  CONSTRAINT `FK_smithy_data_items_name` FOREIGN KEY (`item`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `FK_smity_data_items` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `army_missions` (
  `mission_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `required_warriors` integer NOT NULL
,  `mission` text NOT NULL
,  `difficulty` varchar(1000) NOT NULL
,  `reward` text NOT NULL
,  `time` integer NOT NULL
,  `date` datetime NOT NULL DEFAULT current_timestamp
,  `combat` integer NOT NULL DEFAULT '0'
,  `location` varchar(10000) NOT NULL
);
CREATE TABLE `army_missions_active` (
  `username` varchar(1000) NOT NULL
,  `mission_id` integer NOT NULL
,  `mission_countdown` datetime NOT NULL
);
CREATE TABLE `assignment_types` (
  `type` varchar(10000) NOT NULL
,  `xp` integer NOT NULL
,  `time` integer NOT NULL
,  `per_cargo_xp` integer NOT NULL
);
CREATE TABLE `city_relations` (
  `city` varchar(10000) NOT NULL
,  `hirtam` decimal(6,2) NOT NULL
,  `pvitul` decimal(6,2) NOT NULL
,  `khanz` decimal(6,2) NOT NULL
,  `ter` decimal(6,2) NOT NULL
,  `fansalplains` decimal(6,2) NOT NULL
);
CREATE TABLE `company_unit` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `unit` varchar(100) DEFAULT NULL
,  `parent_unit_id` integer  DEFAULT NULL
);
CREATE TABLE `conversation_trackers` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `conversation_option_value` varchar(100) DEFAULT NULL
,  `user_id` integer  NOT NULL
,  `current_index` varchar(100) DEFAULT NULL
,  `updated_at` datetime DEFAULT NULL
,  `created_at` datetime DEFAULT NULL
,  CONSTRAINT `conversation_trackers_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `crops_data` (
  `crop_type` varchar(1000) NOT NULL
,  `farmer_level` integer NOT NULL
,  `time` integer NOT NULL
,  `experience` integer NOT NULL
,  `seed_required` integer NOT NULL
,  `seed_item` varchar(256) NOT NULL
,  `min_crop_count` integer NOT NULL
,  `max_crop_count` integer NOT NULL
,  `location` varchar(10000) NOT NULL
,  PRIMARY KEY (`crop_type`)
,  CONSTRAINT `FK_crops_data_item_name` FOREIGN KEY (`crop_type`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `FK_crops_data_seed_item` FOREIGN KEY (`seed_item`) REFERENCES `items` (`name`)
);
CREATE TABLE `daqloon_stats` (
  `location` varchar(1000) NOT NULL
,  `difficulty` varchar(1000) NOT NULL
,  `attack` integer NOT NULL
,  `defence` integer NOT NULL
,  `amount` varchar(1000) NOT NULL
);
CREATE TABLE `diplomacy` (
  `id` integer NOT NULL
,  `username` varchar(10000) NOT NULL
,  `hirtam` decimal(6,2) NOT NULL DEFAULT '1.00'
,  `pvitul` decimal(6,2) NOT NULL DEFAULT '1.00'
,  `khanz` decimal(6,2) NOT NULL DEFAULT '1.00'
,  `ter` decimal(6,2) NOT NULL DEFAULT '1.00'
,  `fansalplains` decimal(6,2) NOT NULL DEFAULT '1.00'
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK_diplomacy_user_id` FOREIGN KEY (`id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `efficiency_upgrades` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `level` integer  DEFAULT NULL
,  `price` integer  DEFAULT NULL
);
CREATE TABLE `escrow` (
  `id` integer NOT NULL
,  `offeror` varchar(10000) NOT NULL
,  `item` varchar(10000) NOT NULL
,  `amount` integer NOT NULL
,  `date_inserted` timestamp NOT NULL DEFAULT current_timestamp
);
CREATE TABLE `failed_jobs` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `uuid` varchar(255) NOT NULL
,  `connection` text NOT NULL
,  `queue` text NOT NULL
,  `payload` longtext NOT NULL
,  `exception` longtext NOT NULL
,  `failed_at` timestamp NOT NULL DEFAULT current_timestamp
,  UNIQUE (`uuid`)
);
CREATE TABLE `farmer` (
  `username` varchar(1000) NOT NULL
,  `fields` integer NOT NULL DEFAULT '2'
,  `crop_type` varchar(1000) DEFAULT NULL
,  `crop_quant` integer NOT NULL DEFAULT '0'
,  `crop_finishes_at` datetime DEFAULT current_timestamp
,  `location` varchar(10000) NOT NULL
,  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `user_id` integer  NOT NULL
,  CONSTRAINT `farmer_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `farmer_workforce` (
  `username` varchar(1000) NOT NULL
,  `workforce_total` integer NOT NULL DEFAULT '4'
,  `avail_workforce` integer NOT NULL DEFAULT '4'
,  `towhar` integer NOT NULL DEFAULT '0'
,  `krasnur` integer NOT NULL
,  `efficiency_level` integer NOT NULL DEFAULT '1'
,  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `user_id` integer  NOT NULL
,  CONSTRAINT `farmer_workforce_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `favor_assignments` (
  `favor_id` integer NOT NULL
,  `username` varchar(10000) NOT NULL
,  `base` varchar(1000) NOT NULL
,  `destination` varchar(1000) NOT NULL
,  `cargo` varchar(1000) NOT NULL
,  `cargo_amount` integer NOT NULL
);
CREATE TABLE `healing_items` (
  `item_id` integer NOT NULL
,  `item` varchar(10000) NOT NULL
,  `price` integer NOT NULL
,  `heal` integer NOT NULL
,  `bakery_item` integer NOT NULL DEFAULT '1'
,  UNIQUE (`item_id`)
);
CREATE TABLE `healing_items_required` (
  `item_id` integer NOT NULL
,  `required_item` varchar(10000) NOT NULL
,  `amount` integer NOT NULL
,  `id` integer NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `healing_items_required_FK` FOREIGN KEY (`item_id`) REFERENCES `healing_items` (`item_id`)
);
CREATE TABLE `hunger` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `current` integer NOT NULL
,  `user_id` integer  DEFAULT NULL
);
CREATE TABLE `inventory` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(10000) NOT NULL
,  `item` varchar(10000) NOT NULL
,  `amount` integer NOT NULL
);
CREATE TABLE `item_prices` (
  `item` varchar(10000) NOT NULL
,  `week_amount` integer NOT NULL DEFAULT '0'
,  `week_price` decimal(10,4) NOT NULL DEFAULT '0.0000'
);
CREATE TABLE `items` (
  `item_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(254) NOT NULL
,  `store_value` integer NOT NULL DEFAULT '0'
,  `in_game` integer NOT NULL
,  `towhar_rate` integer NOT NULL DEFAULT '1'
,  `golbak_rate` integer NOT NULL DEFAULT '1'
,  `snerpiir_rate` integer NOT NULL DEFAULT '1'
,  `cruendo_rate` integer NOT NULL DEFAULT '1'
,  `pvitul_rate` integer NOT NULL DEFAULT '1'
,  `khanz_rate` integer NOT NULL DEFAULT '1'
,  `ter_rate` integer NOT NULL DEFAULT '1'
,  `krasnur_rate` integer NOT NULL DEFAULT '1'
,  `hirtam_rate` integer NOT NULL DEFAULT '1'
,  `fansal_plains_rate` integer NOT NULL DEFAULT '1'
,  `tasnobil_rate` integer NOT NULL DEFAULT '1'
,  `trader_assignment_type` varchar(1000) NOT NULL DEFAULT 'small trade'
,  `adventure_requirement` integer NOT NULL DEFAULT '0'
,  `adventure_requirement_difficulty` varchar(1000) NOT NULL DEFAULT '1'
,  `adventure_requirement_role` varchar(1000) NOT NULL
,  UNIQUE (`name`)
);
CREATE TABLE `laboratory_upgrades` (
  `profiency` varchar(10000) NOT NULL
,  `type` varchar(10000) NOT NULL
,  `level` integer NOT NULL
,  `cost` integer NOT NULL
);
CREATE TABLE `level_data` (
  `level` integer NOT NULL
,  `next_Level` integer DEFAULT NULL
,  `max_farm_workers` integer DEFAULT NULL
,  `max_mine_workers` integer DEFAULT NULL
,  `max_warriors` integer DEFAULT NULL
,  `max_efficiency_level` integer DEFAULT NULL
,  UNIQUE (`level`)
);
CREATE TABLE `levelup_data` (
  `level` integer NOT NULL
,  `profiency` varchar(10000) NOT NULL
,  `image` text NOT NULL
,  `unlocked` text NOT NULL
);
CREATE TABLE `market_box` (
  `id` integer NOT NULL
,  `username` varchar(10000) NOT NULL
,  `item` varchar(10000) NOT NULL
,  `amount` integer NOT NULL
);
CREATE TABLE `merchant_offer_times` (
  `username` varchar(100) NOT NULL
,  `date` date NOT NULL
);
CREATE TABLE `merchant_offers` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `location` varchar(50) NOT NULL
,  `item` varchar(50) NOT NULL
,  `store_value` integer NOT NULL
,  `store_buy_price` integer NOT NULL
,  `amount` integer NOT NULL
,  `date_inserted` datetime NOT NULL DEFAULT current_timestamp
,  UNIQUE (`id`)
);
CREATE TABLE `merchants` (
  `item_id` integer NOT NULL
,  `item` varchar(1000) NOT NULL
,  `amount` integer NOT NULL
,  `want` varchar(10000) NOT NULL
,  `want_amount` integer NOT NULL
,  `location` varchar(100) NOT NULL
);
CREATE TABLE `messages` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `title` varchar(10000) NOT NULL
,  `sender` varchar(1000) NOT NULL
,  `receiver` varchar(10000) NOT NULL
,  `message` mediumtext NOT NULL
,  `date` datetime NOT NULL DEFAULT current_timestamp
,  `is_message_read` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `migrations` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `migration` varchar(255) NOT NULL
,  `batch` integer NOT NULL
);
CREATE TABLE `miner` (
  `username` varchar(10000) NOT NULL
,  `mineral_ore` varchar(10000) DEFAULT NULL
,  `mining_finishes_at` datetime NOT NULL DEFAULT current_timestamp
,  `permits` integer NOT NULL DEFAULT '30'
,  `location` varchar(1000) NOT NULL
,  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `user_id` integer  NOT NULL
,  CONSTRAINT `miner_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);
CREATE TABLE `miner_permit_cost` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `location` varchar(100) DEFAULT NULL
,  `permit_cost` integer  DEFAULT NULL
,  `permit_amount` integer DEFAULT NULL
);
CREATE TABLE `miner_workforce` (
  `username` varchar(10000) NOT NULL
,  `workforce_total` integer NOT NULL DEFAULT '2'
,  `avail_workforce` integer NOT NULL DEFAULT '2'
,  `golbak` integer NOT NULL DEFAULT '0'
,  `snerpiir` integer NOT NULL
,  `mineral_quant_level` integer NOT NULL DEFAULT '1'
,  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `efficiency_level` integer  DEFAULT '1'
,  `user_id` integer  NOT NULL
,  CONSTRAINT `miner_workforce_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);
CREATE TABLE `minerals` (
  `mineral_type` varchar(10000) NOT NULL
,  `mineral_ore` varchar(100) NOT NULL DEFAULT 'iron ore'
,  `miner_level` integer NOT NULL
,  `experience` integer NOT NULL
,  `time` integer NOT NULL
,  `min_per_period` integer NOT NULL
,  `max_per_period` integer NOT NULL
,  `permit_cost` integer NOT NULL
,  `location` varchar(1000) NOT NULL
,  `id` integer  NOT NULL
,  PRIMARY KEY (`mineral_type`)
,  CONSTRAINT `FK_minerals_data_mineral_ore` FOREIGN KEY (`mineral_ore`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `offer_records` (
  `id` integer NOT NULL
,  `username` varchar(1000) NOT NULL
,  `type` varchar(1000) NOT NULL
,  `item` varchar(1000) NOT NULL
,  `amount` integer NOT NULL
,  `price_ea` integer NOT NULL
,  `time` timestamp NOT NULL DEFAULT current_timestamp
);
CREATE TABLE `offers` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `offeror` varchar(10000) NOT NULL
,  `item` varchar(10000) NOT NULL
,  `amount` integer NOT NULL
,  `price_ea` integer NOT NULL
,  `type` varchar(1000) NOT NULL
,  `progress` integer NOT NULL DEFAULT '0'
,  `amount_left` integer NOT NULL
,  `box_item` varchar(100) DEFAULT 'none'
,  `box_amount` integer NOT NULL DEFAULT '0'
,  UNIQUE (`id`)
);
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL
,  `token` varchar(255) NOT NULL
,  `created_at` timestamp NULL DEFAULT NULL
,  PRIMARY KEY (`email`)
);
CREATE TABLE `personal_access_tokens` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `tokenable_type` varchar(255) NOT NULL
,  `tokenable_id` integer  NOT NULL
,  `name` varchar(255) NOT NULL
,  `token` varchar(64) NOT NULL
,  `abilities` text COLLATE BINARY
,  `last_used_at` timestamp NULL DEFAULT NULL
,  `expires_at` timestamp NULL DEFAULT NULL
,  `created_at` timestamp NULL DEFAULT NULL
,  `updated_at` timestamp NULL DEFAULT NULL
,  UNIQUE (`token`)
);
CREATE TABLE `persons` (
  `location` varchar(10000) NOT NULL
,  `name` varchar(10000) NOT NULL
);
CREATE TABLE `public_chat` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `time` timestamp NOT NULL DEFAULT current_timestamp
,  `clock` time NOT NULL
,  `username` varchar(10000) NOT NULL
,  `message` text NOT NULL
,  UNIQUE (`id`)
);
CREATE TABLE `skill_requirements` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `item` varchar(1000) NOT NULL
,  `skill` varchar(1000) NOT NULL
,  `level` integer NOT NULL
);
CREATE TABLE `smithy_items` (
  `item_id` integer NOT NULL
,  `item` varchar(1000) NOT NULL
,  `store_value` integer NOT NULL
,  `mineral` varchar(1000) NOT NULL
,  `item_multiplier` integer NOT NULL DEFAULT '1'
,  CONSTRAINT `FK_smithy_items_data_item` FOREIGN KEY (`item`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `FK_smithy_items_data_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `smithy_items_required` (
  `item_id` integer NOT NULL
,  `required_item` varchar(1000) DEFAULT NULL
,  `amount` integer NOT NULL
,  CONSTRAINT `FK_smithy_items_required_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `FK_smithy_items_required_item_required` FOREIGN KEY (`required_item`) REFERENCES `items` (`name`)
);
CREATE TABLE `stockpile` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(10000) NOT NULL
,  `item` varchar(1000) NOT NULL
,  `amount` integer NOT NULL
);
CREATE TABLE `store_discounts` (
  `store` varchar(100) NOT NULL
,  `discount` float NOT NULL
,  `profiency` varchar(100) NOT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
CREATE TABLE `tavern_prices` (
  `type` varchar(10000) NOT NULL
,  `price` integer NOT NULL
);
CREATE TABLE `tavern_times` (
  `username` varchar(10000) NOT NULL
,  `new_workers` date DEFAULT '0000-00-00'
,  `towhar` integer NOT NULL DEFAULT '0'
,  `krasnur` integer NOT NULL DEFAULT '0'
,  `snerpiir` integer NOT NULL DEFAULT '0'
,  `golbak` integer NOT NULL DEFAULT '0'
,  `tasnobil` integer NOT NULL DEFAULT '0'
,  `cruendo` integer NOT NULL DEFAULT '0'
,  `fagna` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `tavern_workers` (
  `username` varchar(10000) NOT NULL
,  `city` varchar(10000) NOT NULL
,  `type` varchar(1000) NOT NULL
,  `level` integer NOT NULL
);
CREATE TABLE `telescope_entries` (
  `sequence` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `uuid` char(36) NOT NULL
,  `batch_id` char(36) NOT NULL
,  `family_hash` varchar(255) DEFAULT NULL
,  `should_display_on_index` integer NOT NULL DEFAULT '1'
,  `type` varchar(20) NOT NULL
,  `content` longtext NOT NULL
,  `created_at` datetime DEFAULT NULL
,  UNIQUE (`uuid`)
);
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) NOT NULL
,  `tag` varchar(255) NOT NULL
,  PRIMARY KEY (`entry_uuid`,`tag`)
,  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
);
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) NOT NULL
,  PRIMARY KEY (`tag`)
);
CREATE TABLE `trader` (
  `username` varchar(10000) NOT NULL
,  `assignment_id` integer NOT NULL DEFAULT '0'
,  `cart_id` integer NOT NULL
,  `cart_amount` integer NOT NULL DEFAULT '0'
,  `delivered` integer NOT NULL DEFAULT '0'
,  `id` integer  NOT NULL
,  `trading_countdown` datetime DEFAULT NULL
,  CONSTRAINT `trader_FK` FOREIGN KEY (`cart_id`) REFERENCES `travelbureau_carts` (`id`)
);
CREATE TABLE `trader_assignment_types` (
  `id` integer  NOT NULL
,  `type` varchar(100) NOT NULL
,  `xp_per_cargo` float NOT NULL
,  `item_reward_amount` integer  NOT NULL
,  `xp_finished` integer  NOT NULL
,  `diplomacy_percentage` float NOT NULL
,  `currency_reward_amount` integer  NOT NULL
,  `required_level` integer NOT NULL
,  `xp_started` integer  NOT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `trader_assignments` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `base` varchar(10000) NOT NULL
,  `destination` varchar(10000) NOT NULL
,  `cargo` text NOT NULL
,  `assignment_amount` varchar(1000) NOT NULL
,  `time` integer NOT NULL
,  `assignment_type` varchar(100) NOT NULL
,  `date_inserted` datetime NOT NULL DEFAULT current_timestamp
,  UNIQUE (`id`)
);
CREATE TABLE `trader_data` (
  `level` integer NOT NULL
,  `capasity` integer NOT NULL
);
CREATE TABLE `training_type_data` (
  `training_type` varchar(10000) NOT NULL
,  `time` integer NOT NULL
,  `experience` integer NOT NULL
);
CREATE TABLE `travel_times` (
  `city` varchar(1000) NOT NULL
,  `location_x` varchar(1000) NOT NULL
,  `location_y` varchar(1000) NOT NULL
);
CREATE TABLE `travelbureau_carts` (
  `name` varchar(1000) NOT NULL
,  `wheel` varchar(1000) NOT NULL
,  `wood` varchar(10000) NOT NULL
,  `store_value` integer NOT NULL
,  `capasity` integer NOT NULL
,  `towhar` integer NOT NULL
,  `golbak` integer NOT NULL
,  `mineral_amount` integer NOT NULL
,  `wood_amount` integer NOT NULL
,  `item_id` integer DEFAULT NULL
,  `id` integer NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `travelbureau_carts_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`)
,  CONSTRAINT `travelbureau_carts_FK_1` FOREIGN KEY (`name`) REFERENCES `items` (`name`)
);
CREATE TABLE `travelbureau_carts_req_items` (
  `required_item` varchar(1000) NOT NULL
,  `amount` integer NOT NULL
,  `item_id` integer DEFAULT NULL
,  CONSTRAINT `FK_travelburea_item_name` FOREIGN KEY (`required_item`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `travelbureau_carts_req_items_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`)
);
CREATE TABLE `travelbureau_horses` (
  `type` varchar(1000) NOT NULL
,  `value` integer NOT NULL
,  `towhar` integer NOT NULL
,  `golbak` integer NOT NULL
,  `snerpiir` integer NOT NULL
,  `speed` integer NOT NULL
);
CREATE TABLE `user_data` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(1000) NOT NULL
,  `location` varchar(1000) NOT NULL DEFAULT 'tutorial island'
,  `map_location` varchar(10) NOT NULL DEFAULT '9.9'
,  `game_id` varchar(1000) NOT NULL
,  `session_id` integer NOT NULL
,  `destination` varchar(1000) NOT NULL DEFAULT 'none'
,  `arrive_time` datetime NOT NULL DEFAULT current_timestamp
,  `profiency` varchar(1000) NOT NULL DEFAULT 'none'
,  `horse` varchar(10000) NOT NULL DEFAULT 'none'
,  `artefact` varchar(1000) NOT NULL DEFAULT 'none'
,  `hunger` integer NOT NULL DEFAULT '100'
,  `hunger_date` timestamp NOT NULL DEFAULT current_timestamp
,  `frajrite_items` integer NOT NULL
,  `wujkin_items` integer NOT NULL
,  `stockpile_max_amount` integer DEFAULT '60'
);
CREATE TABLE `user_levels` (
  `id` integer NOT NULL
,  `username` varchar(1000) NOT NULL
,  `adventurer_respect` decimal(6,2) NOT NULL DEFAULT '1.00'
,  `farmer_level` integer NOT NULL DEFAULT '1'
,  `farmer_xp` integer NOT NULL DEFAULT '0'
,  `miner_level` integer NOT NULL DEFAULT '1'
,  `miner_xp` integer NOT NULL DEFAULT '0'
,  `trader_level` integer NOT NULL DEFAULT '1'
,  `trader_xp` integer NOT NULL DEFAULT '0'
,  `warrior_level` integer NOT NULL DEFAULT '1'
,  `warrior_xp` integer NOT NULL DEFAULT '0'
,  `user_id` integer DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK_user_id` FOREIGN KEY (`id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `users` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(255) NOT NULL
,  `email` varchar(255) NOT NULL
,  `email_verified_at` timestamp NULL DEFAULT NULL
,  `password` varchar(255) NOT NULL
,  `remember_token` varchar(100) DEFAULT NULL
,  `created_at` timestamp NULL DEFAULT NULL
,  `updated_at` timestamp NULL DEFAULT NULL
,  UNIQUE (`email`)
);
CREATE TABLE `warrior` (
  `username` varchar(10000) NOT NULL
,  `warrior_amount` integer NOT NULL DEFAULT '2'
,  `mission_id` integer NOT NULL DEFAULT '0'
,  `mission_countdown` datetime NOT NULL
);
CREATE TABLE `warriors` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(10000) NOT NULL
,  `warrior_id` integer NOT NULL
,  `type` varchar(1000) NOT NULL DEFAULT 'melee'
,  `training_countdown` datetime NOT NULL DEFAULT current_timestamp
,  `is_training` integer DEFAULT '0'
,  `training_type` varchar(10000) DEFAULT 'none'
,  `army_mission` integer NOT NULL DEFAULT '0'
,  `health` integer NOT NULL DEFAULT '100'
,  `location` varchar(100) NOT NULL DEFAULT 'tasnobil'
,  `is_resting` integer NOT NULL DEFAULT '0'
,  `rest_start` datetime NOT NULL DEFAULT current_timestamp
,  `user_id` integer  NOT NULL
,  UNIQUE (`warrior_id`,`user_id`)
,  CONSTRAINT `warriors_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);
CREATE TABLE `warriors_armory` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `username` varchar(1000) NOT NULL
,  `warrior_id` integer NOT NULL
,  `helm` varchar(1000) DEFAULT 'none'
,  `ammunition` varchar(10000) DEFAULT 'none'
,  `ammunition_amount` integer NOT NULL DEFAULT '0'
,  `body` varchar(1000) DEFAULT 'none'
,  `right_hand` varchar(1000) DEFAULT 'none'
,  `left_hand` varchar(1000) DEFAULT 'none'
,  `legs` varchar(1000) DEFAULT 'none'
,  `boots` varchar(1000) DEFAULT 'none'
,  `attack` integer NOT NULL DEFAULT '10'
,  `defence` integer NOT NULL DEFAULT '12'
);
CREATE TABLE `warriors_levels` (
  `id` integer NOT NULL
,  `username` varchar(10000) NOT NULL
,  `warrior_id` integer NOT NULL
,  `stamina_level` integer NOT NULL DEFAULT '1'
,  `stamina_xp` integer DEFAULT '0'
,  `technique_level` integer NOT NULL DEFAULT '1'
,  `technique_xp` integer DEFAULT '0'
,  `precision_level` integer NOT NULL DEFAULT '1'
,  `precision_xp` integer DEFAULT '0'
,  `strength_level` integer NOT NULL DEFAULT '1'
,  `strength_xp` integer DEFAULT '0'
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK_ID` FOREIGN KEY (`id`) REFERENCES `warriors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `warriors_levels_data` (
  `skill_level` integer NOT NULL
,  `next_level` integer NOT NULL
);
CREATE TABLE `workplace_data` (
  `level` integer NOT NULL
,  `max_workers` integer NOT NULL
,  `skill` varchar(10000) NOT NULL
,  `cost` integer NOT NULL
);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2024_01_22_180008_create_adventure_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2024_01_22_180008_create_adventure_battles_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2024_01_22_180008_create_adventure_crystals_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2024_01_22_180008_create_adventure_req_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_01_22_180008_create_adventure_requests_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_01_22_180008_create_adventure_requirements_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_01_22_180008_create_adventure_rewards_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2024_01_22_180008_create_adventures_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_01_22_180008_create_adventures_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2024_01_22_180008_create_adventures_farmer_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2024_01_22_180008_create_adventures_miner_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_01_22_180008_create_adventures_trader_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_01_22_180008_create_adventures_warrior_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_01_22_180008_create_archery_shop_item_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_01_22_180008_create_archery_shop_item_required_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_01_22_180008_create_armory_items_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_01_22_180008_create_army_missions_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_01_22_180008_create_army_missions_active_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_01_22_180008_create_assignment_types_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2024_01_22_180008_create_city_relations_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_01_22_180008_create_company_unit_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_01_22_180008_create_crops_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_01_22_180008_create_daqloon_stats_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2024_01_22_180008_create_diplomacy_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2024_01_22_180008_create_efficiency_upgrades_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2024_01_22_180008_create_escrow_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2024_01_22_180008_create_failed_jobs_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2024_01_22_180008_create_farmer_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2024_01_22_180008_create_farmer_workforce_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2024_01_22_180008_create_favor_assignments_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2024_01_22_180008_create_healing_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2024_01_22_180008_create_healing_items_required_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2024_01_22_180008_create_hunger_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2024_01_22_180008_create_inventory_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2024_01_22_180008_create_item_prices_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2024_01_22_180008_create_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2024_01_22_180008_create_laboratory_upgrades_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2024_01_22_180008_create_level_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2024_01_22_180008_create_levelup_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2024_01_22_180008_create_market_box_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2024_01_22_180008_create_merchant_offer_times_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2024_01_22_180008_create_merchant_offers_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2024_01_22_180008_create_merchants_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2024_01_22_180008_create_messages_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2024_01_22_180008_create_miner_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2024_01_22_180008_create_miner_permit_cost_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2024_01_22_180008_create_miner_workforce_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2024_01_22_180008_create_minerals_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2024_01_22_180008_create_offer_records_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2024_01_22_180008_create_offers_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2024_01_22_180008_create_password_reset_tokens_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2024_01_22_180008_create_personal_access_tokens_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2024_01_22_180008_create_persons_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2024_01_22_180008_create_public_chat_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2024_01_22_180008_create_skill_requirements_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2024_01_22_180008_create_smithy_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2024_01_22_180008_create_smithy_items_required_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2024_01_22_180008_create_stockpile_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2024_01_22_180008_create_store_discounts_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2024_01_22_180008_create_tavern_prices_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2024_01_22_180008_create_tavern_times_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2024_01_22_180008_create_tavern_workers_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2024_01_22_180008_create_trader_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2024_01_22_180008_create_trader_assignment_types_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2024_01_22_180008_create_trader_assignments_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2024_01_22_180008_create_trader_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2024_01_22_180008_create_training_type_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2024_01_22_180008_create_travel_times_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2024_01_22_180008_create_travelbureau_carts_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2024_01_22_180008_create_travelbureau_carts_req_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2024_01_22_180008_create_travelbureau_horses_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2024_01_22_180008_create_user_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2024_01_22_180008_create_user_levels_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2024_01_22_180008_create_users_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2024_01_22_180008_create_warrior_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2024_01_22_180008_create_warriors_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2024_01_22_180008_create_warriors_armory_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2024_01_22_180008_create_warriors_levels_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2024_01_22_180008_create_warriors_levels_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2024_01_22_180008_create_workplace_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2024_01_22_180011_add_foreign_keys_to_adventure_req_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2024_01_22_180011_add_foreign_keys_to_archery_shop_item_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2024_01_22_180011_add_foreign_keys_to_armory_items_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2024_01_22_180011_add_foreign_keys_to_crops_data_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2024_01_22_180011_add_foreign_keys_to_diplomacy_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2024_01_22_180011_add_foreign_keys_to_healing_items_required_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2024_01_22_180011_add_foreign_keys_to_minerals_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2024_01_22_180011_add_foreign_keys_to_smithy_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2024_01_22_180011_add_foreign_keys_to_smithy_items_required_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2024_01_22_180011_add_foreign_keys_to_trader_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2024_01_22_180011_add_foreign_keys_to_travelbureau_carts_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2024_01_22_180011_add_foreign_keys_to_travelbureau_carts_req_items_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2024_01_22_180011_add_foreign_keys_to_user_levels_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2024_01_22_180011_add_foreign_keys_to_warriors_armory_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2024_01_22_180011_add_foreign_keys_to_warriors_levels_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2018_08_08_100000_create_telescope_entries_table',2);
CREATE INDEX "idx_archery_shop_items_archery_shop_item_FK" ON "archery_shop_items" (`item_id`);
CREATE INDEX "idx_smithy_items_required_FK_smithy_items_required_item_id" ON "smithy_items_required" (`item_id`);
CREATE INDEX "idx_smithy_items_required_FK_smithy_items_required_item_required" ON "smithy_items_required" (`required_item`);
CREATE INDEX "idx_miner_workforce_miner_workforce_FK" ON "miner_workforce" (`user_id`);
CREATE INDEX "idx_farmer_workforce_farmer_workforce_FK" ON "farmer_workforce" (`user_id`);
CREATE INDEX "idx_adventure_battles_adventure_id" ON "adventure_battles" (`adventure_id`);
CREATE INDEX "idx_army_missions_armymission_model" ON "army_missions" (`mission_id`);
CREATE INDEX "idx_archery_shop_items_required_FK_archery_shop_items" ON "archery_shop_items_required" (`item_id`);
CREATE INDEX "idx_archery_shop_items_required_FK_archery_shop_item_material" ON "archery_shop_items_required" (`required_item`);
CREATE INDEX "idx_farmer_farmer_FK" ON "farmer" (`user_id`);
CREATE INDEX "idx_adventure_requirements_adventure_id" ON "adventure_requirements" (`adventure_id`);
CREATE INDEX "idx_telescope_entries_tags_telescope_entries_tags_tag_index" ON "telescope_entries_tags" (`tag`);
CREATE INDEX "idx_smithy_items_item_id" ON "smithy_items" (`item_id`);
CREATE INDEX "idx_smithy_items_FK_smithy_items_data_item" ON "smithy_items" (`item`);
CREATE INDEX "idx_conversation_trackers_conversation_trackers_FK" ON "conversation_trackers" (`user_id`);
CREATE INDEX "idx_crops_data_FK_crops_data_seed_item" ON "crops_data" (`seed_item`);
CREATE INDEX "idx_armory_items_data_item_name" ON "armory_items_data" (`item_id`);
CREATE INDEX "idx_armory_items_data_FK_smithy_data_items_name" ON "armory_items_data" (`item`);
CREATE INDEX "idx_minerals_FK_minerals_data_mineral_ore" ON "minerals" (`mineral_ore`);
CREATE INDEX "idx_healing_items_required_healing_items_required_FK" ON "healing_items_required" (`item_id`);
CREATE INDEX "idx_warriors_warriors_FK" ON "warriors" (`user_id`);
CREATE INDEX "idx_trader_trader_FK" ON "trader" (`cart_id`);
CREATE INDEX "idx_personal_access_tokens_personal_access_tokens_tokenable_type_tokenable_id_index" ON "personal_access_tokens" (`tokenable_type`,`tokenable_id`);
CREATE INDEX "idx_travelbureau_carts_req_items_FK_travelburea_item_name" ON "travelbureau_carts_req_items" (`required_item`);
CREATE INDEX "idx_travelbureau_carts_req_items_travelbureau_carts_req_items_FK" ON "travelbureau_carts_req_items" (`item_id`);
CREATE INDEX "idx_travelbureau_carts_travelbureau_carts_FK" ON "travelbureau_carts" (`item_id`);
CREATE INDEX "idx_travelbureau_carts_travelbureau_carts_FK_1" ON "travelbureau_carts" (`name`);
CREATE INDEX "idx_telescope_entries_telescope_entries_batch_id_index" ON "telescope_entries" (`batch_id`);
CREATE INDEX "idx_telescope_entries_telescope_entries_family_hash_index" ON "telescope_entries" (`family_hash`);
CREATE INDEX "idx_telescope_entries_telescope_entries_created_at_index" ON "telescope_entries" (`created_at`);
CREATE INDEX "idx_telescope_entries_telescope_entries_type_should_display_on_index_index" ON "telescope_entries" (`type`,`should_display_on_index`);
CREATE INDEX "idx_miner_miner_FK" ON "miner" (`user_id`);
END TRANSACTION;
