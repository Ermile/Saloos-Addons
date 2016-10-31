ALTER TABLE `termusages` CHANGE `termusage_foreign` `termusage_foreign` ENUM('posts','products','attachments','files','comments','users') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `terms` ADD `status` ENUM('enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other') NOT NULL DEFAULT 'awaiting' AFTER `user_id`;
UPDATE `terms` SET `status` = 'enable' WHERE 1;
ALTER TABLE `terms` ADD `term_count` INT(10) UNSIGNED NULL AFTER `status`;