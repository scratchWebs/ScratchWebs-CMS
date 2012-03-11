use audley_1;

/* Scripts Updates db v1.0 to v1.1 */


/* Create: table to set databse version */
CREATE  TABLE `audley_1`.`_dbVersion` (
  `id_dbVersion` FLOAT NOT NULL ,
  PRIMARY KEY (`id_dbVersion`) );



/* set: new db version */
INSERT INTO _dbVersion VALUES (1.1);



/* Rename: tblPageStats to tblStats */
ALTER TABLE `audley_1`.`tblPageStats` RENAME TO  `audley_1`.`tblStats` ;