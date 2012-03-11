use audley_1;

/* Scripts Updates db v1.0 to v1.1 */


/* Create: table to set databse version */
CREATE  TABLE _dbVersion (
  id_dbVersion FLOAT NOT NULL ,
  PRIMARY KEY (id_dbVersion) );



/* set: new db version */
INSERT INTO _dbVersion VALUES (1.1);



/* Rename: tblPageStats to tblStats */
ALTER TABLE tblPageStats RENAME TO  tblStats;



/* Create: table tblweblogs to store testimonials */
CREATE TABLE `tblweblogs` (
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `weblog_id` int(11) NOT NULL AUTO_INCREMENT,
  `weblog_name` varchar(50) NOT NULL,
  `weblog_desc` varchar(500) DEFAULT NULL,
  `weblog_entry_name` varchar(50) NOT NULL,
  PRIMARY KEY (`weblog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1$$

