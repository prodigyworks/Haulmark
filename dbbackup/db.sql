-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.17 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table hallmark.hallmark_absence
CREATE TABLE IF NOT EXISTS `hallmark_absence` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `memberid` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `daystaken` float DEFAULT NULL,
  `requesteddate` date NOT NULL,
  `enddate_half` int(11) DEFAULT NULL,
  `startdate_half` int(11) DEFAULT NULL,
  `reason` text,
  `rejecteddate` date NOT NULL,
  `rejectedby` int(11) DEFAULT NULL,
  `absencetype` varchar(20) DEFAULT NULL,
  `accepteddate` date DEFAULT NULL,
  `acceptedby` int(11) DEFAULT NULL,
  `absentreason` text,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_accountstatus
CREATE TABLE IF NOT EXISTS `hallmark_accountstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_applicationactionroles
CREATE TABLE IF NOT EXISTS `hallmark_applicationactionroles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actionid` int(11) DEFAULT NULL,
  `roleid` varchar(20) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_applicationactions
CREATE TABLE IF NOT EXISTS `hallmark_applicationactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `description` varchar(40) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_applicationtablecolumns
CREATE TABLE IF NOT EXISTS `hallmark_applicationtablecolumns` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `headerid` int(10) NOT NULL,
  `columnindex` int(10) NOT NULL,
  `width` int(10) NOT NULL,
  `hidecolumn` int(10) NOT NULL DEFAULT '0',
  `label` varchar(60) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `headerid_column` (`headerid`,`columnindex`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_applicationtables
CREATE TABLE IF NOT EXISTS `hallmark_applicationtables` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pageid` int(10) NOT NULL,
  `memberid` int(10) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_bankholiday
CREATE TABLE IF NOT EXISTS `hallmark_bankholiday` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_booking
CREATE TABLE IF NOT EXISTS `hallmark_booking` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `vehicleid` int(10) DEFAULT NULL,
  `driverid` int(10) DEFAULT NULL,
  `trailerid` int(10) DEFAULT NULL,
  `customerid` int(10) DEFAULT NULL,
  `signatureid` int(10) DEFAULT NULL,
  `loadtypeid` int(10) DEFAULT NULL,
  `vehicletypeid` int(10) DEFAULT NULL,
  `depotid` int(10) DEFAULT NULL,
  `worktypeid` int(10) DEFAULT NULL,
  `pallets` int(10) DEFAULT NULL,
  `items` int(10) DEFAULT NULL,
  `memberid` int(10) DEFAULT NULL,
  `startdatetime` datetime DEFAULT NULL,
  `enddatetime` datetime DEFAULT NULL,
  `dsdatetime` datetime DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `charge` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `miles` decimal(10,2) DEFAULT NULL,
  `vehiclecostoverhead` decimal(10,2) DEFAULT NULL,
  `allegrodayrate` decimal(10,2) DEFAULT NULL,
  `agencydayrate` decimal(10,2) DEFAULT NULL,
  `wages` decimal(10,2) DEFAULT NULL,
  `fuelcostoverhead` decimal(10,2) DEFAULT NULL,
  `maintenanceoverhead` decimal(10,2) DEFAULT NULL,
  `profitmargin` decimal(10,2) DEFAULT NULL,
  `customercostpermile` decimal(10,2) DEFAULT NULL,
  `bookingtype` varchar(1) DEFAULT NULL,
  `postedtosage` varchar(1) DEFAULT 'N',
  `statusid` int(11) DEFAULT NULL,
  `nominalledgercodeid` int(11) DEFAULT NULL,
  `legsummary` varchar(300) DEFAULT NULL,
  `duration` decimal(10,2) DEFAULT NULL,
  `ordernumber` varchar(20) DEFAULT NULL,
  `ordernumber2` varchar(20) DEFAULT NULL,
  `drivername` varchar(30) DEFAULT NULL,
  `driverphone` varchar(30) DEFAULT NULL,
  `storename` varchar(30) DEFAULT NULL,
  `fromplace` varchar(100) DEFAULT NULL,
  `toplace` varchar(100) DEFAULT NULL,
  `fromplace_lat` double DEFAULT NULL,
  `fromplace_lng` double DEFAULT NULL,
  `fromplace_phone` varchar(50) DEFAULT NULL,
  `fromplace_ref` varchar(50) DEFAULT NULL,
  `toplace_lat` double DEFAULT NULL,
  `toplace_lng` double DEFAULT NULL,
  `toplace_phone` varchar(50) DEFAULT NULL,
  `toplace_ref` varchar(50) DEFAULT NULL,
  `totalmiles` double DEFAULT NULL,
  `totaltimehrs` double DEFAULT NULL,
  `notes` text,
  `agencyvehicleregistration` varchar(10) NOT NULL,
  `fixedprice` varchar(1) NOT NULL,
  `podsent` varchar(1) DEFAULT NULL,
  `invoiced` varchar(1) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_bookingdocs
CREATE TABLE IF NOT EXISTS `hallmark_bookingdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bookingid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_bookingleg
CREATE TABLE IF NOT EXISTS `hallmark_bookingleg` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bookingid` int(10) NOT NULL,
  `signatureid` int(10) NOT NULL,
  `pallets` int(10) NOT NULL,
  `sequence` int(10) NOT NULL,
  `status` varchar(1) NOT NULL,
  `damagedimageid` int(11) NOT NULL,
  `damagedtext` text NOT NULL,
  `arrivaltime` datetime NOT NULL,
  `departuretime` datetime NOT NULL,
  `place` varchar(50) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `place_lng` double NOT NULL,
  `place_lat` double NOT NULL,
  `timetaken` double NOT NULL,
  `miles` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookingid` (`bookingid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_bookinglegdocs
CREATE TABLE IF NOT EXISTS `hallmark_bookinglegdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bookinglegid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_bookingpod
CREATE TABLE IF NOT EXISTS `hallmark_bookingpod` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bookingid` int(10) NOT NULL,
  `documentid` int(10) NOT NULL,
  `poddate` date DEFAULT NULL,
  `reference` varchar(60) DEFAULT NULL,
  `notes` text,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerid` (`bookingid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_bookingstatus
CREATE TABLE IF NOT EXISTS `hallmark_bookingstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `workflowrole` varchar(20) DEFAULT NULL,
  `bgcolour` varchar(20) DEFAULT NULL,
  `fgcolour` varchar(20) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `emailcontent` text,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_chat
CREATE TABLE IF NOT EXISTS `hallmark_chat` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `memberid` int(10) NOT NULL,
  `status` varchar(1) NOT NULL,
  `completeddatetime` datetime NOT NULL,
  `createddate` datetime NOT NULL,
  `message` tinytext,
  `metacreateddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_customer
CREATE TABLE IF NOT EXISTS `hallmark_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `street` varchar(60) DEFAULT NULL,
  `address2` varchar(60) DEFAULT NULL,
  `town` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `county` varchar(30) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `telephone` varchar(12) DEFAULT NULL,
  `addressextra` varchar(12) DEFAULT NULL,
  `fax` varchar(12) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `email2` varchar(60) DEFAULT NULL,
  `podfolder` varchar(100) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `accountcode` varchar(20) DEFAULT NULL,
  `nominalledgercode` varchar(20) DEFAULT NULL,
  `collectionpoint` varchar(100) DEFAULT NULL,
  `collectionpoint_lat` double DEFAULT NULL,
  `collectionpoint_lng` double DEFAULT NULL,
  `deliverypoint_lat` double DEFAULT NULL,
  `deliverypoint_lng` double DEFAULT NULL,
  `deliverypoint` varchar(100) DEFAULT NULL,
  `selfbilledinvoices` varchar(1) DEFAULT NULL,
  `mobileautoinvoice` varchar(1) DEFAULT NULL,
  `vatregistered` varchar(1) DEFAULT NULL,
  `contact1` varchar(40) DEFAULT NULL,
  `title1` varchar(40) DEFAULT NULL,
  `contact2` varchar(40) DEFAULT NULL,
  `title2` varchar(40) DEFAULT NULL,
  `telephone2` varchar(15) DEFAULT NULL,
  `vatnumber` varchar(20) DEFAULT NULL,
  `vatprefix` varchar(5) DEFAULT NULL,
  `sagecustomerref` varchar(60) DEFAULT NULL,
  `sagetaxcode` varchar(60) DEFAULT NULL,
  `accountstatusid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `imageid` int(11) DEFAULT NULL,
  `terms` text,
  `duedays` int(11) DEFAULT NULL,
  `creditlimit` decimal(10,2) DEFAULT NULL,
  `settlementdiscount` decimal(10,2) DEFAULT NULL,
  `standardratepermile` decimal(10,2) DEFAULT NULL,
  `taxcodeid` int(11) DEFAULT NULL,
  `termsagreed` text,
  `notes` text,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `podfolder` (`podfolder`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_customerdeliverypoint
CREATE TABLE IF NOT EXISTS `hallmark_customerdeliverypoint` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customerid` int(11) DEFAULT NULL,
  `charge` double DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_customerdocs
CREATE TABLE IF NOT EXISTS `hallmark_customerdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customerid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_customerpod
CREATE TABLE IF NOT EXISTS `hallmark_customerpod` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customerid` int(10) NOT NULL,
  `documentid` int(10) NOT NULL,
  `bookingid` int(10) DEFAULT NULL,
  `poddate` date DEFAULT NULL,
  `reference` varchar(60) DEFAULT NULL,
  `notes` text,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerid` (`customerid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_customerpoint
CREATE TABLE IF NOT EXISTS `hallmark_customerpoint` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deliverypointid` int(11) DEFAULT NULL,
  `point` varchar(50) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deliverypointid` (`deliverypointid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_customerteam
CREATE TABLE IF NOT EXISTS `hallmark_customerteam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_deliverymethod
CREATE TABLE IF NOT EXISTS `hallmark_deliverymethod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_discountband
CREATE TABLE IF NOT EXISTS `hallmark_discountband` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_documents
CREATE TABLE IF NOT EXISTS `hallmark_documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `mimetype` varchar(255) DEFAULT NULL,
  `roleid` varchar(50) DEFAULT NULL,
  `expirydate` date DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `compressed` int(11) DEFAULT NULL,
  `image` longblob,
  `createdby` int(11) DEFAULT NULL,
  `createddate` timestamp NULL DEFAULT NULL,
  `lastmodifiedby` int(11) DEFAULT NULL,
  `lastmodifieddate` timestamp NULL DEFAULT NULL,
  `sessionid` varchar(50) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessionid` (`sessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_driver
CREATE TABLE IF NOT EXISTS `hallmark_driver` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `street` varchar(60) DEFAULT NULL,
  `town` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `county` varchar(30) DEFAULT NULL,
  `addressextra` varchar(30) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `telephone` varchar(14) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `fax` varchar(14) DEFAULT NULL,
  `reference` varchar(60) DEFAULT NULL,
  `usualvehicleid` int(11) DEFAULT NULL,
  `prorataholidayentitlement` int(11) DEFAULT NULL,
  `holidayentitlement` int(11) DEFAULT NULL,
  `usualtrailerid` int(11) DEFAULT NULL,
  `qualifications` text,
  `hazardousqualifications` varchar(1) DEFAULT NULL,
  `agencydriver` varchar(1) DEFAULT NULL,
  `hgvlicenceexpire` date DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_driverdocs
CREATE TABLE IF NOT EXISTS `hallmark_driverdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `driverid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_errors
CREATE TABLE IF NOT EXISTS `hallmark_errors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `memberid` int(11) DEFAULT NULL,
  `description` text,
  `createddate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_filter
CREATE TABLE IF NOT EXISTS `hallmark_filter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `memberid` int(11) NOT NULL,
  `pageid` int(11) NOT NULL,
  `description` varchar(60) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_filterdata
CREATE TABLE IF NOT EXISTS `hallmark_filterdata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filterid` int(11) NOT NULL,
  `columnname` varchar(60) DEFAULT NULL,
  `value` text,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_holiday
CREATE TABLE IF NOT EXISTS `hallmark_holiday` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `memberid` int(11) DEFAULT NULL,
  `year` int(11) unsigned DEFAULT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `daystaken` float DEFAULT NULL,
  `accepteddate` date DEFAULT NULL,
  `acceptedby` int(11) DEFAULT NULL,
  `requesteddate` date DEFAULT NULL,
  `rejecteddate` date DEFAULT NULL,
  `rejectedby` int(11) DEFAULT NULL,
  `reason` text,
  `enddate_half` int(11) DEFAULT NULL,
  `startdate_half` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_images
CREATE TABLE IF NOT EXISTS `hallmark_images` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `path` char(255) DEFAULT '',
  `mimetype` char(50) DEFAULT '',
  `name` char(255) DEFAULT '',
  `imgwidth` smallint(4) DEFAULT '0',
  `imgheight` smallint(4) DEFAULT '0',
  `tag` char(255) DEFAULT '',
  `description` char(255) DEFAULT '',
  `image` longblob,
  `createddate` timestamp NULL DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ID` (`id`),
  FULLTEXT KEY `search_index` (`name`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_invoice
CREATE TABLE IF NOT EXISTS `hallmark_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerid` int(11) NOT NULL,
  `revision` int(11) NOT NULL,
  `orderdate` date NOT NULL,
  `emaileddate` datetime NOT NULL,
  `emailed` varchar(1) NOT NULL,
  `emailfailedreason` text NOT NULL,
  `yourordernumber` varchar(50) NOT NULL,
  `paid` varchar(1) NOT NULL,
  `exported` varchar(1) NOT NULL,
  `downloaded` varchar(1) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `takenbyid` int(11) NOT NULL,
  `deliverycharge` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `converteddatetime` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customerid` (`customerid`),
  KEY `takenbyid` (`takenbyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_invoicedocs
CREATE TABLE IF NOT EXISTS `hallmark_invoicedocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `invoiceid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_invoiceitem
CREATE TABLE IF NOT EXISTS `hallmark_invoiceitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `description` text NOT NULL,
  `priceeach` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `nominalledgercodeid` int(11) NOT NULL,
  `linetotal` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `vatrate` decimal(10,2) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customerid` (`invoiceid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_loadtype
CREATE TABLE IF NOT EXISTS `hallmark_loadtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_loginaudit
CREATE TABLE IF NOT EXISTS `hallmark_loginaudit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberid` int(10) unsigned NOT NULL,
  `timeon` datetime DEFAULT NULL,
  `timeoff` datetime DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_members
CREATE TABLE IF NOT EXISTS `hallmark_members` (
  `member_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `login` varchar(100) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `title` varchar(60) DEFAULT NULL,
  `imageid` int(11) DEFAULT NULL,
  `customerid` int(11) DEFAULT NULL,
  `supplierid` int(11) DEFAULT NULL,
  `driverid` int(11) DEFAULT NULL,
  `prorataholidayentitlement` int(11) unsigned DEFAULT NULL,
  `holidayentitlement` int(11) unsigned DEFAULT NULL,
  `officeid` int(11) DEFAULT NULL,
  `description` text,
  `lastaccessdate` datetime DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `postcode` varchar(8) DEFAULT NULL,
  `systemuser` varchar(1) DEFAULT NULL,
  `accepted` varchar(1) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `guid` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `landline` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `dateofbirth` datetime NOT NULL,
  `notes` text NOT NULL,
  `address` text NOT NULL,
  `loginauditid` int(11) NOT NULL,
  `postcode_lat` float NOT NULL,
  `postcode_lng` float NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_messages
CREATE TABLE IF NOT EXISTS `hallmark_messages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `from_member_id` int(11) DEFAULT NULL,
  `to_member_id` int(11) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `message` text,
  `status` varchar(1) DEFAULT NULL,
  `deleted` varchar(1) DEFAULT NULL,
  `action` text,
  `createddate` timestamp NULL DEFAULT NULL,
  `replied` varchar(1) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `to_member_id` (`to_member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_nominalledgercode
CREATE TABLE IF NOT EXISTS `hallmark_nominalledgercode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_pagenavigation
CREATE TABLE IF NOT EXISTS `hallmark_pagenavigation` (
  `pagenavigationid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL,
  `childpageid` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `pagetype` varchar(1) DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `divider` int(11) DEFAULT NULL,
  `target` varchar(50) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`pagenavigationid`),
  UNIQUE KEY `ix_pagenav` (`pageid`,`childpageid`,`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_pageroles
CREATE TABLE IF NOT EXISTS `hallmark_pageroles` (
  `pageroleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL,
  `roleid` varchar(20) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`pageroleid`),
  UNIQUE KEY `ix_pageroles` (`pageid`,`roleid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_pages
CREATE TABLE IF NOT EXISTS `hallmark_pages` (
  `pageid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pagename` varchar(50) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `mobilepagename` varchar(50) DEFAULT NULL,
  `content` text,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`pageid`),
  UNIQUE KEY `ix_page` (`pagename`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_pricebreak
CREATE TABLE IF NOT EXISTS `hallmark_pricebreak` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productid` int(11) NOT NULL,
  `priceeach` decimal(10,2) DEFAULT NULL,
  `qtyfrom` int(11) DEFAULT NULL,
  `qtyto` int(11) DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_product
CREATE TABLE IF NOT EXISTS `hallmark_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupcode` varchar(30) DEFAULT NULL,
  `productcode` varchar(30) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `mainsupplierpartnumber` varchar(30) DEFAULT NULL,
  `supplierid` int(11) DEFAULT NULL,
  `estimatedcost` decimal(10,2) DEFAULT NULL,
  `rspnet` decimal(10,2) DEFAULT NULL,
  `imageid` int(11) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productcode` (`productcode`),
  UNIQUE KEY `groupcode` (`groupcode`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_productdocs
CREATE TABLE IF NOT EXISTS `hallmark_productdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `productid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_proforma
CREATE TABLE IF NOT EXISTS `hallmark_proforma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplierid` int(11) NOT NULL,
  `revision` int(11) NOT NULL,
  `orderdate` date NOT NULL,
  `yourordernumber` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `takenbyid` int(11) NOT NULL,
  `deliverycharge` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `converteddatetime` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customerid` (`supplierid`),
  KEY `takenbyid` (`takenbyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_proformadocs
CREATE TABLE IF NOT EXISTS `hallmark_proformadocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `proformaid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_proformaitem
CREATE TABLE IF NOT EXISTS `hallmark_proformaitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proformaid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `description` text NOT NULL,
  `priceeach` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `linetotal` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `vatrate` decimal(10,2) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customerid` (`proformaid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_rate
CREATE TABLE IF NOT EXISTS `hallmark_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_roles
CREATE TABLE IF NOT EXISTS `hallmark_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `roleid` varchar(20) DEFAULT '',
  `systemrole` varchar(1) DEFAULT NULL,
  `defaultpageid` int(11) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_siteconfig
CREATE TABLE IF NOT EXISTS `hallmark_siteconfig` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `domainurl` varchar(60) DEFAULT NULL,
  `address` text,
  `maintelephone` varchar(14) DEFAULT NULL,
  `bookingprefix` varchar(5) DEFAULT NULL,
  `trafficofficetelephone1` varchar(14) DEFAULT NULL,
  `trafficofficetelephone2` varchar(14) DEFAULT NULL,
  `fax` varchar(14) DEFAULT NULL,
  `accountsemail` varchar(60) DEFAULT NULL,
  `trafficemail` varchar(60) DEFAULT NULL,
  `website` varchar(60) DEFAULT NULL,
  `vatregnumber` varchar(20) DEFAULT NULL,
  `timezoneoffset` varchar(10) DEFAULT NULL,
  `vatprefix` varchar(2) DEFAULT NULL,
  `sslencryption` varchar(1) DEFAULT NULL,
  `companynumber` varchar(20) DEFAULT NULL,
  `currentrhaterms` varchar(20) DEFAULT NULL,
  `financialyearend` date DEFAULT NULL,
  `rhamembershipnumber` varchar(20) DEFAULT NULL,
  `payereference` varchar(20) DEFAULT NULL,
  `bank` varchar(30) DEFAULT NULL,
  `bankaccountnumber` varchar(30) DEFAULT NULL,
  `basepostcode` varchar(8) DEFAULT NULL,
  `banksortcode` varchar(8) DEFAULT NULL,
  `emailfooter` text,
  `termsandconditions` text,
  `webbookingconfirmation` text,
  `lastschedulerun` date DEFAULT NULL,
  `runscheduledays` int(11) DEFAULT NULL,
  `averagewaittime` int(11) DEFAULT NULL,
  `defaultworktype` int(11) DEFAULT NULL,
  `vatrate` decimal(10,2) DEFAULT NULL,
  `defaultprofitmargin` decimal(10,2) DEFAULT NULL,
  `defaultwagesmargin` decimal(10,2) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_supplier
CREATE TABLE IF NOT EXISTS `hallmark_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(50) DEFAULT NULL,
  `accountnumber` varchar(15) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstname` varchar(25) DEFAULT NULL,
  `lastname` varchar(25) DEFAULT NULL,
  `invoiceaddress1` varchar(60) DEFAULT NULL,
  `invoiceaddress2` varchar(60) NOT NULL,
  `invoiceaddress3` varchar(60) DEFAULT NULL,
  `invoicecity` varchar(30) DEFAULT NULL,
  `invoicecountry` varchar(30) DEFAULT NULL,
  `invoicepostcode` varchar(12) DEFAULT NULL,
  `email1` varchar(60) DEFAULT NULL,
  `telephone1` varchar(15) DEFAULT NULL,
  `fax1` varchar(15) DEFAULT NULL,
  `deliverymethodid` int(11) DEFAULT NULL,
  `discountbandid` int(11) DEFAULT NULL,
  `deliveryaddress1` varchar(60) DEFAULT NULL,
  `deliveryaddress2` varchar(60) DEFAULT NULL,
  `deliveryaddress3` varchar(60) DEFAULT NULL,
  `deliverycity` varchar(20) DEFAULT NULL,
  `deliverycountry` varchar(20) DEFAULT NULL,
  `deliverypostcode` varchar(12) DEFAULT NULL,
  `email2` varchar(60) DEFAULT NULL,
  `telephone2` varchar(12) DEFAULT NULL,
  `customerteamid` int(11) DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `accountnumber` (`accountnumber`),
  FULLTEXT KEY `name2` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_supplierdocs
CREATE TABLE IF NOT EXISTS `hallmark_supplierdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplierid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_tablerate
CREATE TABLE IF NOT EXISTS `hallmark_tablerate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicletypeid` int(11) NOT NULL DEFAULT '0',
  `rateid` int(11) NOT NULL DEFAULT '0',
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicletypeid_rateid` (`vehicletypeid`,`rateid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_taxcode
CREATE TABLE IF NOT EXISTS `hallmark_taxcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_trailer
CREATE TABLE IF NOT EXISTS `hallmark_trailer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `registration` varchar(10) NOT NULL,
  `description` varchar(60) NOT NULL,
  `manufacturer` varchar(20) DEFAULT NULL,
  `purchasedate` date DEFAULT NULL,
  `trailertypeid` int(11) DEFAULT NULL,
  `purchaseprice` decimal(10,2) DEFAULT NULL,
  `mpg` decimal(10,2) DEFAULT NULL,
  `presentprice` decimal(10,2) DEFAULT NULL,
  `grossweight` decimal(10,2) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `subcontractor` varchar(1) DEFAULT NULL,
  `active` varchar(1) DEFAULT NULL,
  `notes` text,
  `ystachometer` int(11) DEFAULT NULL,
  `capacity` decimal(10,3) DEFAULT NULL,
  `usualdriverid` int(11) DEFAULT NULL,
  `usualtrailerid` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration` (`registration`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_trailerdocs
CREATE TABLE IF NOT EXISTS `hallmark_trailerdocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trailerid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_trailertype
CREATE TABLE IF NOT EXISTS `hallmark_trailertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `allegrodayrate` decimal(10,4) DEFAULT NULL,
  `agencydayrate` decimal(10,4) DEFAULT NULL,
  `trailercostpermile` decimal(10,4) DEFAULT NULL,
  `overheadcostpermile` decimal(10,4) DEFAULT NULL,
  `standardratepermile` decimal(10,4) DEFAULT NULL,
  `fuelcostpermile` decimal(10,4) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_trailerunavailability
CREATE TABLE IF NOT EXISTS `hallmark_trailerunavailability` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `reasonid` int(11) DEFAULT NULL,
  `trailerid` int(11) DEFAULT NULL,
  `supplierid` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `ordernumber` varchar(30) DEFAULT NULL,
  `invoicenumber` varchar(30) DEFAULT NULL,
  `defectnumber` varchar(7) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `workcarriedout` text,
  `totalcost` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicleid` (`trailerid`),
  KEY `reasonid` (`reasonid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_trailerunavailabilitydocs
CREATE TABLE IF NOT EXISTS `hallmark_trailerunavailabilitydocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `maintenanceid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_trailerunavailabilityreasons
CREATE TABLE IF NOT EXISTS `hallmark_trailerunavailabilityreasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `defectnumberrequired` varchar(1) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_tyreunavailability
CREATE TABLE IF NOT EXISTS `hallmark_tyreunavailability` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `reasonid` int(11) DEFAULT NULL,
  `trailerid` int(11) DEFAULT NULL,
  `vehicleid` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `ordernumber` varchar(30) DEFAULT NULL,
  `invoicenumber` varchar(30) DEFAULT NULL,
  `defectnumber` varchar(7) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `workcarriedout` text,
  `totalcost` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicleid` (`trailerid`),
  KEY `reasonid` (`reasonid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_tyreunavailabilitydocs
CREATE TABLE IF NOT EXISTS `hallmark_tyreunavailabilitydocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `maintenanceid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_tyreunavailabilityreasons
CREATE TABLE IF NOT EXISTS `hallmark_tyreunavailabilityreasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `defectnumberrequired` varchar(1) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_useragent
CREATE TABLE IF NOT EXISTS `hallmark_useragent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `useragent` varchar(250) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `useragent` (`useragent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_userroles
CREATE TABLE IF NOT EXISTS `hallmark_userroles` (
  `userroleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roleid` varchar(20) DEFAULT NULL,
  `memberid` int(11) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`userroleid`),
  UNIQUE KEY `ix_userroles` (`roleid`,`memberid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_vehicle
CREATE TABLE IF NOT EXISTS `hallmark_vehicle` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `registration` varchar(10) NOT NULL,
  `description` varchar(60) NOT NULL,
  `manufacturer` varchar(20) DEFAULT NULL,
  `mobileno` varchar(20) DEFAULT NULL,
  `purchasedate` date DEFAULT NULL,
  `maxpallets` int(11) DEFAULT NULL,
  `purchaseprice` decimal(10,2) DEFAULT NULL,
  `mpg` decimal(10,2) DEFAULT NULL,
  `presentprice` decimal(10,2) DEFAULT NULL,
  `grossweight` decimal(10,2) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `mork` varchar(1) DEFAULT NULL,
  `subcontractor` varchar(1) DEFAULT NULL,
  `active` varchar(1) DEFAULT NULL,
  `notes` text,
  `ystachometer` int(11) DEFAULT NULL,
  `vehicletypeid` int(11) DEFAULT NULL,
  `capacity` decimal(10,3) DEFAULT NULL,
  `usualdriverid` int(11) DEFAULT NULL,
  `usualtrailerid` int(11) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration` (`registration`),
  KEY `vehicletype` (`vehicletypeid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_vehicledocs
CREATE TABLE IF NOT EXISTS `hallmark_vehicledocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vehicleid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_vehicletype
CREATE TABLE IF NOT EXISTS `hallmark_vehicletype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `imageid` int(11) DEFAULT NULL,
  `allegrodayrate` decimal(10,4) DEFAULT NULL,
  `agencydayrate` decimal(10,4) DEFAULT NULL,
  `vehiclecostpermile` decimal(10,4) DEFAULT NULL,
  `overheadcostpermile` decimal(10,4) DEFAULT NULL,
  `standardratepermile` decimal(10,4) DEFAULT NULL,
  `fuelcostpermile` decimal(10,4) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_vehicleunavailability
CREATE TABLE IF NOT EXISTS `hallmark_vehicleunavailability` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `reasonid` int(11) DEFAULT NULL,
  `vehicleid` int(11) DEFAULT NULL,
  `supplierid` int(11) DEFAULT NULL,
  `workcarriedout` text,
  `totalcost` decimal(10,2) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `ordernumber` varchar(30) DEFAULT NULL,
  `invoicenumber` varchar(30) DEFAULT NULL,
  `defectnumber` varchar(7) DEFAULT NULL,
  `metacreateddate` datetime DEFAULT NULL,
  `metamodifieddate` datetime DEFAULT NULL,
  `metacreateduserid` int(11) DEFAULT NULL,
  `metamodifieduserid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicleid` (`vehicleid`),
  KEY `reasonid` (`reasonid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_vehicleunavailabilitydocs
CREATE TABLE IF NOT EXISTS `hallmark_vehicleunavailabilitydocs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `maintenanceid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_vehicleunavailabilityreasons
CREATE TABLE IF NOT EXISTS `hallmark_vehicleunavailabilityreasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `defectnumberrequired` varchar(1) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for table hallmark.hallmark_worktype
CREATE TABLE IF NOT EXISTS `hallmark_worktype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `nominalledgercodeid` int(11) DEFAULT NULL,
  `metacreateddate` datetime NOT NULL,
  `metacreateduserid` int(11) NOT NULL,
  `metamodifieddate` datetime NOT NULL,
  `metamodifieduserid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Data exporting was unselected.


-- Dumping structure for trigger hallmark.halfcolumn_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `halfcolumn_insert` BEFORE INSERT ON `hallmark_members` FOR EACH ROW BEGIN
    SET NEW.fullname = CONCAT(NEW.firstname, ' ', NEW.lastname);
  END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger hallmark.halfcolumn_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `halfcolumn_update` BEFORE UPDATE ON `hallmark_members` FOR EACH ROW BEGIN
    SET NEW.fullname = CONCAT(NEW.firstname, ' ', NEW.lastname);
  END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
