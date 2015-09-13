CREATE TABLE IF NOT EXISTS `#__geommunity3es_maps` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`title` VARCHAR(255)  NOT NULL ,
`def_lat` VARCHAR(255)  NOT NULL ,
`def_lng` VARCHAR(255)  NOT NULL ,
`show_users` TINYINT(1)  NOT NULL ,
`users_addressfield_id` VARCHAR(180)  NOT NULL ,
`profiletypes` VARCHAR(255)  NULL ,
`onlineonly` VARCHAR(255)  NOT NULL ,
`usermarker` VARCHAR(255)  NOT NULL ,
`show_photoalbums` VARCHAR(255)  NOT NULL ,
`show_photos` TINYINT(1)  NOT NULL ,
`show_videos` TINYINT(1)  NOT NULL ,
`show_groups` TINYINT(1)  NOT NULL ,
`groups_addressfield_id` VARCHAR(180) NULL ,
`show_events` TINYINT(1)  NOT NULL ,
`events_addressfield_id` VARCHAR(180)  NULL ,
`show_easyblogs` TINYINT(1)  NOT NULL ,
`kmlurl` TEXT NOT NULL ,
`privacyaware` VARCHAR(255)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

INSERT IGNORE INTO `#__geommunity3es_maps` (
`id` , `title` , `def_lat` , `def_lng` , `show_users` ,`onlineonly`  , `usermarker` , `show_photoalbums` , `show_photos` , `show_videos` , `show_groups` , `show_events` , `show_easyblogs` , `kmlurl` , `privacyaware` , `ordering` , `state` 
)
VALUES
( '1' , 'Default' , '1' , 	'1', 		'1', 			'0',	'1' , 		'1' , 		'1' ,		'0' ,		'1', 		'0' , 			'0' , 'http://www.glotter.com/data/kml/data9.kmz' ,'1' ,'0' , '1' );