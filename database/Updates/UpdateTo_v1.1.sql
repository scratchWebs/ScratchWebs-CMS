/* Scripts Updates db v1.0 to v1.1 
 * 
 * change 'use audley_1' to the name of the relevant db
 * */

/*
 * use audley_1;
 * */


/* Create: table to set databse version */
CREATE  TABLE _dbVersion (
  id_dbVersion FLOAT NOT NULL ,
  PRIMARY KEY (id_dbVersion) );



/* set: new db version */
INSERT INTO _dbVersion VALUES (1.1);



/* Rename: tblPageStats to tblStats */
ALTER TABLE tblPageStats RENAME TO  tblStats;



/* Create: table tblweblogs to store testimonials (header record) */
CREATE TABLE `tblweblogs` (
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `weblog_id` int(11) NOT NULL AUTO_INCREMENT,
  `weblog_name` varchar(50) NOT NULL,
  `weblog_desc` varchar(500) DEFAULT NULL,
  `weblog_entry_name` varchar(50) NOT NULL,
  PRIMARY KEY (`weblog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/* Insert: testimonial header record (for audley site) */
INSERT INTO `tblweblogs`
(`delete_flag`,`enabled`,`weblog_id`,`weblog_name`,`weblog_desc`,`weblog_entry_name`)
VALUES(0,1,1,'Testimonials',NULL,'Testimonial');



/* Create: table tblweblogentries to store indivitual testominial entries */
CREATE TABLE `tblweblogentries` (
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `wlentry_id` int(11) NOT NULL AUTO_INCREMENT,
  `wlentry_text` varchar(5000) NOT NULL,
  `wlentry_author` varchar(100) DEFAULT NULL,
  `wlentry_date` datetime NOT NULL,
  `wlentry_order` int(11) NOT NULL,
  `wlentry_fk_weblog_id` int(11) NOT NULL,
  PRIMARY KEY (`wlentry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/* Update gallery descriptions so they can hold more data */
ALTER TABLE `tblGalleries` 
CHANGE COLUMN `gallery_desc_long` `gallery_desc_long` MEDIUMTEXT NOT NULL;

