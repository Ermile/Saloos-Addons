CREATE TABLE IF NOT EXISTS `commentdetails` (
`user_id` INT(10) unsigned NOT NULL,
`comment_id` bigint(20) unsigned NOT NULL,
`type` ENUM('minus','plus') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;