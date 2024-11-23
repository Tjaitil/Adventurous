/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `adventure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure` (
  `username` varchar(10000) NOT NULL,
  `adventure_id` smallint NOT NULL DEFAULT '0',
  `adventure_countdown` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `adventure_status` int NOT NULL DEFAULT '0',
  `reward` tinyint(1) NOT NULL,
  `notification` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventure_battles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure_battles` (
  `adventure_id` int NOT NULL,
  `daqloon_damage` int NOT NULL,
  `warrior_damage` int NOT NULL,
  `daqloon_wounded` int NOT NULL,
  `warrior_wounded` int NOT NULL,
  `daqloon_combo` int NOT NULL,
  `warrior_combo` int NOT NULL,
  KEY `adventure_id` (`adventure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventure_crystals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure_crystals` (
  `difficulty` varchar(10000) NOT NULL,
  `location` varchar(10000) NOT NULL,
  `min_chance` int NOT NULL,
  `max_chance` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventure_req_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure_req_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `difficulty` int NOT NULL,
  `amount_min` int NOT NULL,
  `amount_max` int NOT NULL,
  `role` varchar(10000) NOT NULL,
  UNIQUE KEY `item_id` (`item_id`),
  CONSTRAINT `fk_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventure_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure_requests` (
  `request_id` smallint NOT NULL AUTO_INCREMENT,
  `sender` varchar(10000) NOT NULL,
  `receiver` varchar(10000) NOT NULL,
  `adventure_id` smallint NOT NULL,
  `role` varchar(1000) NOT NULL,
  `method` varchar(100) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` text NOT NULL,
  UNIQUE KEY `request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventure_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure_requirements` (
  `adventure_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(10000) NOT NULL,
  `role` varchar(1000) NOT NULL,
  `required` varchar(1000) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `provided` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  KEY `adventure_id` (`adventure_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventure_rewards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventure_rewards` (
  `role` varchar(1000) NOT NULL,
  `difficulty` varchar(1000) NOT NULL,
  `location` varchar(1000) NOT NULL,
  `item` varchar(1000) NOT NULL,
  `min_amount` tinyint NOT NULL,
  `max_amount` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventures` (
  `adventure_id` int NOT NULL,
  `difficulty` varchar(1000) NOT NULL DEFAULT 'none',
  `location` varchar(1000) NOT NULL DEFAULT 'none',
  `adventure_leader` varchar(10000) NOT NULL,
  `farmer` varchar(1000) NOT NULL DEFAULT 'none',
  `miner` varchar(1000) NOT NULL DEFAULT 'none',
  `trader` varchar(1000) NOT NULL DEFAULT 'none',
  `warrior` varchar(1000) NOT NULL DEFAULT 'none',
  `adventure_status` tinyint(1) NOT NULL DEFAULT '0',
  `adventure_countdown` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `battle_result` smallint NOT NULL,
  `other_invite` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventures_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventures_data` (
  `location` varchar(10000) NOT NULL,
  `difficulty` varchar(10000) NOT NULL,
  `time` mediumint NOT NULL,
  `user_xp` smallint NOT NULL,
  `warrior_xp_min` smallint NOT NULL,
  `warrior_xp_max` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventures_farmer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventures_farmer` (
  `adventure_id` smallint NOT NULL,
  `username` varchar(1000) NOT NULL,
  `provided` varchar(10000) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `reward` smallint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventures_miner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventures_miner` (
  `adventure_id` smallint NOT NULL,
  `username` varchar(1000) NOT NULL,
  `provided` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventures_trader`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventures_trader` (
  `adventure_id` smallint NOT NULL,
  `username` varchar(1000) NOT NULL,
  `provided` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adventures_warrior`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adventures_warrior` (
  `adventure_id` smallint NOT NULL,
  `username` varchar(1000) NOT NULL,
  `provided` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `archery_shop_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archery_shop_items` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `item` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `item_multiplier` int unsigned DEFAULT NULL,
  `store_value` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `archery_shop_item_UN` (`item`),
  KEY `archery_shop_item_FK` (`item_id`),
  CONSTRAINT `archery_shop_item_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  CONSTRAINT `archery_shop_item_FK_1` FOREIGN KEY (`item`) REFERENCES `items` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `archery_shop_items_required`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archery_shop_items_required` (
  `item_id` int NOT NULL,
  `required_item` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `amount` smallint NOT NULL,
  `id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_archery_shop_items` (`item_id`),
  KEY `FK_archery_shop_item_material` (`required_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `armory_items_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `armory_items_data` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item` varchar(254) NOT NULL,
  `mineral_required` smallint NOT NULL,
  `wood_required` int NOT NULL,
  `level` smallint NOT NULL,
  `attack` smallint NOT NULL DEFAULT '0',
  `defence` smallint NOT NULL DEFAULT '0',
  `price` smallint NOT NULL,
  `type` varchar(10000) NOT NULL DEFAULT 'none',
  `warrior_type` varchar(100) NOT NULL DEFAULT 'all',
  UNIQUE KEY `item_id` (`item_id`),
  KEY `item_name` (`item_id`),
  KEY `FK_smithy_data_items_name` (`item`),
  CONSTRAINT `FK_smithy_data_items_name` FOREIGN KEY (`item`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_smity_data_items` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `army_missions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `army_missions` (
  `mission_id` int NOT NULL AUTO_INCREMENT,
  `required_warriors` int NOT NULL,
  `mission` text NOT NULL,
  `difficulty` varchar(1000) NOT NULL,
  `reward` text NOT NULL,
  `time` smallint NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `combat` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(10000) NOT NULL,
  KEY `armymission_model` (`mission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `army_missions_active`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `army_missions_active` (
  `username` varchar(1000) NOT NULL,
  `mission_id` smallint NOT NULL,
  `mission_countdown` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assignment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignment_types` (
  `type` varchar(10000) NOT NULL,
  `xp` smallint NOT NULL,
  `time` mediumint NOT NULL,
  `per_cargo_xp` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `city_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `city_relations` (
  `city` varchar(10000) NOT NULL,
  `hirtam` decimal(6,2) NOT NULL,
  `pvitul` decimal(6,2) NOT NULL,
  `khanz` decimal(6,2) NOT NULL,
  `ter` decimal(6,2) NOT NULL,
  `fansalplains` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_unit` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `unit` varchar(100) DEFAULT NULL,
  `parent_unit_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conversation_trackers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversation_trackers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_option_value` varchar(100) DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `current_index` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversation_trackers_FK` (`user_id`),
  CONSTRAINT `conversation_trackers_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crops_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crops_data` (
  `crop_type` varchar(1000) NOT NULL,
  `farmer_level` tinyint NOT NULL,
  `time` smallint NOT NULL,
  `experience` smallint NOT NULL,
  `seed_required` smallint NOT NULL,
  `seed_item` varchar(256) NOT NULL,
  `min_crop_count` smallint NOT NULL,
  `max_crop_count` smallint NOT NULL,
  `location` varchar(10000) NOT NULL,
  PRIMARY KEY (`crop_type`),
  KEY `FK_crops_data_seed_item` (`seed_item`),
  CONSTRAINT `FK_crops_data_item_name` FOREIGN KEY (`crop_type`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_crops_data_seed_item` FOREIGN KEY (`seed_item`) REFERENCES `items` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daqloon_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daqloon_stats` (
  `location` varchar(1000) NOT NULL,
  `difficulty` varchar(1000) NOT NULL,
  `attack` int NOT NULL,
  `defence` int NOT NULL,
  `amount` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `diplomacy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diplomacy` (
  `id` int NOT NULL,
  `username` varchar(10000) NOT NULL,
  `hirtam` decimal(6,2) NOT NULL DEFAULT '1.00',
  `pvitul` decimal(6,2) NOT NULL DEFAULT '1.00',
  `khanz` decimal(6,2) NOT NULL DEFAULT '1.00',
  `ter` decimal(6,2) NOT NULL DEFAULT '1.00',
  `fansalplains` decimal(6,2) NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `FK_diplomacy_user_id` FOREIGN KEY (`id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `efficiency_upgrades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efficiency_upgrades` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `level` int unsigned DEFAULT NULL,
  `price` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `escrow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `escrow` (
  `id` smallint NOT NULL,
  `offeror` varchar(10000) NOT NULL,
  `item` varchar(10000) NOT NULL,
  `amount` smallint NOT NULL,
  `date_inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `farmer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer` (
  `username` varchar(1000) NOT NULL,
  `fields` tinyint NOT NULL DEFAULT '2',
  `crop_type` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `crop_quant` tinyint NOT NULL DEFAULT '0',
  `crop_finishes_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `location` varchar(10000) NOT NULL,
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `farmer_FK` (`user_id`),
  CONSTRAINT `farmer_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `farmer_workforce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer_workforce` (
  `username` varchar(1000) NOT NULL,
  `workforce_total` tinyint NOT NULL DEFAULT '4',
  `avail_workforce` tinyint NOT NULL DEFAULT '4',
  `towhar` tinyint NOT NULL DEFAULT '0',
  `krasnur` tinyint NOT NULL,
  `efficiency_level` smallint NOT NULL DEFAULT '1',
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `farmer_workforce_FK` (`user_id`),
  CONSTRAINT `farmer_workforce_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `favor_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favor_assignments` (
  `favor_id` smallint NOT NULL,
  `username` varchar(10000) NOT NULL,
  `base` varchar(1000) NOT NULL,
  `destination` varchar(1000) NOT NULL,
  `cargo` varchar(1000) NOT NULL,
  `cargo_amount` mediumint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `healing_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `healing_items` (
  `item_id` int NOT NULL,
  `item` varchar(10000) NOT NULL,
  `price` int NOT NULL,
  `heal` smallint NOT NULL,
  `bakery_item` smallint NOT NULL DEFAULT '1',
  UNIQUE KEY `healing_items_UN` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `healing_items_required`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `healing_items_required` (
  `item_id` int NOT NULL,
  `required_item` varchar(10000) NOT NULL,
  `amount` smallint NOT NULL,
  `id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `healing_items_required_FK` (`item_id`),
  CONSTRAINT `healing_items_required_FK` FOREIGN KEY (`item_id`) REFERENCES `healing_items` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hunger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hunger` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `current` smallint NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(10000) NOT NULL,
  `item` varchar(10000) NOT NULL,
  `amount` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_prices` (
  `item` varchar(10000) NOT NULL,
  `week_amount` bigint NOT NULL DEFAULT '0',
  `week_price` decimal(10,4) NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(254) NOT NULL,
  `store_value` int NOT NULL DEFAULT '0',
  `in_game` tinyint(1) NOT NULL,
  `towhar_rate` int NOT NULL DEFAULT '1',
  `golbak_rate` int NOT NULL DEFAULT '1',
  `snerpiir_rate` int NOT NULL DEFAULT '1',
  `cruendo_rate` int NOT NULL DEFAULT '1',
  `pvitul_rate` int NOT NULL DEFAULT '1',
  `khanz_rate` int NOT NULL DEFAULT '1',
  `ter_rate` int NOT NULL DEFAULT '1',
  `krasnur_rate` int NOT NULL DEFAULT '1',
  `hirtam_rate` int NOT NULL DEFAULT '1',
  `fansal_plains_rate` int NOT NULL DEFAULT '1',
  `tasnobil_rate` int NOT NULL DEFAULT '1',
  `trader_assignment_type` varchar(1000) NOT NULL DEFAULT 'small trade',
  `adventure_requirement` tinyint NOT NULL DEFAULT '0',
  `adventure_requirement_difficulty` varchar(1000) NOT NULL DEFAULT '1',
  `adventure_requirement_role` varchar(1000) NOT NULL,
  PRIMARY KEY (`item_id`) USING BTREE,
  UNIQUE KEY `item_name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `laboratory_upgrades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratory_upgrades` (
  `profiency` varchar(10000) NOT NULL,
  `type` varchar(10000) NOT NULL,
  `level` smallint NOT NULL,
  `cost` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `level_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `level_data` (
  `level` tinyint NOT NULL,
  `next_Level` int DEFAULT NULL,
  `max_farm_workers` tinyint DEFAULT NULL,
  `max_mine_workers` tinyint DEFAULT NULL,
  `max_warriors` tinyint DEFAULT NULL,
  `max_efficiency_level` int DEFAULT NULL,
  UNIQUE KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `levelup_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `levelup_data` (
  `level` tinyint NOT NULL,
  `profiency` varchar(10000) NOT NULL,
  `image` text NOT NULL,
  `unlocked` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `market_box`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `market_box` (
  `id` int NOT NULL,
  `username` varchar(10000) NOT NULL,
  `item` varchar(10000) NOT NULL,
  `amount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `merchant_offer_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_offer_times` (
  `username` varchar(100) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `merchant_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_offers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  `item` varchar(50) NOT NULL,
  `store_value` int NOT NULL,
  `store_buy_price` int NOT NULL,
  `amount` int NOT NULL,
  `date_inserted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `offer_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `merchants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchants` (
  `item_id` int NOT NULL,
  `item` varchar(1000) NOT NULL,
  `amount` smallint NOT NULL,
  `want` varchar(10000) NOT NULL,
  `want_amount` int NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(10000) NOT NULL,
  `sender` varchar(1000) NOT NULL,
  `receiver` varchar(10000) NOT NULL,
  `message` mediumtext NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_message_read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `miner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miner` (
  `username` varchar(10000) NOT NULL,
  `mineral_ore` varchar(10000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `mining_finishes_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `permits` int NOT NULL DEFAULT '30',
  `location` varchar(1000) NOT NULL,
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `miner_FK` (`user_id`),
  CONSTRAINT `miner_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `miner_permit_cost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miner_permit_cost` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(100) DEFAULT NULL,
  `permit_cost` int unsigned DEFAULT NULL,
  `permit_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `miner_workforce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miner_workforce` (
  `username` varchar(10000) NOT NULL,
  `workforce_total` smallint NOT NULL DEFAULT '2',
  `avail_workforce` smallint NOT NULL DEFAULT '2',
  `golbak` smallint NOT NULL DEFAULT '0',
  `snerpiir` smallint NOT NULL,
  `mineral_quant_level` smallint NOT NULL DEFAULT '1',
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `efficiency_level` int unsigned DEFAULT '1',
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `miner_workforce_FK` (`user_id`),
  CONSTRAINT `miner_workforce_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `minerals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `minerals` (
  `mineral_type` varchar(10000) NOT NULL,
  `mineral_ore` varchar(100) NOT NULL DEFAULT 'iron ore',
  `miner_level` smallint NOT NULL,
  `experience` smallint NOT NULL,
  `time` smallint NOT NULL,
  `min_per_period` mediumint NOT NULL,
  `max_per_period` int NOT NULL,
  `permit_cost` int NOT NULL,
  `location` varchar(1000) NOT NULL,
  `id` int unsigned NOT NULL,
  PRIMARY KEY (`mineral_type`(255)) USING BTREE,
  KEY `FK_minerals_data_mineral_ore` (`mineral_ore`),
  CONSTRAINT `FK_minerals_data_mineral_ore` FOREIGN KEY (`mineral_ore`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offer_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_records` (
  `id` int NOT NULL,
  `username` varchar(1000) NOT NULL,
  `type` varchar(1000) NOT NULL,
  `item` varchar(1000) NOT NULL,
  `amount` smallint NOT NULL,
  `price_ea` smallint NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offers` (
  `id` smallint NOT NULL AUTO_INCREMENT,
  `offeror` varchar(10000) NOT NULL,
  `item` varchar(10000) NOT NULL,
  `amount` smallint NOT NULL,
  `price_ea` smallint NOT NULL,
  `type` varchar(1000) NOT NULL,
  `progress` smallint NOT NULL DEFAULT '0',
  `amount_left` smallint NOT NULL,
  `box_item` varchar(100) DEFAULT 'none',
  `box_amount` int NOT NULL DEFAULT '0',
  UNIQUE KEY `unit_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `persons` (
  `location` varchar(10000) NOT NULL,
  `name` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `public_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `public_chat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `clock` time NOT NULL,
  `username` varchar(10000) NOT NULL,
  `message` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `skill_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill_requirements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item` varchar(1000) NOT NULL,
  `skill` varchar(1000) NOT NULL,
  `level` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `smithy_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `smithy_items` (
  `item_id` int NOT NULL,
  `item` varchar(1000) NOT NULL,
  `store_value` int NOT NULL,
  `mineral` varchar(1000) NOT NULL,
  `item_multiplier` int NOT NULL DEFAULT '1',
  KEY `item_id` (`item_id`),
  KEY `FK_smithy_items_data_item` (`item`),
  CONSTRAINT `FK_smithy_items_data_item` FOREIGN KEY (`item`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_smithy_items_data_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `smithy_items_required`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `smithy_items_required` (
  `item_id` int NOT NULL,
  `required_item` varchar(1000) DEFAULT NULL,
  `amount` int NOT NULL,
  KEY `FK_smithy_items_required_item_id` (`item_id`),
  KEY `FK_smithy_items_required_item_required` (`required_item`),
  CONSTRAINT `FK_smithy_items_required_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_smithy_items_required_item_required` FOREIGN KEY (`required_item`) REFERENCES `items` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stockpile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stockpile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(10000) NOT NULL,
  `item` varchar(1000) NOT NULL,
  `amount` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `store_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_discounts` (
  `store` varchar(100) NOT NULL,
  `discount` float NOT NULL,
  `profiency` varchar(100) NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tavern_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tavern_prices` (
  `type` varchar(10000) NOT NULL,
  `price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tavern_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tavern_times` (
  `username` varchar(10000) NOT NULL,
  `new_workers` date DEFAULT '0000-00-00',
  `towhar` tinyint(1) NOT NULL DEFAULT '0',
  `krasnur` tinyint(1) NOT NULL DEFAULT '0',
  `snerpiir` tinyint(1) NOT NULL DEFAULT '0',
  `golbak` tinyint(1) NOT NULL DEFAULT '0',
  `tasnobil` tinyint(1) NOT NULL DEFAULT '0',
  `cruendo` tinyint(1) NOT NULL DEFAULT '0',
  `fagna` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tavern_workers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tavern_workers` (
  `username` varchar(10000) NOT NULL,
  `city` varchar(10000) NOT NULL,
  `type` varchar(1000) NOT NULL,
  `level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trader`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trader` (
  `username` varchar(10000) NOT NULL,
  `assignment_id` int NOT NULL DEFAULT '0',
  `cart_id` int NOT NULL,
  `cart_amount` int NOT NULL DEFAULT '0',
  `delivered` smallint NOT NULL DEFAULT '0',
  `id` int unsigned NOT NULL,
  `trading_countdown` datetime DEFAULT NULL,
  KEY `trader_FK` (`cart_id`),
  CONSTRAINT `trader_FK` FOREIGN KEY (`cart_id`) REFERENCES `travelbureau_carts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trader_assignment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trader_assignment_types` (
  `id` int unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `xp_per_cargo` float NOT NULL,
  `item_reward_amount` int unsigned NOT NULL,
  `xp_finished` int unsigned NOT NULL,
  `diplomacy_percentage` float NOT NULL,
  `currency_reward_amount` int unsigned NOT NULL,
  `required_level` int NOT NULL,
  `xp_started` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trader_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trader_assignments` (
  `id` smallint NOT NULL AUTO_INCREMENT,
  `base` varchar(10000) NOT NULL,
  `destination` varchar(10000) NOT NULL,
  `cargo` text NOT NULL,
  `assignment_amount` varchar(1000) NOT NULL,
  `time` int NOT NULL,
  `assignment_type` varchar(100) NOT NULL,
  `date_inserted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `assignment_id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trader_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trader_data` (
  `level` smallint NOT NULL,
  `capasity` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_type_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_type_data` (
  `training_type` varchar(10000) NOT NULL,
  `time` smallint NOT NULL,
  `experience` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travel_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_times` (
  `city` varchar(1000) NOT NULL,
  `location_x` varchar(1000) NOT NULL,
  `location_y` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelbureau_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travelbureau_carts` (
  `name` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `wheel` varchar(1000) NOT NULL,
  `wood` varchar(10000) NOT NULL,
  `store_value` smallint NOT NULL,
  `capasity` mediumint NOT NULL,
  `towhar` smallint NOT NULL,
  `golbak` smallint NOT NULL,
  `mineral_amount` smallint NOT NULL,
  `wood_amount` smallint NOT NULL,
  `item_id` int DEFAULT NULL,
  `id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `travelbureau_carts_FK` (`item_id`),
  KEY `travelbureau_carts_FK_1` (`name`),
  CONSTRAINT `travelbureau_carts_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  CONSTRAINT `travelbureau_carts_FK_1` FOREIGN KEY (`name`) REFERENCES `items` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelbureau_carts_req_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travelbureau_carts_req_items` (
  `required_item` varchar(1000) NOT NULL,
  `amount` int NOT NULL,
  `item_id` int DEFAULT NULL,
  KEY `FK_travelburea_item_name` (`required_item`),
  KEY `travelbureau_carts_req_items_FK` (`item_id`),
  CONSTRAINT `FK_travelburea_item_name` FOREIGN KEY (`required_item`) REFERENCES `items` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `travelbureau_carts_req_items_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelbureau_horses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travelbureau_horses` (
  `type` varchar(1000) NOT NULL,
  `value` mediumint NOT NULL,
  `towhar` smallint NOT NULL,
  `golbak` smallint NOT NULL,
  `snerpiir` smallint NOT NULL,
  `speed` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(1000) NOT NULL,
  `location` varchar(1000) NOT NULL DEFAULT 'tutorial island',
  `map_location` varchar(10) NOT NULL DEFAULT '9.9',
  `game_id` varchar(1000) NOT NULL,
  `session_id` int NOT NULL,
  `destination` varchar(1000) NOT NULL DEFAULT 'none',
  `arrive_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `profiency` varchar(1000) NOT NULL DEFAULT 'none',
  `horse` varchar(10000) NOT NULL DEFAULT 'none',
  `artefact` varchar(1000) NOT NULL DEFAULT 'none',
  `hunger` smallint NOT NULL DEFAULT '100',
  `hunger_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `frajrite_items` tinyint(1) NOT NULL,
  `wujkin_items` tinyint(1) NOT NULL,
  `stockpile_max_amount` int DEFAULT '60',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_levels` (
  `id` int NOT NULL,
  `username` varchar(1000) NOT NULL,
  `adventurer_respect` decimal(6,2) NOT NULL DEFAULT '1.00',
  `farmer_level` smallint NOT NULL DEFAULT '1',
  `farmer_xp` int NOT NULL DEFAULT '0',
  `miner_level` smallint NOT NULL DEFAULT '1',
  `miner_xp` int NOT NULL DEFAULT '0',
  `trader_level` smallint NOT NULL DEFAULT '1',
  `trader_xp` int NOT NULL DEFAULT '0',
  `warrior_level` smallint NOT NULL DEFAULT '1',
  `warrior_xp` int NOT NULL DEFAULT '0',
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `FK_user_id` FOREIGN KEY (`id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warrior`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warrior` (
  `username` varchar(10000) NOT NULL,
  `warrior_amount` smallint NOT NULL DEFAULT '2',
  `mission_id` smallint NOT NULL DEFAULT '0',
  `mission_countdown` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warriors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warriors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(10000) NOT NULL,
  `warrior_id` smallint NOT NULL,
  `type` varchar(1000) NOT NULL DEFAULT 'melee',
  `training_countdown` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_training` tinyint(1) DEFAULT '0',
  `training_type` varchar(10000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `army_mission` int NOT NULL DEFAULT '0',
  `health` smallint NOT NULL DEFAULT '100',
  `location` varchar(100) NOT NULL DEFAULT 'tasnobil',
  `is_resting` tinyint(1) NOT NULL DEFAULT '0',
  `rest_start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warriors_UN` (`warrior_id`,`user_id`),
  KEY `warriors_FK` (`user_id`),
  CONSTRAINT `warriors_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warriors_armory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warriors_armory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(1000) NOT NULL,
  `warrior_id` int NOT NULL,
  `helm` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `ammunition` varchar(10000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `ammunition_amount` smallint NOT NULL DEFAULT '0',
  `body` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `right_hand` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `left_hand` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `legs` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `boots` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'none',
  `attack` smallint NOT NULL DEFAULT '10',
  `defence` smallint NOT NULL DEFAULT '12',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warriors_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warriors_levels` (
  `id` int NOT NULL,
  `username` varchar(10000) NOT NULL,
  `warrior_id` int NOT NULL,
  `stamina_level` smallint NOT NULL DEFAULT '1',
  `stamina_xp` smallint DEFAULT '0',
  `technique_level` smallint NOT NULL DEFAULT '1',
  `technique_xp` smallint DEFAULT '0',
  `precision_level` smallint NOT NULL DEFAULT '1',
  `precision_xp` smallint DEFAULT '0',
  `strength_level` smallint NOT NULL DEFAULT '1',
  `strength_xp` smallint DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `FK_ID` FOREIGN KEY (`id`) REFERENCES `warriors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warriors_levels_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warriors_levels_data` (
  `skill_level` smallint NOT NULL,
  `next_level` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `workplace_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workplace_data` (
  `level` smallint NOT NULL,
  `max_workers` smallint NOT NULL,
  `skill` varchar(10000) NOT NULL,
  `cost` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

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
