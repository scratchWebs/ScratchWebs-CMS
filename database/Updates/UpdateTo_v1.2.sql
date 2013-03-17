/* Scripts Updates db v1.1 to v1.2 
 * 
 * change 'use audley_1' to the name of the relevant db
 * */

/*
 * use audley_1;
 * */



/*
	Add field to tblImages: img_url
*/
ALTER TABLE `audley_1`.`tblimages` ADD COLUMN `img_url` VARCHAR(2000) NOT NULL  AFTER `img_fk_pg_id` ;

