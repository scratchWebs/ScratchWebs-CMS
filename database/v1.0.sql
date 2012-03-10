SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE `audley_1` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `audley_1`;

CREATE TABLE IF NOT EXISTS `tblFeatures` (
  `feature_id` int(11) NOT NULL,
  `feature_type` tinyint(4) NOT NULL,
  `feature_code_ref` varchar(30) NOT NULL,
  `feature_fk_pg_id` int(11) NOT NULL,
  PRIMARY KEY  (`feature_id`,`feature_type`,`feature_fk_pg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tblGalleries` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `gallery_id` int(11) NOT NULL auto_increment,
  `gallery_code_ref` varchar(30) NOT NULL,
  `gallery_name` varchar(100) NOT NULL,
  `gallery_desc_short` varchar(500) NOT NULL,
  `gallery_desc_long` varchar(1000) NOT NULL,
  `gallery_order` int(11) NOT NULL,
  `gallery_featured` tinyint(1) NOT NULL,
  `gallery_internal_count` int(11) NOT NULL,
  `gallery_external_count` int(11) NOT NULL,
  `gallery_fk_portfolio_id` int(11) default NULL,
  `gallery_fk_pg_id` int(11) default NULL,
  PRIMARY KEY  (`gallery_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

CREATE TABLE IF NOT EXISTS `tblImages` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `img_id` int(11) NOT NULL auto_increment,
  `img_code_ref` varchar(30) NOT NULL,
  `img_name` varchar(100) NOT NULL,
  `img_desc_short` varchar(500) NOT NULL,
  `img_desc_long` varchar(1000) NOT NULL,
  `img_width` int(5) NOT NULL,
  `img_height` int(5) NOT NULL,
  `img_type` varchar(25) NOT NULL,
  `img_data_thumb` longblob NOT NULL,
  `img_data_preview` longblob NOT NULL,
  `img_data_large` longblob NOT NULL,
  `img_data_original` longblob NOT NULL,
  `img_featured` tinyint(1) NOT NULL,
  `img_order` int(11) NOT NULL,
  `img_internal_count` int(11) NOT NULL default '0',
  `img_external_count` int(11) NOT NULL default '0',
  `img_fk_gallery_id` int(11) default NULL,
  `img_fk_section_id` int(11) default NULL,
  `img_fk_pg_id` int(11) default NULL,
  PRIMARY KEY  (`img_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=143 ;

CREATE TABLE IF NOT EXISTS `tblLog` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `log_id` int(11) NOT NULL auto_increment,
  `log_object_type` tinyint(4) NOT NULL,
  `log_object_id` int(11) NOT NULL,
  `log_type` tinyint(4) NOT NULL,
  `log_message` varchar(200) NOT NULL,
  `log_date` datetime NOT NULL,
  `ip_address` varchar(20) NOT NULL default 'unknown',
  `log_user_agent` varchar(256) NOT NULL default '''''',
  `log_fk_user_id` int(11) NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=633 ;

CREATE TABLE IF NOT EXISTS `tblPageStats` (
  `stat_id` int(11) NOT NULL auto_increment,
  `stat_date` datetime NOT NULL,
  `stat_object_type` int(11) NOT NULL,
  `stat_object_id` tinyint(4) NOT NULL,
  `stat_ip_address` varchar(20) NOT NULL,
  `stat_referer` varchar(256) NOT NULL,
  `stat_user_agent` varchar(256) NOT NULL,
  PRIMARY KEY  (`stat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=814 ;

CREATE TABLE IF NOT EXISTS `tblPages` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `pg_id` int(11) NOT NULL auto_increment COMMENT 'Auto Incremented ID',
  `pg_code_ref` varchar(30) NOT NULL,
  `pg_path` varchar(50) NOT NULL,
  `pg_linkname` varchar(50) NOT NULL,
  `pg_title` varchar(100) NOT NULL,
  `pg_description` varchar(100) NOT NULL,
  `pg_meta_title` varchar(100) NOT NULL default '',
  `pg_meta_description` varchar(1000) NOT NULL default '',
  `pg_meta_keywords` varchar(1000) NOT NULL default '',
  `pg_order` int(11) NOT NULL,
  `pg_enabled` tinyint(1) NOT NULL default '1',
  `pg_internal_count` int(11) NOT NULL default '0',
  `pg_external_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

CREATE TABLE IF NOT EXISTS `tblPortfolios` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `portfolio_id` int(11) NOT NULL auto_increment,
  `portfolio_name` varchar(100) NOT NULL,
  `portfolio_gallery_rename` varchar(100) NOT NULL,
  `portfolio_order` int(11) NOT NULL,
  `portfolio_featured` tinyint(1) NOT NULL,
  `portfolio_internal_count` int(11) NOT NULL default '0',
  `portfolio_external_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`portfolio_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `tblSections` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `section_id` int(11) NOT NULL auto_increment,
  `section_code_ref` varchar(30) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `section_html` varchar(5000) NOT NULL,
  `section_max_size` int(11) NOT NULL,
  `section_order` int(11) NOT NULL,
  `section_fk_pg_id` int(11) NOT NULL,
  PRIMARY KEY  (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

CREATE TABLE IF NOT EXISTS `tblUsers` (
  `delete_flag` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `user_id` int(11) NOT NULL auto_increment,
  `user_name` varchar(20) NOT NULL,
  `user_pass` varchar(20) NOT NULL,
  `user_type` tinyint(4) NOT NULL,
  `user_full_name` varchar(50) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_is_expired` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;
