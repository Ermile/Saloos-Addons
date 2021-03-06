CREATE TABLE `transactionlogs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `amount` varchar(255) NULL DEFAULT NULL,
  `payment` varchar(255) NULL DEFAULT NULL,
  `date` date NULL DEFAULT NULL,
  `time` time NULL DEFAULT NULL,
  `condition` varchar(255) NULL DEFAULT NULL,
  `request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `session` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` ENUM('enable','disable','expired','awaiting','filtered','blocked','other') NULL DEFAULT 'awaiting',
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `createdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;