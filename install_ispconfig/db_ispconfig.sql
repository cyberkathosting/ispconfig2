-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2005 at 05:49 PM
-- Server version: 4.0.24
-- PHP Version: 5.0.4
--
-- Database: `db_ispconfig`
--

-- --------------------------------------------------------

--
-- Table structure for table `del_status`
--

CREATE TABLE `del_status` (
  `id` int(11) NOT NULL auto_increment,
  `doc_id` int(11) NOT NULL default '0',
  `doctype_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `pfad` varchar(255) NOT NULL default '',
  `web_host` varchar(255) NOT NULL default '',
  `web_domain` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `del_status`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_a`
--

CREATE TABLE `dns_a` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1018',
  `host` varchar(255) default NULL,
  `ip_adresse` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `dns_a`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_cname`
--

CREATE TABLE `dns_cname` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1019',
  `host` varchar(255) default NULL,
  `ziel` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `dns_cname`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_dep`
--

CREATE TABLE `dns_dep` (
  `dep_id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `parent_doc_id` int(10) unsigned NOT NULL default '0',
  `parent_doctype_id` int(10) unsigned NOT NULL default '0',
  `parent_tree_id` int(11) NOT NULL default '0',
  `child_doc_id` int(11) NOT NULL default '0',
  `child_doctype_id` int(11) NOT NULL default '0',
  `child_tree_id` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`dep_id`),
  KEY `userid` (`userid`),
  KEY `groupid` (`groupid`),
  KEY `parent_doc_id` (`parent_doc_id`),
  KEY `parent_doctype_id` (`parent_doctype_id`),
  KEY `parent_tree_id` (`parent_tree_id`),
  KEY `child_doc_id` (`child_doc_id`),
  KEY `child_doctype_id` (`child_doctype_id`),
  KEY `child_tree_id` (`child_tree_id`),
  KEY `status` (`status`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `dns_dep`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_isp_dns`
--

CREATE TABLE `dns_isp_dns` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1016',
  `dns_soa` varchar(255) default NULL,
  `dns_refresh` varchar(255) default '28800',
  `dns_retry` varchar(255) default '7200',
  `dns_expire` varchar(255) default '604800',
  `dns_ttl` varchar(255) default '86400',
  `dns_ns1` varchar(255) default NULL,
  `dns_ns2` varchar(255) default NULL,
  `dns_adminmail` varchar(255) default NULL,
  `server_id` varchar(255) default NULL,
  `status` char(1) default NULL,
  `dns_soa_ip` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `dns_isp_dns`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_mx`
--

CREATE TABLE `dns_mx` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1020',
  `host` varchar(255) default NULL,
  `prioritaet` varchar(255) default NULL,
  `mailserver` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `dns_mx`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_nodes`
--

CREATE TABLE `dns_nodes` (
  `tree_id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `parent` varchar(100) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  `doctype_id` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '1',
  `icon` varchar(255) NOT NULL default '',
  `modul` varchar(255) NOT NULL default '',
  `doc_id` bigint(21) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tree_id`),
  UNIQUE KEY `tree_id_2` (`tree_id`),
  KEY `tree_id` (`tree_id`,`userid`,`groupid`),
  KEY `doc_id` (`doc_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `dns_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_secondary`
--

CREATE TABLE `dns_secondary` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1028',
  `domain` varchar(255) default NULL,
  `master_ip` varchar(255) default NULL,
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `dns_secondary`
--


-- --------------------------------------------------------

--
-- Table structure for table `dns_spf`
--

CREATE TABLE `dns_spf` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1031',
  `host` varchar(255) default NULL,
  `a` varchar(255) default NULL,
  `mx` varchar(255) default NULL,
  `ptr` varchar(255) default NULL,
  `a_break` text,
  `mx_break` text,
  `ip4_break` text,
  `include_break` varchar(255) default NULL,
  `all_` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `dns_spf`
--


-- --------------------------------------------------------

--
-- Table structure for table `doctype`
--

CREATE TABLE `doctype` (
  `doctype_id` int(10) unsigned NOT NULL auto_increment,
  `userid` bigint(21) NOT NULL default '0',
  `groupid` bigint(21) NOT NULL default '0',
  `doctype_modul` varchar(255) NOT NULL default '',
  `doctype_name` varchar(255) NOT NULL default '',
  `doctype_title` varchar(255) NOT NULL default '',
  `doctype_def` text NOT NULL,
  `doctype_tree` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`doctype_id`),
  UNIQUE KEY `doctype_id_2` (`doctype_id`),
  KEY `doctype_id` (`doctype_id`,`userid`,`groupid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `doctype`
--

INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(3, 1, 1, 'sys', 'news', 'News', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:4:"news";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:8:"sys_news";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:5:"titel";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Titel";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}i:1;O:9:"dateField":15:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:6:"format";s:5:"d.m.Y";s:4:"name";s:5:"datum";s:4:"type";s:9:"dateField";s:5:"title";s:5:"Datum";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}i:2;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";s:0:"";s:4:"rows";s:1:"8";s:4:"name";s:8:"newstext";s:4:"type";s:8:"longText";s:5:"title";s:8:"Newstext";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}i:3;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:7:"visible";s:4:"type";s:13:"checkboxField";s:5:"title";s:8:"Sichtbar";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:4:"News";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}}s:5:"title";s:4:"News";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:9:"text2.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1, 1, 1, 'sys', 'user', 'User Manager', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:4:"user";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:8:"sys_user";s:10:"form_width";s:3:"450";s:4:"deck";a:4:{i:0;O:4:"deck":5:{s:8:"elements";a:7:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"20";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:8:"username";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Username";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"20";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"20";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:4:"pwcl";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"20";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:7:"gueltig";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Account aktiv";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"30";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:5:"email";s:4:"type";s:9:"shortText";s:5:"title";s:14:"Email (prefix)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:1:"r";s:5:"Lesen";s:2:"rw";s:17:"Lesen & Schreiben";s:3:"rwa";s:13:"Administrator";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:5:"perms";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Rechte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:11:"optionField":21:{s:11:"option_type";s:6:"option";s:6:"values";N;s:6:"source";s:2:"db";s:12:"source_table";s:11:"sys_modules";s:11:"value_field";s:12:"module_title";s:8:"id_field";s:11:"module_name";s:9:"css_class";s:0:"";s:4:"size";s:1:"5";s:8:"multiple";s:1:"1";s:5:"order";N;s:4:"name";s:7:"modules";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Module";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:10:"newsletter";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"Newsletter";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:10:"Stammdaten";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:1;O:4:"deck":5:{s:8:"elements";a:14:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:4:"Frau";s:4:"Frau";s:6:"\r\nHerr";s:4:"Herr";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:6:"anrede";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Anrede";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"vorname";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Vorname";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"name";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Nachname";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"strasse";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Strasse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"5";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:3:"plz";s:4:"type";s:9:"shortText";s:5:"title";s:3:"PLZ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"5";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:3:"ort";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Ort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:11:"Deutschland";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"land";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Land";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"telefon";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Telefon";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:3:"fax";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Fax";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"email_home";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:6:"trenn1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"alter1";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Alter";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:5:"woher";s:4:"type";s:9:"shortText";s:5:"title";s:15:"Empfohlen durch";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:13;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:15:"passwortRecover";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Passwortsatz";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:7:"Adresse";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:3:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:1:{s:4:"blau";s:4:"Blau";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:6:"design";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Design";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:6:"025CCA";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"6";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"nav_color";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Farbe 1";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"6";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:6:"E4E4E4";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"6";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"box_color";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Farbe 2";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"6";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:6:"Design";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:3;O:4:"deck":5:{s:8:"elements";a:6:{i:0;O:12:"integerField":14:{s:5:"value";s:7:"5120000";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"30";s:4:"name";s:7:"mailmax";s:4:"type";s:12:"integerField";s:5:"title";s:13:"Postfach (KB)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:14:"bookmark_order";s:4:"type";s:13:"checkboxField";s:5:"title";s:18:"Bookmarks Sortiert";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:4:"news";s:4:"type";s:13:"checkboxField";s:5:"title";s:4:"News";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";N;s:6:"source";s:2:"db";s:12:"source_table";s:11:"sys_modules";s:11:"value_field";s:12:"module_title";s:8:"id_field";s:11:"module_name";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:5:"modul";s:4:"type";s:11:"optionField";s:5:"title";s:10:"Startseite";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:9:"ispconfig";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"50";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"site";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Sitename";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:13:"ispconfig.org";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"domain";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:8:"Optionen";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}}s:5:"title";s:12:"User Manager";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:4:"auth";s:12:"event_insert";s:13:"check_adduser";s:12:"event_update";s:16:"check_updateuser";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1000, 1, 0, '', 'dummy', 'dummy', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(10, 1, 0, 'help', 'documents', 'Online Hilfe', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:4:"help";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:9:"documents";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:14:"help_documents";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:2:"t2";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"h_title";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Titel";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}i:1;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";s:4:"rows";s:2:"15";s:4:"name";s:6:"h_text";s:4:"type";s:8:"longText";s:5:"title";s:12:"Beschreibung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}}s:5:"title";s:5:"Hilfe";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}}s:5:"title";s:12:"Online Hilfe";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:8:"help.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(2, 1, 0, 'sys', 'modules', 'Modul Manager', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:7:"modules";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:11:"sys_modules";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:6:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"module_name";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"module_title";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Titel";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:14:"module_enabled";s:4:"type";s:13:"checkboxField";s:5:"title";s:5:"Aktiv";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:6:"trenn1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:1:"p";s:8:"Physisch";s:1:"v";s:8:"Virtuell";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:11:"module_type";s:4:"type";s:11:"optionField";s:5:"title";s:3:"Typ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"module_path";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Pfad";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:13:"Einstellungen";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}}s:5:"title";s:13:"Modul Manager";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:11:"fenster.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1010, 1, 1, 'sys', 'server', 'ISP Server', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:6:"server";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:10:"isp_server";s:10:"form_width";s:3:"450";s:4:"deck";a:9:{i:0;O:4:"deck":5:{s:8:"elements";a:14:{i:0;O:9:"shortText":16:{s:5:"value";s:8:"Server 1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"server_name";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Server Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t5";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:3:"www";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"server_host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"server_domain";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:27:"/^[a-zA-Z0-9\\\\-\\\\.]{3,63}$/";s:10:"reg_fehler";s:51:"Es sind nur folgende Zeichen erlaubt: a-z A-Z 0-9 -";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:7:"0.0.0.0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"15";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"server_ip";s:4:"type";s:9:"shortText";s:5:"title";s:10:"IP Adresse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"15";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:13:"255.255.255.0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"15";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"server_netzmaske";s:4:"type";s:9:"shortText";s:5:"title";s:9:"Netzmaske";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"15";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:2:"de";s:7:"Deutsch";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:14:"server_sprache";s:4:"type";s:11:"optionField";s:5:"title";s:7:"Sprache";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:14:"root@localhost";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:18:"server_admin_email";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Admin Email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:9;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:5:"mysql";s:5:"mySQL";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:14:"server_db_type";s:4:"type";s:11:"optionField";s:5:"title";s:13:"Datenbank Typ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"30";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:14:"server_db_user";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Username";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"30";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:18:"server_db_passwort";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:13;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:14:"server_ipliste";s:4:"type";s:8:"longText";s:5:"title";s:8:"IP Liste";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:9:"descField":14:{s:5:"value";s:16:"hinweis_ip_liste";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:11:"txt_ipliste";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:6:"Server";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:1;O:4:"deck":5:{s:8:"elements";a:10:{i:0;O:9:"shortText":16:{s:5:"value";s:5:"httpd";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"server_httpd_user";s:4:"type";s:9:"shortText";s:5:"title";s:10:"httpd User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:5:"httpd";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"30";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:18:"server_httpd_group";s:4:"type";s:9:"shortText";s:5:"title";s:11:"httpd Group";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:10:"/etc/httpd";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:22:"server_path_httpd_conf";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Conf. Dir.";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:26:"/etc/httpd/conf/httpd.conf";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:15:"dist_httpd_conf";s:4:"type";s:9:"shortText";s:5:"title";s:10:"httpd.conf";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:9:"/home/www";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:22:"server_path_httpd_root";s:4:"type";s:9:"shortText";s:5:"title";s:13:"Document Root";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:20:"/usr/local/frontpage";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:21:"server_path_frontpage";s:4:"type";s:9:"shortText";s:5:"title";s:14:"Frontpage Pfad";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:15:"/home/www/error";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:23:"server_path_httpd_error";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Error Pages";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:26:"/var/logs/httpd/access.log";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:21:"server_path_httpd_log";s:4:"type";s:9:"shortText";s:5:"title";s:10:"access Log";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:9;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:19:"server_httpd_suexec";s:4:"type";s:13:"checkboxField";s:5:"title";s:6:"Suexec";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:3:"Web";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:8:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:8:"sendmail";s:8:"Sendmail";s:7:"postfix";s:7:"Postfix";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:10:"server_mta";s:4:"type";s:11:"optionField";s:5:"title";s:7:"MTA Typ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:18:"/etc/virtusertable";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:30:"server_sendmail_virtuser_datei";s:4:"type";s:9:"shortText";s:5:"title";s:14:"Virtuser Datei";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:21:"/etc/mail/sendmail.cw";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:18:"server_sendmail_cw";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Sendmail CW";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:16:"/var/log/maillog";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"dist_mail_log";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Mail Log";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:20:"server_mail_log_save";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Log speichern";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:11:"use_maildir";s:4:"type";s:13:"checkboxField";s:5:"title";s:7:"Maildir";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:22:"admispconfig@localhost";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"virusadmin";s:4:"type";s:9:"shortText";s:5:"title";s:17:"Antivir Mailadmin";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:17:"spamfilter_enable";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"Spamfilter";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:5:"EMail";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:3;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:7:"proftpd";s:6:"ProFTP";s:6:"vsftpd";s:6:"VS-FTP";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:14:"server_ftp_typ";s:4:"type";s:11:"optionField";s:5:"title";s:12:"FTP Programm";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:17:"/etc/proftpd.conf";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:25:"server_proftpd_conf_datei";s:4:"type";s:9:"shortText";s:5:"title";s:15:"FTP conf. Datei";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:21:"/var/logs/proftpd.log";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:18:"server_proftpd_log";s:4:"type";s:9:"shortText";s:5:"title";s:16:"ProFTP Log Datei";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:19:"server_ftp_log_save";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Log speichern";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:3:"FTP";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:4;O:4:"deck":5:{s:8:"elements";a:10:{i:0;O:9:"shortText":16:{s:5:"value";s:4:"bind";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"server_bind_user";s:4:"type";s:9:"shortText";s:5:"title";s:9:"BIND User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:4:"bind";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"server_bind_group";s:4:"type";s:9:"shortText";s:5:"title";s:10:"BIND Group";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t6";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:15:"/etc/named.conf";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:22:"server_bind_named_conf";s:4:"type";s:9:"shortText";s:5:"title";s:10:"named.conf";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:9:"/etc/bind";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:24:"server_bind_zonefile_dir";s:4:"type";s:9:"shortText";s:5:"title";s:14:"Zonefiles Dir.";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t7";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:23:"server_bind_ns1_default";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Default Ns1";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:23:"server_bind_ns2_default";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Default Ns2";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:29:"server_bind_adminmail_default";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Admin Email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:9;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:23:"server_bind_standard_mx";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"Standard MX";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:3:"DNS";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:5;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1011";s:4:"view";s:4:"list";s:6:"fields";s:15:"titel,on_action";s:9:"css_class";s:2:"t2";s:4:"name";s:6:"action";s:4:"type";s:11:"attachField";s:5:"title";s:6:"Aktion";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:8:"Aktionen";s:7:"visible";s:1:"0";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:6;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:7:"inaktiv";i:1;s:5:"aktiv";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:23:"server_enable_frontpage";s:4:"type";s:11:"optionField";s:5:"title";s:20:"Frontpage Extensions";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:10:"Funktionen";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:7;O:4:"deck":5:{s:8:"elements";a:65:{i:0;O:9:"shortText":16:{s:5:"value";s:5:"10000";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"userid_von";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Userid von";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:11:"/etc/passwd";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"passwd_datei";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Passwd Datei";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:11:"/etc/shadow";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"shadow_datei";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Shadow Datei";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:5:"10000";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"groupid_von";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Groupid von";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:10:"/etc/group";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"group_datei";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Group Datei";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:9:"127.0.0.1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";s:1:"1";s:4:"name";s:14:"server_soap_ip";s:4:"type";s:9:"shortText";s:5:"title";s:14:"SOAP Server IP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:4:"8080";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";s:1:"1";s:4:"name";s:16:"server_soap_port";s:4:"type";s:9:"shortText";s:5:"title";s:16:"SOAP Server Port";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:13;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:4:"none";s:4:"none";s:8:"blowfish";s:8:"blowfish";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:20:"server_soap_encoding";s:4:"type";s:11:"optionField";s:5:"title";s:13:"SOAP Encoding";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t8";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:15;O:9:"descField":14:{s:5:"value";s:32:"txt_client_salutatory_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:28:"client_salutatory_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:16;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:36:"client_salutatory_email_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:40:"txt_client_salutatory_email_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:17;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:35:"client_salutatory_email_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:39:"txt_client_salutatory_email_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:18;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:27:"client_salutatory_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:31:"txt_client_salutatory_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:19;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:31:"client_salutatory_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:35:"txt_client_salutatory_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:20;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:31:"client_salutatory_email_message";s:4:"type";s:8:"longText";s:5:"title";s:35:"txt_client_salutatory_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:21;O:9:"descField":14:{s:5:"value";s:37:"txt_client_salutatory_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:33:"client_salutatory_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:22;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t9";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:23;O:9:"descField":14:{s:5:"value";s:29:"txt_res_salutatory_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:25:"res_salutatory_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:24;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:33:"res_salutatory_email_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:37:"txt_res_salutatory_email_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:25;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:32:"res_salutatory_email_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:36:"txt_res_salutatory_email_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:26;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:24:"res_salutatory_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:28:"txt_res_salutatory_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:27;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:28:"res_salutatory_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:32:"txt_res_salutatory_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:28;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:28:"res_salutatory_email_message";s:4:"type";s:8:"longText";s:5:"title";s:32:"txt_res_salutatory_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:29;O:9:"descField":14:{s:5:"value";s:34:"txt_res_salutatory_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:30:"res_salutatory_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:30;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"t10";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:31;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:14:"standard_index";s:4:"type";s:8:"longText";s:5:"title";s:18:"txt_standard_index";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:32;O:9:"descField":14:{s:5:"value";s:28:"txt_standard_index_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:24:"standard_index_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:33;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:19:"user_standard_index";s:4:"type";s:8:"longText";s:5:"title";s:23:"txt_user_standard_index";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:34;O:9:"descField":14:{s:5:"value";s:33:"txt_user_standard_index_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:29:"user_standard_index_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:35;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"t11";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:36;O:9:"descField":14:{s:5:"value";s:33:"txt_traffic_suspension_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:29:"traffic_suspension_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:37;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:31:"traffic_suspension_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:35:"txt_traffic_suspension_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:38;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:30:"traffic_suspension_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:34:"txt_traffic_suspension_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:39;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:28:"traffic_suspension_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:32:"txt_traffic_suspension_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:40;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:32:"traffic_suspension_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:36:"txt_traffic_suspension_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:41;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:32:"traffic_suspension_email_message";s:4:"type";s:8:"longText";s:5:"title";s:36:"txt_traffic_suspension_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:42;O:9:"descField":14:{s:5:"value";s:38:"txt_traffic_suspension_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:34:"traffic_suspension_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:43;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"t12";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:44;O:9:"descField":14:{s:5:"value";s:35:"txt_traffic_notification_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:31:"traffic_notification_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:45;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:33:"traffic_notification_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:37:"txt_traffic_notification_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:46;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:32:"traffic_notification_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:36:"txt_traffic_notification_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:47;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:30:"traffic_notification_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:34:"txt_traffic_notification_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:48;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:34:"traffic_notification_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:38:"txt_traffic_notification_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:49;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:34:"traffic_notification_email_message";s:4:"type";s:8:"longText";s:5:"title";s:38:"txt_traffic_notification_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:50;O:9:"descField":14:{s:5:"value";s:40:"txt_traffic_notification_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:36:"traffic_notification_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:51;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"t13";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:52;O:9:"descField":14:{s:5:"value";s:37:"txt_res_traffic_suspension_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:33:"res_traffic_suspension_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:53;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:35:"res_traffic_suspension_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:39:"txt_res_traffic_suspension_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:54;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:34:"res_traffic_suspension_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:38:"txt_res_traffic_suspension_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:55;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:32:"res_traffic_suspension_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:36:"txt_res_traffic_suspension_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:56;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:36:"res_traffic_suspension_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:40:"txt_res_traffic_suspension_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:57;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:36:"res_traffic_suspension_email_message";s:4:"type";s:8:"longText";s:5:"title";s:40:"txt_res_traffic_suspension_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:58;O:9:"descField":14:{s:5:"value";s:42:"txt_res_traffic_suspension_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:38:"res_traffic_suspension_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:59;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"t14";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:60;O:9:"descField":14:{s:5:"value";s:39:"txt_res_traffic_notification_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:35:"res_traffic_notification_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:61;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:37:"res_traffic_notification_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:41:"txt_res_traffic_notification_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:62;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:36:"res_traffic_notification_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:40:"txt_res_traffic_notification_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:63;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:34:"res_traffic_notification_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:38:"txt_res_traffic_notification_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:64;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:38:"res_traffic_notification_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:42:"txt_res_traffic_notification_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:65;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:38:"res_traffic_notification_email_message";s:4:"type";s:8:"longText";s:5:"title";s:42:"txt_res_traffic_notification_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:66;O:9:"descField":14:{s:5:"value";s:44:"txt_res_traffic_notification_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:40:"res_traffic_notification_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:67;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:4:"t115";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:68;O:9:"descField":14:{s:5:"value";s:16:"txt_global_stats";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:12:"global_stats";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:69;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"global_stats_user";s:4:"type";s:9:"shortText";s:5:"title";s:21:"txt_global_stats_user";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:70;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:21:"global_stats_password";s:4:"type";s:9:"shortText";s:5:"title";s:25:"txt_global_stats_password";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:9:"Sonstiges";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:8;O:4:"deck":5:{s:8:"elements";a:16:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"dist";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Dist";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"dist_init_scripts";s:4:"type";s:9:"shortText";s:5:"title";s:17:"dist_init_scripts";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"dist_runlevel";s:4:"type";s:9:"shortText";s:5:"title";s:13:"dist_runlevel";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"dist_smrsh";s:4:"type";s:9:"shortText";s:5:"title";s:10:"dist_smrsh";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"dist_shells";s:4:"type";s:9:"shortText";s:5:"title";s:11:"dist_shells";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:21:"dist_bind_init_script";s:4:"type";s:9:"shortText";s:5:"title";s:21:"dist_bind_init_script";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"dist_bind_pidfile";s:4:"type";s:9:"shortText";s:5:"title";s:17:"dist_bind_pidfile";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:18:"dist_bind_hintfile";s:4:"type";s:9:"shortText";s:5:"title";s:18:"dist_bind_hintfile";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:19:"dist_bind_localfile";s:4:"type";s:9:"shortText";s:5:"title";s:19:"dist_bind_localfile";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:9;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"dist_cron_daemon";s:4:"type";s:9:"shortText";s:5:"title";s:16:"dist_cron_daemon";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:10;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"dist_cron_tab";s:4:"type";s:9:"shortText";s:5:"title";s:13:"dist_cron_tab";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"dist_mysql_group";s:4:"type";s:9:"shortText";s:5:"title";s:16:"dist_mysql_group";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"dist_httpd_daemon";s:4:"type";s:9:"shortText";s:5:"title";s:17:"dist_httpd_daemon";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:13;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"dist_pop3";s:4:"type";s:9:"shortText";s:5:"title";s:9:"dist_pop3";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:14;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:17:"dist_pop3_version";s:4:"type";s:9:"shortText";s:5:"title";s:17:"dist_pop3_version";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:15;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"dist_ftp_version";s:4:"type";s:9:"shortText";s:5:"title";s:16:"dist_ftp_version";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:4:"Dist";s:7:"visible";s:1:"0";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:10:"ISP Server";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:10:"server.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:10:"isp_server";s:12:"event_insert";s:13:"server_insert";s:12:"event_update";s:13:"server_update";s:12:"event_delete";s:0:"";s:10:"event_show";N;s:11:"wysiwyg_lib";i:0;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1011, 1, 1, 'isp', 'isp_actions', 'ISP Aktionen', 'O:3:"doc":17:{s:4:"tree";s:1:"0";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:12:"storage_type";s:2:"db";s:10:"form_width";s:3:"450";s:5:"cache";s:1:"0";s:7:"version";d:1.1;s:6:"userid";s:1:"4";s:7:"groupid";s:1:"0";s:5:"modul";s:3:"isp";s:4:"name";s:11:"isp_actions";s:12:"storage_path";s:15:"isp_isp_actions";s:5:"title";s:12:"ISP Aktionen";s:11:"description";s:0:"";s:4:"icon";s:0:"";s:8:"permtype";N;s:4:"deck";a:1:{i:0;O:4:"deck":3:{s:7:"visible";s:1:"1";s:5:"title";s:6:"Aktion";s:8:"elements";a:6:{i:0;O:9:"shorttext":13:{s:8:"language";s:2:"de";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:9:"maxlength";s:3:"255";s:4:"type";s:9:"shortText";s:4:"name";s:5:"titel";s:11:"description";s:0:"";s:5:"value";s:0:"";s:5:"title";s:5:"Titel";s:9:"css_class";s:0:"";s:8:"password";N;s:6:"search";N;}i:1;O:11:"optionfield":19:{s:8:"language";s:2:"de";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:4:"type";s:11:"optionField";s:4:"name";s:9:"on_action";s:11:"description";s:0:"";s:11:"option_type";s:8:"dropdown";s:6:"values";a:7:{s:4:"zeit";s:4:"Zeit";s:4:"user";s:10:"onUserSave";s:3:"web";s:9:"onWebSave";s:6:"client";s:12:"onClientSave";s:8:"reseller";s:14:"onResellerSave";s:6:"server";s:12:"onServerSave";s:6:"action";s:12:"onActionSave";}s:6:"source";s:4:"list";s:5:"title";s:8:"Auslöser";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:9:"css_class";s:0:"";s:6:"search";N;s:5:"order";N;}i:2;O:14:"seperatorfield":8:{s:8:"language";s:2:"de";s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:4:"type";s:14:"seperatorField";s:4:"name";s:2:"t1";s:5:"width";s:1:"1";s:9:"css_class";s:0:"";}i:3;O:9:"shorttext":13:{s:8:"language";s:2:"de";s:6:"length";s:1:"5";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:9:"maxlength";s:1:"5";s:4:"type";s:9:"shortText";s:4:"name";s:4:"zeit";s:11:"description";s:0:"";s:5:"value";s:5:"00:00";s:5:"title";s:12:"Zeit (00:00)";s:9:"css_class";s:0:"";s:8:"password";N;s:6:"search";N;}i:4;O:9:"shorttext":13:{s:8:"language";s:2:"de";s:6:"length";s:1:"5";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:9:"maxlength";s:1:"5";s:4:"type";s:9:"shortText";s:4:"name";s:9:"intervall";s:11:"description";s:0:"";s:5:"value";s:0:"";s:5:"title";s:19:"Intervall (Minuten)";s:9:"css_class";s:0:"";s:8:"password";N;s:6:"search";N;}i:5;O:9:"shorttext":13:{s:8:"language";s:2:"de";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:9:"maxlength";s:3:"255";s:4:"type";s:9:"shortText";s:4:"name";s:4:"pfad";s:11:"description";s:0:"";s:5:"value";s:0:"";s:5:"title";s:4:"Pfad";s:9:"css_class";s:0:"";s:8:"password";N;s:6:"search";N;}}}}}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1012, 1, 0, 'isp', 'isp_kunde', 'ISP Kunde', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"1";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:9:"isp_kunde";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:13:"isp_isp_kunde";s:10:"form_width";s:3:"450";s:4:"deck";a:5:{i:0;O:4:"deck":5:{s:8:"elements";a:14:{i:0;O:11:"pluginField":13:{s:9:"css_class";s:6:"normal";s:7:"options";N;s:4:"name";s:16:"isp_kundennummer";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"kunde_firma";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Firma";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:2;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:4:{s:4:"Frau";s:4:"Frau";s:4:"Herr";s:4:"Herr";s:5:"Firma";s:5:"Firma";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:12:"kunde_anrede";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Anrede";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"kunde_vorname";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Vorname";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"kunde_name";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"kunde_strasse";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Strasse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:6;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"kunde_plz";s:4:"type";s:9:"shortText";s:5:"title";s:3:"PLZ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"kunde_ort";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Ort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:8;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:14:"kunde_province";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Bundesland";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:9;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"kunde_land";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Land";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:10;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"kunde_telefon";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Telefon";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"kunde_fax";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Fax";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"kunde_email";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:13;O:9:"linkField":14:{s:5:"value";s:7:"http://";s:6:"target";s:6:"_blank";s:9:"css_class";s:0:"";s:4:"name";s:14:"kunde_internet";s:4:"type";s:9:"linkField";s:5:"title";s:8:"Internet";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"20";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:10:"Stammdaten";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:1;O:4:"deck":5:{s:8:"elements";a:1:{i:1;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:17:"isp_kunde_weblist";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:14:"Web Verwaltung";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:9:"descField":14:{s:5:"value";s:29:"zugangsdaten_kunde_einleitung";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:12:"txt_webadmin";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"webadmin_user";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Username";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:17:"webadmin_passwort";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:12:"integerField":14:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:0:"";s:4:"name";s:15:"webadmin_userid";s:4:"type";s:12:"integerField";s:5:"title";s:6:"UserID";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"5";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:12:"Zugangsdaten";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:3;O:4:"deck":5:{s:8:"elements";a:12:{i:0;O:9:"descField":14:{s:5:"value";s:19:"Rechnungsanschrift:";s:9:"css_class";s:3:"t2b";s:9:"alignment";s:4:"left";s:4:"name";s:6:"txt_re";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:14:"rechnung_firma";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Firma";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"rechnung_vorname";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Vorname";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"rechnung_name";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"rechnung_strasse";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Strasse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"rechnung_plz";s:4:"type";s:9:"shortText";s:5:"title";s:3:"PLZ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"rechnung_ort";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Ort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"rechnung_land";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Land";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:4:"tr_9";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:6:{s:9:"Monatlich";s:9:"Monatlich";s:7:"Quartal";s:7:"Quartal";s:12:"Halbjährlich";s:12:"Halbjährlich";s:8:"Jährlich";s:8:"Jährlich";s:8:"Sonstige";s:8:"Sonstige";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:18:"rechnung_intervall";s:4:"type";s:11:"optionField";s:5:"title";s:9:"Intervall";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:11:"doubleField":15:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:0:"";s:8:"currency";s:3:"EUR";s:4:"name";s:14:"rechnung_preis";s:4:"type";s:11:"doubleField";s:5:"title";s:5:"Preis";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:6:{s:10:"bei Erhalt";s:10:"bei Erhalt";s:12:"Netto 7 Tage";s:12:"Netto 7 Tage";s:13:"Netto 15 Tage";s:13:"Netto 15 Tage";s:13:"Netto 30 Tage";s:13:"Netto 30 Tage";s:8:"Sonstige";s:8:"Sonstige";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:28:"rechnung_zahlungsbedingungen";s:4:"type";s:11:"optionField";s:5:"title";s:7:"Zahlung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:8:"Rechnung";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"a";s:10:"perm_write";s:1:"a";}i:4;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:14:"client_traffic";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:9:"Statistik";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:9:"ISP Kunde";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:9:"kunde.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:9:"isp_kunde";s:12:"event_insert";s:12:"kunde_insert";s:12:"event_update";s:12:"kunde_update";s:12:"event_delete";s:12:"kunde_delete";s:10:"event_show";N;s:11:"wysiwyg_lib";i:0;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1013, 1, 0, 'isp', 'isp_web', 'ISP Web', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"1";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:7:"isp_web";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:11:"isp_isp_web";s:10:"form_width";s:3:"450";s:4:"deck";a:8:{i:0;O:4:"deck":5:{s:8:"elements";a:35:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";N;s:6:"source";s:2:"db";s:12:"source_table";s:10:"isp_server";s:11:"value_field";s:11:"server_name";s:8:"id_field";s:6:"doc_id";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:9:"server_id";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:3:"www";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:8:"web_host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:25:"/^[\\\\w0-9\\\\-\\\\.]{0,255}$/";s:10:"reg_fehler";s:53:"Es sind nur folgende Zeichen erlaubt: a-z A-Z 0-9 - .";s:6:"search";s:1:"1";}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"web_domain";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:51:"Es sind nur folgende Zeichen erlaubt: a-z A-Z 0-9 -";s:6:"search";s:1:"1";}i:4;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";N;s:6:"source";s:2:"db";s:12:"source_table";s:13:"isp_server_ip";s:11:"value_field";s:9:"server_ip";s:8:"id_field";s:9:"server_ip";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:6:"web_ip";s:4:"type";s:11:"optionField";s:5:"title";s:10:"IP Adresse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}i:5;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_dns";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"Create DNS";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:10:"web_dns_mx";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Create DNS-MX";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"web_speicher";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Speicher MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:18:"/^[0-9\\\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";s:1:"1";}i:9;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"web_traffic";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Traffic MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:17:"/^[0-9\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";N;}i:10;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{i:1;s:7:"sperren";i:2;s:15:"benachrichtigen";i:3;s:12:"keine Aktion";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:27:"web_traffic_ueberschreitung";s:4:"type";s:11:"optionField";s:5:"title";s:22:"Traffic-Überschreitung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"web_userlimit";s:4:"type";s:9:"shortText";s:5:"title";s:9:"Max. User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:18:"/^[0-9\\\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:15:"web_domainlimit";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Max. Domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:18:"/^[0-9\\\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";N;}i:13;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:9:"web_shell";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Shell Zugriff";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:15;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_cgi";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"CGI Scripte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:16;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:16:"web_standard_cgi";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Standard CGIs";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:17;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_php";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"PHP Scripte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:18;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:17:"web_php_safe_mode";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"PHP Safe Mode";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:20;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:8:"web_ruby";s:4:"type";s:13:"checkboxField";s:5:"title";s:4:"Ruby";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:21;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_ssi";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"SSI";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:22;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_ftp";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"FTP Zugang";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:23;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:13:"web_frontpage";s:4:"type";s:13:"checkboxField";s:5:"title";s:9:"Frontpage";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:24;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:9:"web_mysql";s:4:"type";s:13:"checkboxField";s:5:"title";s:5:"MySQL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:25;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:30:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";i:6;s:1:"6";i:7;s:1:"7";i:8;s:1:"8";i:9;s:1:"9";i:10;s:2:"10";i:11;s:2:"11";i:12;s:2:"12";i:13;s:2:"13";i:14;s:2:"14";i:15;s:2:"15";i:16;s:2:"16";i:17;s:2:"17";i:18;s:2:"18";i:19;s:2:"19";i:20;s:2:"20";i:21;s:2:"21";i:22;s:2:"22";i:23;s:2:"23";i:24;s:2:"24";i:25;s:2:"25";i:26;s:2:"26";i:27;s:2:"27";i:28;s:2:"28";i:29;s:2:"29";i:30;s:2:"30";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:20:"web_mysql_anzahl_dbs";s:4:"type";s:11:"optionField";s:5:"title";s:18:"Anzahl Datenbanken";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:26;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_ssl";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"SSL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:27;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:11:"web_anonftp";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Anonymous FTP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:28;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"web_anonftplimit";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Anon. FTP MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:18:"/^[0-9\\\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";N;}i:29;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:14:"web_postgresql";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"postgreSQL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:30;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_wap";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"WAP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:31;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:26:"web_individual_error_pages";s:4:"type";s:13:"checkboxField";s:5:"title";s:26:"Individuelle Fehler-Seiten";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:32;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:33;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:18:"web_mailuser_login";s:4:"type";s:13:"checkboxField";s:5:"title";s:14:"Mailuser Login";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:34;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:5:"trweb";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:35;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:17:"web_httpd_include";s:4:"type";s:8:"longText";s:5:"title";s:13:"httpd_inc_txt";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:36;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{i:1;s:5:"aktiv";i:2;s:8:"gesperrt";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:18:"web_traffic_status";s:4:"type";s:11:"optionField";s:5:"title";s:14:"Website-Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:5:"Basis";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"a";}i:1;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1014";s:4:"view";s:4:"list";s:6:"fields";a:6:{s:13:"user_username";s:4:"User";s:9:"user_name";s:5:"Uname";s:10:"user_email";s:5:"Email";s:10:"user_admin";s:5:"Admin";s:18:"user_catchallemail";s:8:"CatchAll";s:0:"";N;}s:9:"css_class";s:2:"t2";s:4:"name";s:4:"user";s:4:"type";s:11:"attachField";s:5:"title";s:4:"User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:12:"User & Email";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1015";s:4:"view";s:4:"list";s:6:"fields";a:3:{s:9:"domain_ip";s:2:"IP";s:11:"domain_host";s:4:"Host";s:13:"domain_domain";s:6:"Domain";}s:9:"css_class";s:2:"t2";s:4:"name";s:10:"domain_abh";s:4:"type";s:11:"attachField";s:5:"title";s:7:"Domains";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:10:"Co-Domains";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:3;O:4:"deck":5:{s:8:"elements";a:10:{i:1;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:238:{s:2:"AF";s:11:"Afghanistan";s:2:"AL";s:7:"Albania";s:2:"DZ";s:7:"Algeria";s:2:"AS";s:14:"American Samoa";s:2:"AD";s:7:"Andorra";s:2:"AO";s:6:"Angola";s:2:"AI";s:8:"Anguilla";s:2:"AQ";s:10:"Antarctica";s:2:"AG";s:19:"Antigua and Barbuda";s:2:"AR";s:9:"Argentina";s:2:"AM";s:7:"Armenia";s:2:"AW";s:5:"Aruba";s:2:"AU";s:9:"Australia";s:2:"AT";s:7:"Austria";s:2:"AZ";s:10:"Azerbaijan";s:2:"BS";s:7:"Bahamas";s:2:"BH";s:7:"Bahrain";s:2:"BD";s:10:"Bangladesh";s:2:"BB";s:8:"Barbados";s:2:"BY";s:7:"Belarus";s:2:"BE";s:7:"Belgium";s:2:"BZ";s:6:"Belize";s:2:"BJ";s:5:"Benin";s:2:"BM";s:7:"Bermuda";s:2:"BT";s:6:"Bhutan";s:2:"BO";s:7:"Bolivia";s:2:"BA";s:22:"Bosnia and Herzegovina";s:2:"BW";s:8:"Botswana";s:2:"BV";s:13:"Bouvet Island";s:2:"BR";s:6:"Brazil";s:2:"IO";s:28:"British Ind. Ocean Territory";s:2:"BN";s:17:"Brunei Darussalam";s:2:"BG";s:8:"Bulgaria";s:2:"BF";s:12:"Burkina Faso";s:2:"BI";s:7:"Burundi";s:2:"KH";s:8:"Cambodia";s:2:"CM";s:8:"Cameroon";s:2:"CA";s:6:"Canada";s:2:"CV";s:10:"Cape Verde";s:2:"KY";s:14:"Cayman Islands";s:2:"CF";s:24:"Central African Republic";s:2:"TD";s:4:"Chad";s:2:"CL";s:5:"Chile";s:2:"CN";s:5:"China";s:2:"CX";s:16:"Christmas Island";s:2:"CC";s:23:"Cocos (Keeling) Islands";s:2:"CO";s:8:"Colombia";s:2:"KM";s:7:"Comoros";s:2:"CG";s:5:"Congo";s:2:"CK";s:12:"Cook Islands";s:2:"CR";s:10:"Costa Rica";s:2:"CI";s:13:"Cote D''ivoire";s:2:"HR";s:7:"Croatia";s:2:"CU";s:4:"Cuba";s:2:"CY";s:6:"Cyprus";s:2:"CZ";s:14:"Czech Republic";s:2:"DK";s:7:"Denmark";s:2:"DJ";s:8:"Djibouti";s:2:"DM";s:8:"Dominica";s:2:"DO";s:18:"Dominican Republic";s:2:"TP";s:10:"East Timor";s:2:"EC";s:7:"Ecuador";s:2:"EG";s:5:"Egypt";s:2:"SV";s:11:"El Salvador";s:2:"GQ";s:17:"Equatorial Guinea";s:2:"ER";s:7:"Eritrea";s:2:"EE";s:7:"Estonia";s:2:"ET";s:8:"Ethiopia";s:2:"FK";s:27:"Falkland Islands (Malvinas)";s:2:"FO";s:13:"Faroe Islands";s:2:"FJ";s:4:"Fiji";s:2:"FI";s:7:"Finland";s:2:"FR";s:6:"France";s:2:"GF";s:13:"French Guiana";s:2:"PF";s:16:"French Polynesia";s:2:"TF";s:27:"French Southern Territories";s:2:"GA";s:5:"Gabon";s:2:"GM";s:6:"Gambia";s:2:"GE";s:7:"Georgia";s:2:"DE";s:7:"Germany";s:2:"GH";s:5:"Ghana";s:2:"GI";s:9:"Gibraltar";s:2:"GR";s:6:"Greece";s:2:"GL";s:9:"Greenland";s:2:"GD";s:7:"Grenada";s:2:"GP";s:10:"Guadeloupe";s:2:"GU";s:4:"Guam";s:2:"GT";s:9:"Guatemala";s:2:"GN";s:6:"Guinea";s:2:"GW";s:13:"Guinea-Bissau";s:2:"GY";s:6:"Guyana";s:2:"HT";s:5:"Haiti";s:2:"HM";s:27:"Heard and Mc Donald Islands";s:2:"HN";s:8:"Honduras";s:2:"HK";s:9:"Hong Kong";s:2:"HU";s:7:"Hungary";s:2:"IS";s:7:"Iceland";s:2:"IN";s:5:"India";s:2:"ID";s:9:"Indonesia";s:2:"IR";s:4:"Iran";s:2:"IQ";s:4:"Iraq";s:2:"IE";s:7:"Ireland";s:2:"IL";s:6:"Israel";s:2:"IT";s:5:"Italy";s:2:"JM";s:7:"Jamaica";s:2:"JP";s:5:"Japan";s:2:"JO";s:6:"Jordan";s:2:"KZ";s:10:"Kazakhstan";s:2:"KE";s:5:"Kenya";s:2:"KI";s:8:"Kiribati";s:2:"KP";s:5:"Korea";s:2:"KR";s:5:"Korea";s:2:"KW";s:6:"Kuwait";s:2:"KG";s:10:"Kyrgyzstan";s:2:"LA";s:28:"Lao People''s Democratic Rep.";s:2:"LV";s:6:"Latvia";s:2:"LB";s:7:"Lebanon";s:2:"LS";s:7:"Lesotho";s:2:"LR";s:7:"Liberia";s:2:"LY";s:22:"Libyan Arab Jamahiriya";s:2:"LI";s:13:"Liechtenstein";s:2:"LT";s:9:"Lithuania";s:2:"LU";s:10:"Luxembourg";s:2:"MO";s:5:"Macau";s:2:"MK";s:9:"Macedonia";s:2:"MG";s:10:"Madagascar";s:2:"MW";s:6:"Malawi";s:2:"MY";s:8:"Malaysia";s:2:"MV";s:8:"Maldives";s:2:"ML";s:4:"Mali";s:2:"MT";s:5:"Malta";s:2:"MH";s:16:"Marshall Islands";s:2:"MQ";s:10:"Martinique";s:2:"MR";s:10:"Mauritania";s:2:"MU";s:9:"Mauritius";s:2:"YT";s:7:"Mayotte";s:2:"MX";s:6:"Mexico";s:2:"FM";s:10:"Micronesia";s:2:"MD";s:7:"Moldova";s:2:"MC";s:6:"Monaco";s:2:"MN";s:8:"Mongolia";s:2:"MS";s:10:"Montserrat";s:2:"MA";s:7:"Morocco";s:2:"MZ";s:10:"Mozambique";s:2:"MM";s:7:"Myanmar";s:2:"NA";s:7:"Namibia";s:2:"NR";s:5:"Nauru";s:2:"NP";s:5:"Nepal";s:2:"NL";s:11:"Netherlands";s:2:"AN";s:20:"Netherlands Antilles";s:2:"NC";s:13:"New Caledonia";s:2:"NZ";s:11:"New Zealand";s:2:"NI";s:9:"Nicaragua";s:2:"NE";s:5:"Niger";s:2:"NG";s:7:"Nigeria";s:2:"NU";s:4:"Niue";s:2:"NF";s:14:"Norfolk Island";s:2:"MP";s:24:"Northern Mariana Islands";s:2:"NO";s:6:"Norway";s:2:"OM";s:4:"Oman";s:2:"PK";s:8:"Pakistan";s:2:"PW";s:5:"Palau";s:2:"PS";s:21:"Palestinian Territory";s:2:"PA";s:6:"Panama";s:2:"PG";s:16:"Papua New Guinea";s:2:"PY";s:8:"Paraguay";s:2:"PE";s:4:"Peru";s:2:"PH";s:11:"Philippines";s:2:"PN";s:8:"Pitcairn";s:2:"PL";s:6:"Poland";s:2:"PT";s:8:"Portugal";s:2:"PR";s:11:"Puerto Rico";s:2:"QA";s:5:"Qatar";s:2:"RE";s:7:"Reunion";s:2:"RO";s:7:"Romania";s:2:"RU";s:18:"Russian Federation";s:2:"RW";s:6:"Rwanda";s:2:"SH";s:12:"Saint Helena";s:2:"KN";s:21:"Saint Kitts And Nevis";s:2:"LC";s:11:"Saint Lucia";s:2:"PM";s:25:"Saint Pierre and Miquelon";s:2:"VC";s:26:"St. Vincent and Grenadines";s:2:"WS";s:5:"Samoa";s:2:"SM";s:10:"San Marino";s:2:"ST";s:21:"Sao Tome and Principe";s:2:"SA";s:12:"Saudi Arabia";s:2:"SN";s:7:"Senegal";s:2:"SC";s:10:"Seychelles";s:2:"SL";s:12:"Sierra Leone";s:2:"SG";s:9:"Singapore";s:2:"SK";s:8:"Slovakia";s:2:"SI";s:8:"Slovenia";s:2:"SB";s:15:"Solomon Islands";s:2:"SO";s:7:"Somalia";s:2:"ZA";s:12:"South Africa";s:2:"GS";s:24:"S.Georgia & S.Sandw.Isl.";s:2:"ES";s:5:"Spain";s:2:"LK";s:9:"Sri Lanka";s:2:"SD";s:5:"Sudan";s:2:"SR";s:8:"Suriname";s:2:"SJ";s:26:"Svalbard a. Jan Mayen Isl.";s:2:"SZ";s:9:"Swaziland";s:2:"SE";s:6:"Sweden";s:2:"CH";s:11:"Switzerland";s:2:"SY";s:20:"Syrian Arab Republic";s:2:"TW";s:6:"Taiwan";s:2:"TJ";s:10:"Tajikistan";s:2:"TZ";s:8:"Tanzania";s:2:"TH";s:8:"Thailand";s:2:"TG";s:4:"Togo";s:2:"TK";s:7:"Tokelau";s:2:"TO";s:5:"Tonga";s:2:"TT";s:19:"Trinidad and Tobago";s:2:"TN";s:7:"Tunisia";s:2:"TR";s:6:"Turkey";s:2:"TM";s:12:"Turkmenistan";s:2:"TC";s:24:"Turks and Caicos Islands";s:2:"TV";s:6:"Tuvalu";s:2:"UG";s:6:"Uganda";s:2:"UA";s:7:"Ukraine";s:2:"AE";s:20:"United Arab Emirates";s:2:"GB";s:14:"United Kingdom";s:2:"US";s:13:"United States";s:2:"UM";s:29:"United States Min. Outl. Isl.";s:2:"UY";s:7:"Uruguay";s:2:"UZ";s:10:"Uzbekistan";s:2:"VU";s:7:"Vanuatu";s:2:"VA";s:29:"Vatican City State (Holy See)";s:2:"VE";s:9:"Venezuela";s:2:"VN";s:7:"Vietnam";s:2:"VG";s:24:"Virgin Islands (British)";s:2:"VI";s:21:"Virgin Islands (U.S.)";s:2:"WF";s:25:"Wallis and Futuna Islands";s:2:"EH";s:14:"Western Sahara";s:2:"YE";s:5:"Yemen";s:2:"YU";s:10:"Yugoslavia";s:2:"ZM";s:6:"Zambia";s:2:"ZW";s:8:"Zimbabwe";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:11:"ssl_country";s:4:"type";s:11:"optionField";s:5:"title";s:4:"Land";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"ssl_state";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Bundesland";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"ssl_locality";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Ort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"ssl_organization";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Firma";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:21:"ssl_organization_unit";s:4:"type";s:9:"shortText";s:5:"title";s:9:"Abteilung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:3:"365";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:8:"ssl_days";s:4:"type";s:9:"shortText";s:5:"title";s:17:"Gültigkeit (Tage)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"5";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t4";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:11:"ssl_request";s:4:"type";s:8:"longText";s:5:"title";s:11:"SSL Request";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:8:"ssl_cert";s:4:"type";s:8:"longText";s:5:"title";s:14:"SSL Zertifikat";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:6:"create";s:19:"Zertifikat erzeugen";s:4:"save";s:20:"Zertifikat speichern";s:6:"delete";s:18:"Zertifikat löschen";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:10:"ssl_action";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Aktion";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:3:"SSL";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:4;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:8:"web_shop";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"Shopsystem";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:14:"web_phpmyadmin";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"phpmyAdmin";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:11:"web_webmail";s:4:"type";s:13:"checkboxField";s:5:"title";s:7:"WebMail";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:13:"web_webalizer";s:4:"type";s:13:"checkboxField";s:5:"title";s:9:"Webalizer";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:6:"Pakete";s:7:"visible";s:1:"0";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:5;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:14:"check_webspace";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:7:"traffic";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:9:"Statistik";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:6;O:4:"deck":5:{s:8:"elements";a:19:{i:0;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1029";s:4:"view";s:4:"list";s:6:"fields";a:2:{s:13:"datenbankname";s:13:"Datenbankname";s:13:"datenbankuser";s:13:"Datenbankuser";}s:9:"css_class";s:2:"t2";s:4:"name";s:17:"optionen_db_liste";s:4:"type";s:11:"attachField";s:5:"title";s:11:"Datenbanken";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:19:"optionen_mysql_user";s:4:"type";s:9:"shortText";s:5:"title";s:10:"MySQL User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:14:"check_database";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:23:"optionen_mysql_passwort";s:4:"type";s:9:"shortText";s:5:"title";s:14:"MySQL Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:28:"optionen_mysql_remote_access";s:4:"type";s:13:"checkboxField";s:5:"title";s:19:"MySQL Remote Access";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:27:"optionen_frontpage_passwort";s:4:"type";s:9:"shortText";s:5:"title";s:18:"Frontpage Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:19:"externer mailserver";i:1;s:18:"lokaler mailserver";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:25:"optionen_local_mailserver";s:4:"type";s:11:"optionField";s:5:"title";s:10:"Mailserver";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:3:"30%";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"optionen_logsize";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Log-Grösse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:8:"longText":18:{s:5:"value";s:139:"index.html\r\nindex.htm\r\nindex.php\r\nindex.php5\r\nindex.php4\r\nindex.php3\r\nindex.shtml\r\nindex.cgi\r\nindex.pl\r\nindex.jsp\r\nDefault.htm\r\ndefault.htm";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:24:"optionen_directory_index";s:4:"type";s:8:"longText";s:5:"title";s:14:"DirectoryIndex";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t5";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_400";s:4:"type";s:8:"longText";s:5:"title";s:32:"Fehler 400<br>(Ungültige Syntax)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_401";s:4:"type";s:8:"longText";s:5:"title";s:45:"Fehler 401<br>(Autorisierung<br>erforderlich)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:13;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_403";s:4:"type";s:8:"longText";s:5:"title";s:24:"Fehler 403<br>(Verboten)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_404";s:4:"type";s:8:"longText";s:5:"title";s:39:"Fehler 404<br>(Datei nicht<br>gefunden)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:15;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_405";s:4:"type";s:8:"longText";s:5:"title";s:40:"Fehler 405<br>(Methode nicht<br>erlaubt)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:16;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_500";s:4:"type";s:8:"longText";s:5:"title";s:41:"Fehler 500<br>(Interner<br>Server-Fehler)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:17;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"error_503";s:4:"type";s:8:"longText";s:5:"title";s:47:"Fehler 503<br>(Service ist z.Zt.<br>überlastet)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:18;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t6";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:19;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:15:"webalizer_stats";s:4:"type";s:13:"checkboxField";s:5:"title";s:15:"Webalizer Stats";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:8:"Optionen";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:7;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:15:"isp_web_faktura";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:7:"Invoice";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"a";s:10:"perm_write";s:1:"a";}}s:5:"title";s:7:"ISP Web";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:7:"isp_web";s:12:"event_insert";s:10:"web_insert";s:12:"event_update";s:10:"web_update";s:12:"event_delete";s:10:"web_delete";s:10:"event_show";s:8:"web_show";s:11:"wysiwyg_lib";i:0;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1014, 1, 0, 'isp', 'isp_user', 'ISP User', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:8:"isp_user";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:12:"isp_isp_user";s:10:"form_width";s:3:"450";s:4:"deck";a:3:{i:0;O:4:"deck":5:{s:8:"elements";a:13:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"user_name";s:4:"type";s:9:"shortText";s:5:"title";s:9:"Real Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:27:"/^[\\\\w\\\\.\\\\-\\\\_\\\\s]{0,50}$/";s:10:"reg_fehler";s:14:"error_realname";s:6:"search";N;}i:1;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:11:"pluginField":13:{s:9:"css_class";s:6:"normal";s:7:"options";N;s:4:"name";s:12:"isp_username";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"user_email";s:4:"type";s:9:"shortText";s:5:"title";s:13:"Email Adresse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"32";s:8:"password";N;s:10:"write_once";s:1:"1";s:4:"name";s:13:"user_username";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Username";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"20";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:13:"user_passwort";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"20";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"user_speicher";s:4:"type";s:9:"shortText";s:5:"title";s:14:"WebSpeicher MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:18:"/^[0-9\\\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:14:"user_mailquota";s:4:"type";s:9:"shortText";s:5:"title";s:15:"MailSpeicher MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:18:"/^[0-9\\\\-]{0,10}$/";s:10:"reg_fehler";s:27:"Es sind nur Zahlen erlaubt.";s:6:"search";N;}i:9;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr3";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:10:"user_admin";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Administrator";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:10:"user_shell";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Shell Zugriff";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:12:"User & Email";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:1;O:4:"deck":5:{s:8:"elements";a:10:{i:1;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:23:"user_emailweiterleitung";s:4:"type";s:8:"longText";s:5:"title";s:13:"Weiterleitung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"descField":14:{s:5:"value";s:23:"email_weiterleitung_txt";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:22:"txt_emailweiterleitung";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:34:"user_emailweiterleitung_local_copy";s:4:"type";s:13:"checkboxField";s:5:"title";s:15:"Kopie speichern";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:15:"user_emailalias";s:4:"type";s:8:"longText";s:5:"title";s:11:"Email Alias";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:9:"descField":14:{s:5:"value";s:15:"email_alias_txt";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:15:"txt_email_alias";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:18:"user_catchallemail";s:4:"type";s:13:"checkboxField";s:5:"title";s:14:"catchAll-Email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:13:"user_mailscan";s:4:"type";s:13:"checkboxField";s:5:"title";s:8:"MailScan";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:18:"user_autoresponder";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Autoresponder";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:23:"user_autoresponder_text";s:4:"type";s:8:"longText";s:5:"title";s:9:"Nachricht";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:24:"Erweiterte Einstellungen";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:13:{i:0;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:15:"user_spamfilter";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"Spamfilter";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:9:"antivirus";s:4:"type";s:13:"checkboxField";s:5:"title";s:9:"Antivirus";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:6:"accept";s:6:"accept";s:7:"discard";s:7:"discard";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:13:"spam_strategy";s:4:"type";s:11:"optionField";s:5:"title";s:14:"Spam Strategie";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:1:"5";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"spam_hits";s:4:"type";s:9:"shortText";s:5:"title";s:9:"Spam Hits";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr4";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:20:"spam_rewrite_subject";s:4:"type";s:13:"checkboxField";s:5:"title";s:15:"Rewrite Subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:10:"***SPAM***";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"spam_subject_tag";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Betreff";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr5";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:9:"use_uribl";s:4:"type";s:13:"checkboxField";s:5:"title";s:9:"Use Uribl";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:14:"spam_whitelist";s:4:"type";s:8:"longText";s:5:"title";s:14:"Spam Whitelist";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:9:"descField":14:{s:5:"value";s:18:"spam_whitelist_txt";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:18:"txt_spam_whitelist";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:2:"10";s:4:"name";s:14:"spam_blacklist";s:4:"type";s:8:"longText";s:5:"title";s:14:"Spam Blacklist";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:9:"descField":14:{s:5:"value";s:18:"spam_blacklist_txt";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:18:"txt_spam_blacklist";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:22:"Spamfilter & Antivirus";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:8:"ISP User";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:8:"isp_user";s:12:"event_insert";s:11:"user_insert";s:12:"event_update";s:11:"user_update";s:12:"event_delete";s:11:"user_delete";s:10:"event_show";s:9:"user_show";s:11:"wysiwyg_lib";N;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1015, 1, 1, 'isp', 'isp_domain', 'ISP Domain', 'O:3:"doc":28:{s:6:"userid";s:1:"4";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:10:"isp_domain";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:14:"isp_isp_domain";s:10:"form_width";s:3:"450";s:4:"deck";a:3:{i:0;O:4:"deck":5:{s:8:"elements";a:9:{i:0;O:9:"shorttext":16:{s:4:"name";s:11:"domain_host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:28:"/^[\\\\w0-9\\\\-\\\\.\\\\*]{0,255}$/";s:10:"reg_fehler";s:56:"Es sind nur folgende Zeichen zugelassen: a-z A-Z 0-9 - .";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:3;O:9:"shorttext":16:{s:4:"name";s:13:"domain_domain";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:23:"/^[\\\\w0-9\\\\-\\\\.]{0,63}/";s:10:"reg_fehler";s:54:"Es sind nur folgende Zeichen zugelassen: a-z A-Z 0-9 -";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:5;O:9:"shorttext":16:{s:4:"name";s:9:"domain_ip";s:4:"type";s:9:"shortText";s:5:"title";s:2:"IP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:18:"/^[0-9\\\\.]{0,15}$/";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:6;O:14:"seperatorfield":13:{s:4:"name";s:2:"t1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"width";s:1:"1";s:9:"css_class";s:0:"";}i:7;O:13:"checkboxfield":13:{s:4:"name";s:10:"domain_dns";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"Create DNS";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";N;s:9:"css_class";s:0:"";}i:8;O:13:"checkboxfield":13:{s:4:"name";s:14:"domain_dnsmail";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Create DNS MX";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";N;s:9:"css_class";s:0:"";}i:9;O:14:"seperatorfield":13:{s:4:"name";s:2:"t2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"width";s:1:"1";s:9:"css_class";s:0:"";}i:10;O:11:"optionfield":21:{s:4:"name";s:15:"domain_register";s:4:"type";s:11:"optionField";s:5:"title";s:20:"Domain Registrierung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:11:"option_type";s:8:"dropdown";s:6:"values";a:4:{s:3:"reg";s:8:"Register";s:3:"upd";s:6:"Update";s:2:"kk";s:9:"KK Antrag";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;}i:11;O:9:"shorttext":16:{s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:6:"Domain";s:7:"visible";i:1;s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:1;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:9:"shorttext":16:{s:4:"name";s:20:"domain_weiterleitung";s:4:"type";s:9:"shortText";s:5:"title";s:13:"Weiterleitung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:1;O:14:"seperatorfield":13:{s:4:"name";s:2:"t3";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"width";s:1:"1";s:9:"css_class";s:0:"";}}s:5:"title";s:13:"Weiterleitung";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"optionfield":21:{s:4:"name";s:23:"domain_local_mailserver";s:4:"type";s:11:"optionField";s:5:"title";s:10:"Mailserver";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:19:"externer mailserver";i:1;s:18:"lokaler mailserver";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;}}s:5:"title";s:8:"Optionen";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:10:"ISP Domain";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:10:"isp_domain";s:12:"event_insert";s:13:"domain_insert";s:12:"event_update";s:13:"domain_update";s:12:"event_delete";s:13:"domain_delete";s:10:"event_show";N;s:11:"wysiwyg_lib";i:0;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1016, 1, 0, 'dns', 'isp_dns', 'DNS Eintrag', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"1";s:5:"modul";s:3:"dns";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:7:"isp_dns";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:11:"dns_isp_dns";s:10:"form_width";s:3:"450";s:4:"deck";a:3:{i:0;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";N;s:6:"source";s:2:"db";s:12:"source_table";s:10:"isp_server";s:11:"value_field";s:11:"server_name";s:8:"id_field";s:6:"doc_id";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:9:"server_id";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"dns_soa";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Domain (SOA)";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"dns_soa_ip";s:4:"type";s:9:"shortText";s:5:"title";s:10:"IP Adresse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"15";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:53:"/^[0-9]{1,3}\\\\.[0-9]{1,3}\\\\.[0-9]{1,3}\\\\.[0-9]{1,3}$/";s:10:"reg_fehler";s:20:"IP-Adresse ungültig.";s:6:"search";s:1:"1";}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:6:"Domain";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:1;O:4:"deck":5:{s:8:"elements";a:9:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"dns_adminmail";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Email Admin";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"dns_ns1";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Nameserver 1";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"dns_ns2";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Nameserver 2";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:5:"28800";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"dns_refresh";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Refresh";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:4:"7200";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"dns_retry";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Retry";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:6:"604800";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"dns_expire";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Expire";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:9:"shortText":16:{s:5:"value";s:5:"86400";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"dns_ttl";s:4:"type";s:9:"shortText";s:5:"title";s:3:"TTL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:8:"Optionen";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:2;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1018";s:4:"view";s:4:"list";s:6:"fields";a:2:{s:10:"ip_adresse";s:10:"IP-Adresse";s:4:"host";s:4:"Host";}s:9:"css_class";s:3:"t2b";s:4:"name";s:8:"a_record";s:4:"type";s:11:"attachField";s:5:"title";s:8:"A Record";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1019";s:4:"view";s:4:"list";s:6:"fields";a:2:{s:4:"host";s:4:"Host";s:4:"ziel";s:4:"Ziel";}s:9:"css_class";s:3:"t2b";s:4:"name";s:5:"cname";s:4:"type";s:11:"attachField";s:5:"title";s:5:"CNAME";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1020";s:4:"view";s:4:"list";s:6:"fields";a:2:{s:10:"mailserver";s:10:"Mailserver";s:4:"host";s:4:"Host";}s:9:"css_class";s:3:"t2b";s:4:"name";s:2:"mx";s:4:"type";s:11:"attachField";s:5:"title";s:2:"MX";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:12:"dns_spf_list";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:7:"Records";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:11:"DNS Eintrag";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:7:"isp_dns";s:12:"event_insert";s:10:"soa_insert";s:12:"event_update";s:10:"soa_update";s:12:"event_delete";s:10:"soa_delete";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";i:0;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1019, 1, 0, 'dns', 'cname', 'CNAME Record', 'O:3:"doc":26:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"dns";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:5:"cname";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:9:"dns_cname";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:9:"shorttext":16:{s:4:"name";s:4:"host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:1;O:9:"shorttext":16:{s:4:"name";s:4:"ziel";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Ziel";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:6:"Record";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:12:"CNAME Record";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.1;s:11:"event_class";s:7:"isp_dns";s:12:"event_insert";s:10:"soa_insert";s:12:"event_update";s:10:"soa_update";s:12:"event_delete";s:10:"soa_delete";}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1018, 1, 0, 'dns', 'a', 'A Record', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"dns";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:1:"a";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:5:"dns_a";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"ip_adresse";s:4:"type";s:9:"shortText";s:5:"title";s:10:"IP Adresse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"15";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:181:"/^([0-9]|[0-9]\\\\d|1\\\\d{2}|2[0-4]\\\\d|25[0-5])\\\\.([0-9]|[0-9]\\\\d|1\\\\d{2}|2[0-4]\\\\d|25[0-5])\\\\.([0-9]|[0-9]\\\\d|1\\\\d{2}|2[0-4]\\\\d|25[0-5])\\\\.([0-9]|[0-9]\\\\d|1\\\\d{2}|2[0-4]\\\\d|25[0-5])$/";s:10:"reg_fehler";s:20:"IP-Adresse ungültig.";s:6:"search";N;}}s:5:"title";s:6:"Record";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:8:"A Record";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:7:"isp_dns";s:12:"event_insert";s:10:"soa_insert";s:12:"event_update";s:10:"soa_update";s:12:"event_delete";s:10:"soa_delete";s:10:"event_show";N;s:11:"wysiwyg_lib";i:0;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1020, 1, 0, 'dns', 'mx', 'MX Record', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"dns";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:2:"mx";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:6:"dns_mx";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:3:{i:0;O:9:"shorttext":16:{s:4:"name";s:4:"host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:1;O:11:"optionfield":21:{s:4:"name";s:10:"prioritaet";s:4:"type";s:11:"optionField";s:5:"title";s:9:"Priorität";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:11:"option_type";s:8:"dropdown";s:6:"values";a:9:{i:10;s:2:"10";i:20;s:2:"20";i:30;s:2:"30";i:40;s:2:"40";i:50;s:2:"50";i:60;s:2:"60";i:70;s:2:"70";i:80;s:2:"80";i:90;s:2:"90";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;}i:2;O:9:"shorttext":16:{s:4:"name";s:10:"mailserver";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Mailserver";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:27:"/^[a-zA-Z0-9\\\\-\\\\.]{3,63}$/";s:10:"reg_fehler";s:51:"Es sind nur folgende Zeichen erlaubt: a-z A-Z 0-9 -";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:6:"Record";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:9:"MX Record";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.1;s:11:"event_class";s:7:"isp_dns";s:12:"event_insert";s:10:"soa_insert";s:12:"event_update";s:10:"soa_update";s:12:"event_delete";s:10:"soa_delete";s:10:"event_show";N;s:11:"wysiwyg_lib";i:0;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1021, 1, 1, 'sys', 'serverstatus', 'ISP Server Status', 'O:3:"doc":26:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"0";s:4:"name";s:12:"serverstatus";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:16:"isp_serverstatus";s:10:"form_width";s:3:"450";s:4:"deck";a:4:{i:0;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:11:"pluginfield":12:{s:4:"name";s:12:"check_uptime";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:9:"css_class";N;}i:1;O:11:"pluginfield":12:{s:4:"name";s:10:"check_disk";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:9:"css_class";s:0:"";}}s:5:"title";s:6:"Status";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:1;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"pluginfield":12:{s:4:"name";s:13:"check_meminfo";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:9:"css_class";N;}}s:5:"title";s:15:"Arbeitsspeicher";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:2;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"pluginfield":12:{s:4:"name";s:13:"check_cpuinfo";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:9:"css_class";N;}}s:5:"title";s:3:"CPU";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:3;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"pluginfield":12:{s:4:"name";s:14:"check_services";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:9:"css_class";s:0:"";}}s:5:"title";s:7:"Dienste";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:17:"ISP Server Status";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:10:"status.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.1;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1022, 1, 1, 'isp', 'isp_reseller', 'ISP Anbieter', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"1";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:12:"isp_reseller";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:16:"isp_isp_reseller";s:10:"form_width";s:3:"450";s:4:"deck";a:5:{i:0;O:4:"deck":5:{s:8:"elements";a:15:{i:0;O:11:"pluginField":13:{s:9:"css_class";s:6:"normal";s:7:"options";N;s:4:"name";s:18:"isp_anbieternummer";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:5:"firma";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Firma";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:3;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:4:"Frau";s:4:"Frau";s:4:"Herr";s:4:"Herr";s:5:"Firma";s:5:"Firma";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:6:"anrede";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Anrede";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"vorname";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Vorname";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"name";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:6;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"strasse";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Strasse";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:3:"plz";s:4:"type";s:9:"shortText";s:5:"title";s:3:"PLZ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:8;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:3:"ort";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Ort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:9;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:8:"province";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Bundesland";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:10;O:9:"shortText":16:{s:5:"value";s:11:"Deutschland";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"land";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Land";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";}i:11;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:7:"telefon";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Telefon";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:12;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:3:"fax";s:4:"type";s:9:"shortText";s:5:"title";s:3:"Fax";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:13;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:5:"email";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:14;O:9:"linkField":14:{s:5:"value";s:7:"http://";s:6:"target";s:6:"_blank";s:9:"css_class";s:0:"";s:4:"name";s:8:"internet";s:4:"type";s:9:"linkField";s:5:"title";s:8:"Internet";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"20";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";s:1:"1";}i:15;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"100";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:14:"reseller_group";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Res. Gruppe";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"5";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:10:"Stammdaten";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:1;O:4:"deck":5:{s:8:"elements";a:25:{i:0;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:9:"limit_web";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Webs";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"limit_disk";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Diskspace MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"limit_traffic";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Traffic MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{i:1;s:7:"sperren";i:2;s:15:"benachrichtigen";i:3;s:12:"keine Aktion";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:29:"limit_traffic_ueberschreitung";s:4:"type";s:11:"optionField";s:5:"title";s:22:"Traffic-Überschreitung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"limit_user";s:4:"type";s:9:"shortText";s:5:"title";s:4:"User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"limit_domain";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Domains";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"limit_domain_dns";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Domain DNS";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:15:"limit_slave_dns";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Slave Zones";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:5:"tr_l1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:17:"limit_dns_manager";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"DNS-Manager";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:19:"limit_httpd_include";s:4:"type";s:13:"checkboxField";s:5:"title";s:14:"HTTPD Includes";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:18:"limit_shell_access";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Shell Zugriff";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:9:"limit_cgi";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"CGI Scripte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:13;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:19:"limit_standard_cgis";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Standard CGIs";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:9:"limit_php";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"PHP Scripte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:15;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:10:"limit_ruby";s:4:"type";s:13:"checkboxField";s:5:"title";s:4:"Ruby";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:16;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:9:"limit_ssi";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"SSI";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:17;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:9:"limit_ftp";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"FTP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:18;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:13:"limit_anonftp";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Anonymous FTP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:19;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:15:"limit_frontpage";s:4:"type";s:13:"checkboxField";s:5:"title";s:9:"Frontpage";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:20;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:11:"limit_mysql";s:4:"type";s:13:"checkboxField";s:5:"title";s:5:"MySQL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:21;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:22:"limit_mysql_anzahl_dbs";s:4:"type";s:9:"shortText";s:5:"title";s:18:"Anzahl Datenbanken";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:22;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:9:"limit_ssl";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"SSL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:23;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:9:"limit_wap";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"WAP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:24;O:13:"checkboxField":13:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:17:"limit_error_pages";s:4:"type";s:13:"checkboxField";s:5:"title";s:26:"Individuelle Fehler-Seiten";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:6:"Limits";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:2;O:4:"deck":5:{s:8:"elements";a:4:{i:0;O:9:"descField":14:{s:5:"value";s:32:"zugangsdaten_anbieter_einleitung";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:17:"txt_reselleradmin";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"reseller_user";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Benutzername";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";s:1:"1";s:10:"write_once";N;s:4:"name";s:17:"reseller_passwort";s:4:"type";s:9:"shortText";s:5:"title";s:8:"Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:12:"integerField":14:{s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:0:"";s:4:"name";s:15:"reseller_userid";s:4:"type";s:12:"integerField";s:5:"title";s:6:"UserID";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"5";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:12:"Zugangsdaten";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:3;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:14:"reseller_stats";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:16:"reseller_traffic";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:9:"Statistik";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"w";}i:4;O:4:"deck":5:{s:8:"elements";a:28:{i:1;O:9:"descField":14:{s:5:"value";s:32:"txt_client_salutatory_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:28:"client_salutatory_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:36:"client_salutatory_email_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:40:"txt_client_salutatory_email_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:35:"client_salutatory_email_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:39:"txt_client_salutatory_email_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:27:"client_salutatory_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:31:"txt_client_salutatory_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:31:"client_salutatory_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:35:"txt_client_salutatory_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:6;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:31:"client_salutatory_email_message";s:4:"type";s:8:"longText";s:5:"title";s:35:"txt_client_salutatory_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:9:"descField":14:{s:5:"value";s:37:"txt_client_salutatory_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:33:"client_salutatory_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:14:"standard_index";s:4:"type";s:8:"longText";s:5:"title";s:18:"txt_standard_index";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:9:"descField":14:{s:5:"value";s:28:"txt_standard_index_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:24:"standard_index_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:19:"user_standard_index";s:4:"type";s:8:"longText";s:5:"title";s:23:"txt_user_standard_index";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:9:"descField":14:{s:5:"value";s:33:"txt_user_standard_index_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:29:"user_standard_index_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:13;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:9:"descField":14:{s:5:"value";s:33:"txt_traffic_suspension_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:29:"traffic_suspension_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:15;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:31:"traffic_suspension_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:35:"txt_traffic_suspension_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:16;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:30:"traffic_suspension_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:34:"txt_traffic_suspension_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:17;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:28:"traffic_suspension_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:32:"txt_traffic_suspension_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:18;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:32:"traffic_suspension_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:36:"txt_traffic_suspension_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:19;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:32:"traffic_suspension_email_message";s:4:"type";s:8:"longText";s:5:"title";s:36:"txt_traffic_suspension_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:20;O:9:"descField":14:{s:5:"value";s:38:"txt_traffic_suspension_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:34:"traffic_suspension_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:21;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:2:"t3";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:22;O:9:"descField":14:{s:5:"value";s:35:"txt_traffic_notification_email_desc";s:9:"css_class";s:2:"t2";s:9:"alignment";s:6:"center";s:4:"name";s:31:"traffic_notification_email_desc";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:23;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:33:"traffic_notification_sender_email";s:4:"type";s:9:"shortText";s:5:"title";s:37:"txt_traffic_notification_sender_email";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:24;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:32:"traffic_notification_sender_name";s:4:"type";s:9:"shortText";s:5:"title";s:36:"txt_traffic_notification_sender_name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:25;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:30:"traffic_notification_email_bcc";s:4:"type";s:9:"shortText";s:5:"title";s:34:"txt_traffic_notification_email_bcc";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:26;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:34:"traffic_notification_email_subject";s:4:"type";s:9:"shortText";s:5:"title";s:38:"txt_traffic_notification_email_subject";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:27;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:34:"traffic_notification_email_message";s:4:"type";s:8:"longText";s:5:"title";s:38:"txt_traffic_notification_email_message";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:28;O:9:"descField":14:{s:5:"value";s:40:"txt_traffic_notification_email_variables";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:36:"traffic_notification_email_variables";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:9:"Sonstiges";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:12:"ISP Anbieter";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:12:"anbieter.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:12:"isp_reseller";s:12:"event_insert";s:15:"reseller_insert";s:12:"event_update";s:15:"reseller_update";s:12:"event_delete";s:15:"reseller_delete";s:10:"event_show";s:13:"reseller_show";s:11:"wysiwyg_lib";N;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1023, 1, 1, 'sys', 'dienste', 'ISP Dienste', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"0";s:4:"name";s:7:"dienste";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:11:"isp_dienste";s:10:"form_width";s:3:"450";s:4:"deck";a:3:{i:0;O:4:"deck":5:{s:8:"elements";a:10:{i:2;O:11:"pluginField":13:{s:9:"css_class";s:0:"";s:7:"options";N;s:4:"name";s:18:"check_services_adm";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:2:"on";s:2:"An";s:3:"off";s:3:"Aus";s:7:"restart";s:11:"neu Starten";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:17:"dienst_www_status";s:4:"type";s:11:"optionField";s:5:"title";s:10:"Web-Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:2:"on";s:2:"An";s:3:"off";s:3:"Aus";s:7:"restart";s:11:"neu Starten";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:17:"dienst_ftp_status";s:4:"type";s:11:"optionField";s:5:"title";s:10:"FTP-Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:2:"on";s:2:"An";s:3:"off";s:3:"Aus";s:7:"restart";s:11:"neu Starten";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:18:"dienst_smtp_status";s:4:"type";s:11:"optionField";s:5:"title";s:11:"SMTP-Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:2:"on";s:2:"An";s:3:"off";s:3:"Aus";s:7:"restart";s:11:"neu Starten";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:17:"dienst_dns_status";s:4:"type";s:11:"optionField";s:5:"title";s:10:"DNS-Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:2:"on";s:2:"An";s:7:"restart";s:11:"neu Starten";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:19:"dienst_mysql_status";s:4:"type";s:11:"optionField";s:5:"title";s:12:"mySQL-Server";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:11;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:4:{s:3:"off";s:3:"Aus";s:2:"on";s:2:"An";s:7:"restart";s:11:"neu Starten";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:22:"dienst_firewall_status";s:4:"type";s:11:"optionField";s:5:"title";s:8:"Firewall";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:11:"pluginField":13:{s:9:"css_class";N;s:7:"options";N;s:4:"name";s:4:"save";s:4:"type";s:11:"pluginField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:7:"Dienste";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:1;O:4:"deck":5:{s:8:"elements";a:1:{i:2;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1024";s:4:"view";s:4:"list";s:6:"fields";a:5:{s:11:"dienst_name";s:6:"Dienst";s:11:"dienst_port";s:4:"Port";s:14:"dienst_monitor";s:5:"Aktiv";s:11:"dienst_host";s:4:"Host";s:0:"";N;}s:9:"css_class";s:3:"t2b";s:4:"name";s:12:"ueberwachung";s:4:"type";s:11:"attachField";s:5:"title";s:11:"Überwachung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:11:"Überwachung";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:2;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:11:"attachField":16:{s:5:"value";N;s:7:"doctype";s:4:"1025";s:4:"view";s:4:"list";s:6:"fields";a:4:{s:11:"dienst_name";s:4:"Name";s:11:"dienst_port";s:4:"Port";s:10:"dienst_typ";s:3:"Typ";s:12:"dienst_aktiv";s:5:"Aktiv";}s:9:"css_class";s:3:"t2b";s:4:"name";s:8:"firewall";s:4:"type";s:11:"attachField";s:5:"title";s:14:"Firewall Regel";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:8:"Firewall";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:11:"ISP Dienste";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:11:"isp_dienste";s:12:"event_insert";s:0:"";s:12:"event_update";s:13:"dienst_update";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";i:0;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1024, 1, 1, 'sys', 'monitor', 'ISP Überwachung', 'O:3:"doc":27:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:7:"monitor";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:11:"isp_monitor";s:10:"form_width";s:3:"450";s:4:"deck";a:2:{i:0;O:4:"deck":5:{s:8:"elements";a:9:{i:1;O:11:"optionfield":21:{s:4:"name";s:11:"dienst_name";s:4:"type";s:11:"optionField";s:5:"title";s:6:"Dienst";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:11:"option_type";s:8:"dropdown";s:6:"values";a:6:{s:3:"web";s:9:"Webserver";s:3:"dns";s:8:"Bind DNS";s:4:"mail";s:10:"Mailserver";s:3:"ftp";s:10:"FTP-Server";s:4:"pop3";s:11:"POP3-Server";s:3:"etc";s:8:"Sonstige";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;}i:2;O:11:"optionfield":21:{s:4:"name";s:14:"dienst_monitor";s:4:"type";s:11:"optionField";s:5:"title";s:11:"Überwachung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:11:"option_type";s:8:"dropdown";s:6:"values";a:3:{s:2:"ja";s:2:"Ja";s:4:"nein";s:4:"Nein";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;}i:3;O:9:"shorttext":16:{s:4:"name";s:11:"dienst_host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:9:"localhost";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:4;O:9:"shorttext":16:{s:4:"name";s:11:"dienst_port";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Port";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:8;O:14:"seperatorfield":13:{s:4:"name";s:3:"tr1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"width";s:1:"1";s:9:"css_class";s:0:"";}i:9;O:9:"descfield":14:{s:4:"name";s:2:"b1";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";s:59:"Der Dienst-Typ wird nur bei sonstigen-Diensten ausgewertet.";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";}i:10;O:14:"seperatorfield":13:{s:4:"name";s:3:"tr2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"width";s:1:"1";s:9:"css_class";s:0:"";}i:11;O:11:"optionfield":21:{s:4:"name";s:10:"dienst_typ";s:4:"type";s:11:"optionField";s:5:"title";s:3:"Typ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:3:"tcp";s:3:"tcp";s:3:"udp";s:3:"udp";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;}i:12;O:9:"shorttext":16:{s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:11:"Überwachung";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:1;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:9:"shorttext":16:{s:4:"name";s:18:"dienst_run_offline";s:4:"type";s:9:"shortText";s:5:"title";s:7:"Offline";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:1;O:9:"shorttext":16:{s:4:"name";s:17:"dienst_run_online";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Online";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:8:"Aktionen";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:15:"ISP Überwachung";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.1;s:11:"event_class";s:11:"isp_monitor";s:12:"event_insert";s:14:"monitor_insert";s:12:"event_update";s:14:"monitor_update";s:12:"event_delete";s:14:"monitor_delete";s:10:"event_show";s:0:"";}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1025, 1, 1, 'sys', 'firewall', 'ISP Firewall', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:8:"firewall";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:12:"isp_firewall";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:5:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"dienst_name";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Name";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:2:"ja";s:2:"Ja";s:4:"nein";s:4:"Nein";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:12:"dienst_aktiv";s:4:"type";s:11:"optionField";s:5:"title";s:5:"Aktiv";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:3:"tcp";s:3:"TCP";s:3:"udp";s:3:"UDP";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:10:"dienst_typ";s:4:"type";s:11:"optionField";s:5:"title";s:3:"Typ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"12";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"dienst_port";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Port";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"15";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:28:"/^[0-9]{1,5}(:[0-9]{1,5}|)$/";s:10:"reg_fehler";s:42:"Der Port muss zwischen 0 und 65000 liegen.";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:14:"Firewall Regel";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:12:"ISP Firewall";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:12:"isp_firewall";s:12:"event_insert";s:15:"firewall_insert";s:12:"event_update";s:15:"firewall_update";s:12:"event_delete";s:15:"firewall_delete";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";i:0;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1026, 1, 1, 'isp_fakt', 'artikel', 'Artikel', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"1";s:5:"modul";s:8:"isp_fakt";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:7:"artikel";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:16:"isp_fakt_artikel";s:10:"form_width";s:3:"450";s:4:"deck";a:2:{i:0;O:4:"deck":5:{s:8:"elements";a:12:{i:0;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";N;s:6:"source";s:2:"db";s:12:"source_table";s:6:"groups";s:11:"value_field";s:4:"name";s:8:"id_field";s:7:"groupid";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:12:"artikelgroup";s:4:"type";s:11:"optionField";s:5:"title";s:11:"Artikel für";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr2";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:9:{s:3:"Web";s:3:"Web";s:6:"Domain";s:6:"Domain";s:7:"Traffic";s:7:"Traffic";s:10:"IP-Traffic";s:10:"IP-Traffic";s:5:"Email";s:5:"Email";s:14:"Dienstleistung";s:14:"Dienstleistung";s:8:"Hardware";s:8:"Hardware";s:8:"Software";s:8:"Software";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:10:"artikeltyp";s:4:"type";s:11:"optionField";s:5:"title";s:3:"Typ";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"artikelnummer";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Artikel Nr.";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:18:"artikelbezeichnung";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Bezeichnung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:12:"beschreibung";s:4:"type";s:8:"longText";s:5:"title";s:12:"Beschreibung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:4:{s:5:"Stück";s:5:"Stück";s:2:"MB";s:2:"MB";s:2:"GB";s:2:"GB";s:6:"Stunde";s:6:"Stunde";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:14:"artikeleinheit";s:4:"type";s:11:"optionField";s:5:"title";s:7:"Einheit";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:12:"integerField":14:{s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:4:"name";s:9:"abpackung";s:4:"type";s:12:"integerField";s:5:"title";s:5:"Menge";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:11:"doubleField":15:{s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"currency";s:3:"EUR";s:4:"name";s:12:"artikelpreis";s:4:"type";s:11:"doubleField";s:5:"title";s:5:"Preis";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:9:"shortText":16:{s:5:"value";s:3:"19%";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:10:"steuersatz";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Steuersatz";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:11;O:9:"shortText":16:{s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"verrechnung";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Verrechnung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}}s:5:"title";s:7:"Artikel";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:1;O:4:"deck":5:{s:8:"elements";a:5:{i:0;O:9:"descField":14:{s:5:"value";s:19:"Bei Überschreitung:";s:9:"css_class";s:3:"t2b";s:9:"alignment";s:4:"left";s:4:"name";s:3:"bs1";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:1;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{s:2:"MB";s:2:"MB";s:2:"GB";s:2:"GB";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:22:"weitere_artikeleinheit";s:4:"type";s:11:"optionField";s:5:"title";s:7:"Einheit";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:12:"integerField":14:{s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:4:"name";s:17:"weitere_abpackung";s:4:"type";s:12:"integerField";s:5:"title";s:5:"Menge";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:11:"doubleField":15:{s:5:"value";s:4:"0,00";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";s:8:"currency";s:3:"EUR";s:4:"name";s:20:"weitere_artikelpreis";s:4:"type";s:11:"doubleField";s:5:"title";s:5:"Preis";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:9:"descField":14:{s:5:"value";s:59:"Diese Angaben sind nur für Artikel vom Typ: Traffic gültig!";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:4:"name";s:3:"bs2";s:4:"type";s:9:"descField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:7:"Traffic";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:7:"Artikel";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1027, 1, 1, 'sys', 'config', 'System Config', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"sys";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:6:"config";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:10:"sys_config";s:10:"form_width";s:3:"450";s:4:"deck";a:2:{i:0;O:4:"deck":5:{s:8:"elements";a:1:{i:0;O:9:"shorttext":16:{s:4:"name";s:9:"sys_farbe";s:4:"type";s:9:"shortText";s:5:"title";s:5:"Farbe";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:9:"Allgemein";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}i:1;O:4:"deck":5:{s:8:"elements";a:5:{i:0;O:12:"integerfield":14:{s:4:"name";s:14:"kunde_nr_start";s:4:"type";s:12:"integerField";s:5:"title";s:14:"Kunden Nr. von";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";}i:1;O:12:"integerfield":14:{s:4:"name";s:17:"anbieter_nr_start";s:4:"type";s:12:"integerField";s:5:"title";s:16:"Anbieter Nr. von";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";}i:2;O:12:"integerfield":14:{s:4:"name";s:17:"rechnung_nr_start";s:4:"type";s:12:"integerField";s:5:"title";s:16:"Rechnung Nr. von";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:5:"value";s:1:"0";s:9:"css_class";s:0:"";s:9:"maxlength";s:2:"10";}i:3;O:9:"shorttext":16:{s:4:"name";s:11:"user_prefix";s:4:"type";s:9:"shortText";s:5:"title";s:11:"User Prefix";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;}i:4;O:9:"shorttext":16:{s:4:"name";s:10:"faktura_on";s:4:"type";s:9:"shortText";s:5:"title";s:10:"faktura_on";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:5:"value";s:1:"1";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;}}s:5:"title";s:11:"ISP-Manager";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:13:"System Config";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.1;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1028, 1, 0, 'dns', 'secondary', 'Slave Zone', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"1";s:5:"modul";s:3:"dns";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:9:"secondary";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:13:"dns_secondary";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:2:{i:0;O:9:"shorttext":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:5:"title";s:6:"domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";s:1:"1";s:4:"name";s:6:"domain";s:4:"type";s:9:"shortText";}i:1;O:9:"shorttext":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:5:"title";s:13:"DNS-Master IP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"15";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:53:"/^[0-9]{1,3}\\\\.[0-9]{1,3}\\\\.[0-9]{1,3}\\\\.[0-9]{1,3}$/";s:10:"reg_fehler";s:20:"IP-Adresse ungültig.";s:6:"search";s:1:"1";s:4:"name";s:9:"master_ip";s:4:"type";s:9:"shortText";}}s:5:"title";s:10:"Slave Zone";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:10:"Slave Zone";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:13:"secondary.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:13:"isp_dns_slave";s:12:"event_insert";s:12:"slave_insert";s:12:"event_update";s:12:"slave_update";s:12:"event_delete";s:12:"slave_delete";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1029, 1, 0, 'isp', 'isp_datenbank', 'ISP Datenbank', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:13:"isp_datenbank";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:17:"isp_isp_datenbank";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:6:{i:0;O:9:"descfield":14:{s:5:"value";s:0:"";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:4:"name";s:13:"datenbankname";s:4:"type";s:9:"descField";}i:1;O:9:"descfield":14:{s:5:"value";s:0:"";s:9:"css_class";s:2:"t2";s:9:"alignment";s:4:"left";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";N;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:4:"name";s:13:"datenbankuser";s:4:"type";s:9:"descField";}i:2;O:9:"shorttext":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";s:1:"1";s:10:"write_once";N;s:5:"title";s:8:"Passwort";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:4:"name";s:11:"db_passwort";s:4:"type";s:9:"shortText";}i:4;O:11:"optionfield":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:4:"Nein";i:1;s:2:"Ja";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:5:"title";s:13:"Remote Access";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;s:4:"name";s:13:"remote_access";s:4:"type";s:11:"optionField";}i:5;O:9:"shorttext":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:5:"title";s:6:"Web-ID";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:4:"name";s:6:"web_id";s:4:"type";s:9:"shortText";}i:6;O:9:"shorttext":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:1:"1";s:8:"password";N;s:10:"write_once";N;s:5:"title";s:6:"Status";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:1:"1";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;s:4:"name";s:6:"status";s:4:"type";s:9:"shortText";}}s:5:"title";s:13:"Eigenschaften";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:13:"ISP Datenbank";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:13:"isp_datenbank";s:12:"event_insert";s:16:"datenbank_insert";s:12:"event_update";s:16:"datenbank_update";s:12:"event_delete";s:16:"datenbank_delete";s:10:"event_show";s:14:"datenbank_show";s:11:"wysiwyg_lib";N;}', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1030, 1, 0, 'isp', 'isp_web_template', 'ISP Web-Vorlage', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"1";s:5:"modul";s:3:"isp";s:4:"tree";s:1:"1";s:7:"buttons";s:1:"1";s:4:"name";s:16:"isp_web_template";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:20:"isp_isp_web_template";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:25:{i:0;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:12:"web_speicher";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Speicher MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:11:"web_traffic";s:4:"type";s:9:"shortText";s:5:"title";s:10:"Traffic MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:2;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:4:{i:1;s:7:"sperren";i:2;s:15:"benachrichtigen";i:3;s:12:"keine Aktion";s:0:"";N;}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:27:"web_traffic_ueberschreitung";s:4:"type";s:11:"optionField";s:5:"title";s:22:"Traffic-Überschreitung";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"web_userlimit";s:4:"type";s:9:"shortText";s:5:"title";s:9:"Max. User";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:4;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:15:"web_domainlimit";s:4:"type";s:9:"shortText";s:5:"title";s:11:"Max. Domain";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:5;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:3:"tr1";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:9:"web_shell";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Shell Zugriff";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_cgi";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"CGI Scripte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:8;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:16:"web_standard_cgi";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Standard CGIs";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:9;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_php";s:4:"type";s:13:"checkboxField";s:5:"title";s:11:"PHP Scripte";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:10;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:17:"web_php_safe_mode";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"PHP Safe Mode";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:11;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:8:"web_ruby";s:4:"type";s:13:"checkboxField";s:5:"title";s:4:"Ruby";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:12;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_ssi";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"SSI";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:13;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_ftp";s:4:"type";s:13:"checkboxField";s:5:"title";s:10:"FTP Zugang";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:14;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:13:"web_frontpage";s:4:"type";s:13:"checkboxField";s:5:"title";s:9:"Frontpage";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:15;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:9:"web_mysql";s:4:"type";s:13:"checkboxField";s:5:"title";s:5:"MySQL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:16;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:30:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";i:6;s:1:"6";i:7;s:1:"7";i:8;s:1:"8";i:9;s:1:"9";i:10;s:2:"10";i:11;s:2:"11";i:12;s:2:"12";i:13;s:2:"13";i:14;s:2:"14";i:15;s:2:"15";i:16;s:2:"16";i:17;s:2:"17";i:18;s:2:"18";i:19;s:2:"19";i:20;s:2:"20";i:21;s:2:"21";i:22;s:2:"22";i:23;s:2:"23";i:24;s:2:"24";i:25;s:2:"25";i:26;s:2:"26";i:27;s:2:"27";i:28;s:2:"28";i:29;s:2:"29";i:30;s:2:"30";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:20:"web_mysql_anzahl_dbs";s:4:"type";s:11:"optionField";s:5:"title";s:18:"Anzahl Datenbanken";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:17;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_ssl";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"SSL";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:18;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:11:"web_anonftp";s:4:"type";s:13:"checkboxField";s:5:"title";s:13:"Anonymous FTP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:19;O:9:"shortText":16:{s:5:"value";s:2:"-1";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:16:"web_anonftplimit";s:4:"type";s:9:"shortText";s:5:"title";s:12:"Anon. FTP MB";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:20;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:7:"web_wap";s:4:"type";s:13:"checkboxField";s:5:"title";s:3:"WAP";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:21;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:26:"web_individual_error_pages";s:4:"type";s:13:"checkboxField";s:5:"title";s:26:"Individuelle Fehler-Seiten";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:22;O:13:"checkboxField":13:{s:5:"value";N;s:9:"css_class";s:0:"";s:4:"name";s:18:"web_mailuser_login";s:4:"type";s:13:"checkboxField";s:5:"title";s:14:"Mailuser Login";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:23;O:14:"seperatorField":13:{s:5:"width";s:1:"1";s:9:"css_class";s:0:"";s:4:"name";s:5:"trweb";s:4:"type";s:14:"seperatorField";s:5:"title";N;s:8:"language";s:2:"de";s:11:"description";N;s:6:"length";i:30;s:7:"visible";i:1;s:8:"required";i:1;s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:24;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:17:"web_httpd_include";s:4:"type";s:8:"longText";s:5:"title";s:13:"httpd_inc_txt";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:11:"Web Vorlage";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:15:"ISP Web-Vorlage";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:7:"doc.gif";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:0:"";s:12:"event_insert";s:0:"";s:12:"event_update";s:0:"";s:12:"event_delete";s:0:"";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES(1031, 1, 0, 'dns', 'spf', 'SPF Record', 'O:3:"doc":28:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:14:"group_required";s:1:"0";s:5:"modul";s:3:"dns";s:4:"tree";s:1:"0";s:7:"buttons";s:1:"1";s:4:"name";s:3:"spf";s:4:"type";s:9:"text/html";s:13:"template_type";s:4:"file";s:13:"template_path";N;s:12:"storage_type";s:2:"db";s:12:"storage_path";s:7:"dns_spf";s:10:"form_width";s:3:"450";s:4:"deck";a:1:{i:0;O:4:"deck":5:{s:8:"elements";a:9:{i:0;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:4:"host";s:4:"type";s:9:"shortText";s:5:"title";s:4:"Host";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"10";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:1;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:4:"nein";i:1;s:2:"ja";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:1:"a";s:4:"type";s:11:"optionField";s:5:"title";s:1:"a";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:2;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:4:"nein";i:1;s:2:"ja";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:2:"mx";s:4:"type";s:11:"optionField";s:5:"title";s:2:"mx";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:3;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:4:"nein";i:1;s:2:"ja";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:3:"ptr";s:4:"type";s:11:"optionField";s:5:"title";s:3:"ptr";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:4;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:7:"a_break";s:4:"type";s:8:"longText";s:5:"title";s:7:"a_break";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:5;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:8:"mx_break";s:4:"type";s:8:"longText";s:5:"title";s:8:"mx_break";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:6;O:8:"longText":18:{s:5:"value";s:0:"";s:12:"storage_type";s:2:"db";s:12:"storage_path";N;s:4:"wrap";s:8:"physical";s:9:"css_class";s:0:"";s:9:"maxlength";N;s:4:"rows";s:1:"5";s:4:"name";s:9:"ip4_break";s:4:"type";s:8:"longText";s:5:"title";s:9:"ip4_break";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}i:7;O:9:"shortText":16:{s:5:"value";s:0:"";s:9:"css_class";s:0:"";s:9:"maxlength";s:3:"255";s:8:"password";N;s:10:"write_once";N;s:4:"name";s:13:"include_break";s:4:"type";s:9:"shortText";s:5:"title";s:13:"include_break";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";s:2:"30";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";s:14:"reg_expression";s:0:"";s:10:"reg_fehler";s:0:"";s:6:"search";N;}i:8;O:11:"optionField":21:{s:11:"option_type";s:8:"dropdown";s:6:"values";a:2:{i:0;s:4:"nein";i:1;s:2:"ja";}s:6:"source";s:4:"list";s:12:"source_table";s:0:"";s:11:"value_field";s:0:"";s:8:"id_field";s:0:"";s:9:"css_class";s:0:"";s:4:"size";s:0:"";s:8:"multiple";N;s:5:"order";N;s:4:"name";s:4:"all_";s:4:"type";s:11:"optionField";s:5:"title";s:4:"all_";s:8:"language";s:2:"de";s:11:"description";s:0:"";s:6:"length";i:30;s:7:"visible";s:1:"1";s:8:"required";s:1:"1";s:14:"reg_expression";N;s:10:"reg_fehler";N;s:6:"search";N;}}s:5:"title";s:6:"Record";s:7:"visible";s:1:"1";s:9:"perm_read";s:1:"r";s:10:"perm_write";s:1:"r";}}s:5:"title";s:10:"SPF Record";s:8:"keywords";N;s:11:"description";s:0:"";s:4:"path";N;s:4:"icon";s:0:"";s:5:"cache";s:1:"0";s:8:"permtype";N;s:7:"version";d:1.100000000000000088817841970012523233890533447265625;s:11:"event_class";s:7:"isp_dns";s:12:"event_insert";s:10:"soa_insert";s:12:"event_update";s:10:"soa_update";s:12:"event_delete";s:10:"soa_delete";s:10:"event_show";s:0:"";s:11:"wysiwyg_lib";N;}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `groupid` bigint(21) NOT NULL auto_increment,
  `userid` bigint(21) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `art` varchar(255) NOT NULL default '',
  `datum` bigint(21) NOT NULL default '0',
  `groupstatus` tinyint(4) NOT NULL default '0',
  `beschreibung` text NOT NULL,
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `groupid_2` (`groupid`),
  KEY `groupid` (`groupid`,`userid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`groupid`, `userid`, `name`, `art`, `datum`, `groupstatus`, `beschreibung`) VALUES (1, 1, 'admin', '', 1015329293, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `help_documents`
--

CREATE TABLE `help_documents` (
  `doc_id` bigint(20) unsigned NOT NULL auto_increment,
  `doctype_id` int(10) unsigned NOT NULL default '10',
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `h_title` varchar(255) NOT NULL default '',
  `h_text` text NOT NULL,
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id_2` (`doc_id`),
  KEY `doc_id` (`doc_id`,`userid`,`groupid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `help_documents`
--


-- --------------------------------------------------------

--
-- Table structure for table `help_nodes`
--

CREATE TABLE `help_nodes` (
  `tree_id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `parent` varchar(100) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  `doctype_id` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '1',
  `icon` varchar(255) NOT NULL default '',
  `modul` varchar(255) NOT NULL default '',
  `doc_id` bigint(21) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tree_id`),
  UNIQUE KEY `tree_id_2` (`tree_id`),
  KEY `tree_id` (`tree_id`,`userid`,`groupid`),
  KEY `doc_id` (`doc_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `help_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `help_tickets`
--

CREATE TABLE `help_tickets` (
  `doc_id` bigint(20) unsigned NOT NULL auto_increment,
  `ticket_from` bigint(20) unsigned default NULL,
  `ticket_to` bigint(20) unsigned default NULL,
  `ticket_status` char(1) default NULL,
  `ticket_reply` bigint(20) unsigned default NULL,
  `ticket_urgency` char(1) default NULL,
  `ticket_date` datetime default NULL,
  `ticket_subject` varchar(255) default NULL,
  `ticket_message` text,
  PRIMARY KEY  (`doc_id`),
  KEY `ticket_from` (`ticket_from`),
  KEY `ticket_to` (`ticket_to`),
  KEY `ticket_status` (`ticket_status`)
) TYPE=MyISAM;

--
-- Dumping data for table `help_tickets`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_com`
--

CREATE TABLE `isp_com` (
  `id` int(11) NOT NULL auto_increment,
  `server_id` int(11) NOT NULL default '0',
  `modul` varchar(50) NOT NULL default '',
  `funktion` varchar(50) NOT NULL default '',
  `data` text NOT NULL,
  `sc` varchar(255) NOT NULL default '',
  `tstamp` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_com`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_dep`
--

CREATE TABLE `isp_dep` (
  `dep_id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `parent_doc_id` int(10) unsigned NOT NULL default '0',
  `parent_doctype_id` int(10) unsigned NOT NULL default '0',
  `parent_tree_id` int(11) NOT NULL default '0',
  `child_doc_id` int(11) NOT NULL default '0',
  `child_doctype_id` int(11) NOT NULL default '0',
  `child_tree_id` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`dep_id`),
  UNIQUE KEY `dep_id_2` (`dep_id`),
  KEY `dep_id` (`dep_id`,`userid`,`groupid`,`parent_doc_id`,`parent_doctype_id`),
  KEY `tree_id` (`parent_tree_id`,`child_doc_id`,`child_doctype_id`,`child_tree_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `isp_dep`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_dienste`
--

CREATE TABLE `isp_dienste` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1023',
  `dienst_www_status` varchar(255) default NULL,
  `dienst_ftp_status` varchar(255) default NULL,
  `dienst_smtp_status` varchar(255) default NULL,
  `dienst_dns_status` varchar(255) default NULL,
  `dienst_mysql_status` varchar(255) default NULL,
  `status` char(1) default NULL,
  `dienst_firewall_status` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_dienste`
--

INSERT INTO `isp_dienste` (`doc_id`, `doctype_id`, `dienst_www_status`, `dienst_ftp_status`, `dienst_smtp_status`, `dienst_dns_status`, `dienst_mysql_status`, `status`, `dienst_firewall_status`) VALUES (1, 1023, 'on', 'on', 'on', 'on', 'on', '', 'off');

-- --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `isp_fakt_artikel`
#

CREATE TABLE isp_fakt_artikel (
  doc_id bigint(20) NOT NULL auto_increment,
  doctype_id bigint(20) NOT NULL default '1026',
  artikelnummer varchar(255) default NULL,
  artikeltyp varchar(255) default NULL,
  artikelbezeichnung varchar(255) default NULL,
  verrechnung varchar(255) default NULL,
  artikeleinheit varchar(255) default NULL,
  artikelpreis double default NULL,
  abpackung int(11) default NULL,
  beschreibung text,
  artikelgroup varchar(255) default NULL,
  steuersatz varchar(255) default NULL,
  weitere_artikeleinheit varchar(255) default NULL,
  weitere_abpackung int(11) default NULL,
  weitere_artikelpreis double default NULL,
  PRIMARY KEY  (doc_id),
  KEY doctype_id (doctype_id)
) TYPE=MyISAM;

#
# Daten für Tabelle `isp_fakt_artikel`
#

INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (2, 1026, 'de0001', 'Domain', 'Domain .tld', '0', 'Stück', '0', 1, '', '', '19%', NULL, NULL, NULL);
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (3, 1026, 'wp0001', 'Web', 'Web Package 1', '12', 'Stück', '0', 1, 'Demo Web-Package', '', '19%', NULL, NULL, NULL);
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (4, 1026, 'SV0001', 'Dienstleistung', 'Service Vertrag Basis', '0', 'Stück', '0', 1, '', '', '19%', NULL, NULL, NULL);
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (5, 1026, 'TF17782', 'Traffic', 'Traffic 5 GB', '1', 'GB', '0', 5, '', '', '19%', 'GB', 1, '0');
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (6, 1026, 'EM0001', 'Email', 'Email Adresse', '1', 'Stück', '0', 1, 'Demo Email Acoount', '', '19%', NULL, NULL, NULL);
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `isp_fakt_dep`
#

CREATE TABLE isp_fakt_dep (
  dep_id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  groupid int(10) unsigned NOT NULL default '0',
  parent_doc_id int(10) unsigned NOT NULL default '0',
  parent_doctype_id int(10) unsigned NOT NULL default '0',
  parent_tree_id int(11) NOT NULL default '0',
  child_doc_id int(11) NOT NULL default '0',
  child_doctype_id int(11) NOT NULL default '0',
  child_tree_id int(11) NOT NULL default '0',
  STATUS tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (dep_id),
  UNIQUE KEY dep_id_2 (dep_id),
  KEY dep_id (dep_id,userid,groupid,parent_doc_id,parent_doctype_id),
  KEY tree_id (parent_tree_id,child_doc_id,child_doctype_id,child_tree_id)
) TYPE=MyISAM PACK_KEYS=1;

#
# Daten für Tabelle `isp_fakt_dep`
#

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `isp_fakt_nodes`
#

CREATE TABLE isp_fakt_nodes (
  tree_id bigint(20) unsigned NOT NULL auto_increment,
  userid bigint(20) unsigned NOT NULL default '0',
  groupid bigint(20) unsigned NOT NULL default '0',
  parent varchar(100) NOT NULL default '',
  type char(1) NOT NULL default '',
  doctype_id int(11) NOT NULL default '0',
  status char(1) NOT NULL default '1',
  icon varchar(255) NOT NULL default '',
  modul varchar(255) NOT NULL default '',
  doc_id bigint(21) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  PRIMARY KEY  (tree_id),
  UNIQUE KEY tree_id_2 (tree_id),
  KEY tree_id (tree_id,userid,groupid),
  KEY doc_id (doc_id)
) TYPE=MyISAM PACK_KEYS=1;

#
# Daten für Tabelle `isp_fakt_nodes`
#

INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (2, 1, 1, '3', 'i', 1026, '1', '', '', 2, 'domain .tld');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (3, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Domains');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (4, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Service');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (5, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Webs');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (6, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Traffic');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (7, 1, 1, '5', 'i', 1026, '1', '', '', 3, 'Web Paket 1');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (8, 1, 1, '4', 'i', 1026, '1', '', '', 4, 'Service Vertrag Basis');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (9, 1, 1, '6', 'i', 1026, '1', '', '', 5, 'Traffic 5 GB');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (10, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Email-Adressen');
INSERT INTO isp_fakt_nodes (tree_id, userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title) VALUES (11, 1, 1, '10', 'i', 1026, '1', '', '', 6, 'Email Adresse');
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `isp_fakt_rechnung`
#

CREATE TABLE isp_fakt_rechnung (
  rechnung_id int(11) NOT NULL auto_increment,
  reseller_id int(11) NOT NULL default '0',
  kunde_id int(11) NOT NULL default '0',
  typ varchar(10) NOT NULL default 'client',
  datum bigint(20) NOT NULL default '0',
  versand char(1) NOT NULL default '',
  rechnung text NOT NULL,
  data text NOT NULL,
  PRIMARY KEY  (rechnung_id)
) TYPE=MyISAM;

#
# Daten für Tabelle `isp_fakt_rechnung`
#

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `isp_fakt_record`
#

CREATE TABLE isp_fakt_record (
  record_id int(11) NOT NULL auto_increment,
  artikel_id int(11) NOT NULL default '0',
  web_id int(11) NOT NULL default '0',
  typ varchar(100) NOT NULL default '',
  manuell char(1) NOT NULL default '0',
  doc_id int(11) NOT NULL default '0',
  doctype_id int(11) NOT NULL default '0',
  anzahl int(11) NOT NULL default '1',
  erste_abrechnung bigint(20) NOT NULL default '0',
  letzte_abrechnung bigint(20) NOT NULL default '0',
  notiz varchar(255) default NULL,
  status tinyint(4) NOT NULL default '1',
  UNIQUE KEY record_id (record_id),
  KEY web_id (web_id,doc_id,doctype_id)
) TYPE=MyISAM;

#
# Daten für Tabelle `isp_fakt_record`
#

# --------------------------------------------------------

--
-- Table structure for table `isp_firewall`
--

CREATE TABLE `isp_firewall` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1025',
  `dienst_name` varchar(255) default NULL,
  `dienst_port` varchar(11) default NULL,
  `dienst_aktiv` varchar(255) default NULL,
  `dienst_typ` varchar(255) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_firewall`
--

INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (1, 1025, 'FTP', '21', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (2, 1025, 'SSH', '22', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (3, 1025, 'SMTP', '25', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (4, 1025, 'DNS', '53', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (5, 1025, 'DNS', '53', 'ja', 'udp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (6, 1025, 'WWW', '80', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (7, 1025, 'ISPConfig', '81', 'ja', 'tcp', '');
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (8, 1025, 'POP3', '110', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (9, 1025, 'IMAP2', '143', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (10, 1025, 'SSL (www)', '443', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (11, 1025, 'Webmin', '10000', 'ja', 'tcp', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `isp_htaccess`
--

CREATE TABLE `isp_htaccess` (
  `doc_id` int(11) NOT NULL auto_increment,
  `user` varchar(50) NOT NULL default '',
  `passwort` varchar(50) NOT NULL default '',
  `htaccess_verzeichnis` varchar(100) NOT NULL default '',
  `web_doc_id` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id` (`doc_id`),
  KEY `doc_id_2` (`doc_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_htaccess`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_actions`
--

CREATE TABLE `isp_isp_actions` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1011',
  `on_action` varchar(255) default NULL,
  `zeit` varchar(5) default NULL,
  `intervall` varchar(5) default NULL,
  `pfad` varchar(255) default NULL,
  `titel` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_actions`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_admin`
--

CREATE TABLE `isp_isp_admin` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1023',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_admin`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_datenbank`
--

CREATE TABLE `isp_isp_datenbank` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1029',
  `datenbankname` varchar(255) NOT NULL default '',
  `datenbankuser` varchar(255) NOT NULL default '',
  `db_passwort` varchar(255) default NULL,
  `web_id` varchar(255) default NULL,
  `status` char(1) default NULL,
  `remote_access` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_datenbank`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_domain`
--

CREATE TABLE `isp_isp_domain` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1015',
  `domain_host` varchar(255) default NULL,
  `domain_dns` char(1) default NULL,
  `domain_dnsmail` smallint(6) default NULL,
  `domain_register` varchar(255) default NULL,
  `domain_weiterleitung` varchar(255) default NULL,
  `status` char(1) default NULL,
  `domain_domain` varchar(255) default NULL,
  `domain_ip` varchar(255) default NULL,
  `domain_local_mailserver` varchar(255) default '1',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_domain`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_kunde`
--

CREATE TABLE `isp_isp_kunde` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1012',
  `kunde_anrede` varchar(255) default NULL,
  `kunde_firma` varchar(255) default NULL,
  `kunde_vorname` varchar(255) default NULL,
  `kunde_name` varchar(255) default NULL,
  `kunde_strasse` varchar(255) default NULL,
  `kunde_plz` varchar(255) default NULL,
  `kunde_ort` varchar(255) default NULL,
  `kunde_land` varchar(255) default NULL,
  `kunde_telefon` varchar(255) default NULL,
  `kunde_fax` varchar(255) default NULL,
  `kunde_email` varchar(255) default NULL,
  `kunde_internet` varchar(255) default NULL,
  `webadmin_user` varchar(255) default NULL,
  `webadmin_passwort` varchar(255) default NULL,
  `webadmin_userid` int(11) NOT NULL default '0',
  `rechnung_firma` varchar(255) default NULL,
  `rechnung_vorname` varchar(255) default NULL,
  `rechnung_name` varchar(255) default NULL,
  `rechnung_strasse` varchar(255) default NULL,
  `rechnung_plz` varchar(255) default NULL,
  `rechnung_ort` varchar(255) default NULL,
  `rechnung_land` varchar(255) default NULL,
  `rechnung_intervall` varchar(255) default NULL,
  `rechnung_preis` double default NULL,
  `rechnung_zahlungsbedingungen` varchar(255) default NULL,
  `kunde_province` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_kunde`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_reseller`
--

CREATE TABLE `isp_isp_reseller` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1022',
  `firma` varchar(255) default '',
  `vorname` varchar(255) default '',
  `limit_user` varchar(255) default '-1',
  `limit_disk` varchar(255) default '-1',
  `limit_web` varchar(255) default '-1',
  `limit_domain` varchar(255) default '-1',
  `reseller_group` varchar(100) default '',
  `name` varchar(255) default '',
  `strasse` varchar(255) default '',
  `plz` varchar(255) default '',
  `ort` varchar(255) default '',
  `telefon` varchar(255) default '',
  `fax` varchar(255) default '',
  `email` varchar(255) default '',
  `internet` varchar(255) default '',
  `reseller_user` varchar(255) default '',
  `reseller_passwort` varchar(255) default '',
  `reseller_userid` int(11) NOT NULL default '0',
  `anrede` varchar(255) default '',
  `land` varchar(255) default '',
  `limit_httpd_include` char(1) default '0',
  `limit_dns_manager` char(1) default '0',
  `limit_domain_dns` varchar(255) default '-1',
  `province` varchar(255) default '',
  `limit_shell_access` char(1) default '0',
  `limit_cgi` char(1) NOT NULL default '1',
  `limit_php` char(1) NOT NULL default '1',
  `limit_ssi` char(1) NOT NULL default '1',
  `limit_ftp` char(1) NOT NULL default '1',
  `limit_mysql` smallint(6) default '0',
  `limit_ssl` char(1) NOT NULL default '1',
  `limit_anonftp` char(1) NOT NULL default '1',
  `limit_standard_cgis` smallint(6) default '1',
  `limit_wap` smallint(6) default '1',
  `limit_error_pages` smallint(6) default '1',
  `limit_frontpage` char(1) default '0',
  `limit_mysql_anzahl_dbs` varchar(255) default '-1',
  `limit_slave_dns` varchar(255) default '-1',
  `client_salutatory_email_sender_email` varchar(255) default '',
  `client_salutatory_email_sender_name` varchar(255) default '',
  `client_salutatory_email_bcc` varchar(255) default '',
  `client_salutatory_email_subject` varchar(255) default '',
  `client_salutatory_email_message` text NOT NULL,
  `standard_index` text NOT NULL,
  `user_standard_index` text NOT NULL,
  `traffic_suspension_sender_email` varchar(255) default '',
  `traffic_suspension_sender_name` varchar(255) default '',
  `traffic_suspension_email_bcc` varchar(255) default '',
  `traffic_suspension_email_subject` varchar(255) default '',
  `traffic_suspension_email_message` text NOT NULL,
  `traffic_notification_sender_email` varchar(255) default '',
  `traffic_notification_sender_name` varchar(255) default '',
  `traffic_notification_email_bcc` varchar(255) default '',
  `traffic_notification_email_subject` varchar(255) default '',
  `traffic_notification_email_message` text NOT NULL,
  `limit_traffic` varchar(255) default '-1',
  `limit_traffic_ueberschreitung` varchar(255) default '1',
  `limit_ruby` char(1) default '1',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_reseller`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_user`
--

CREATE TABLE `isp_isp_user` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1014',
  `user_username` varchar(32) default NULL,
  `user_passwort` varchar(64) default NULL,
  `user_speicher` varchar(10) default NULL,
  `user_admin` char(1) default NULL,
  `user_shell` char(1) default NULL,
  `user_emailalias` text,
  `user_name` varchar(255) default NULL,
  `status` char(1) default NULL,
  `user_autoresponder` char(1) default NULL,
  `user_autoresponder_text` text,
  `user_mailquota` varchar(10) default NULL,
  `user_catchallemail` char(1) default '0',
  `user_mailscan` char(1) default NULL,
  `user_emailweiterleitung_local_copy` smallint(6) default NULL,
  `user_email` varchar(255) default NULL,
  `user_emailweiterleitung` text,
  `user_spamfilter` char(1) default NULL,
  `spam_strategy` varchar(255) NOT NULL default 'accept',
  `spam_hits` varchar(255) default '5.0',
  `spam_rewrite_subject` char(1) NOT NULL default '1',
  `spam_subject_tag` varchar(255) NOT NULL default '***SPAM***',
  `antivirus` char(1) NOT NULL default '0',
  `user_lang` char(2) NOT NULL default '',
  `spam_whitelist` text,
  `spam_blacklist` text,
  `use_uribl` char(1) default '0',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_web`
--

CREATE TABLE `isp_isp_web` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1013',
  `web_host` varchar(255) default NULL,
  `web_domain` varchar(255) default NULL,
  `web_speicher` varchar(10) default NULL,
  `web_traffic` varchar(10) default NULL,
  `web_cgi` char(1) default NULL,
  `web_php` char(1) default NULL,
  `web_ssi` char(1) default NULL,
  `web_ftp` smallint(6) default NULL,
  `web_frontpage` smallint(6) default NULL,
  `web_mysql` smallint(6) default NULL,
  `web_postgresql` smallint(6) default NULL,
  `web_shell` char(1) default NULL,
  `web_shop` char(1) default NULL,
  `web_phpmyadmin` char(1) default NULL,
  `web_webmail` char(1) default NULL,
  `web_webalizer` char(1) default NULL,
  `web_ssl` char(1) default NULL,
  `status` char(1) default NULL,
  `ssl_request` text,
  `ssl_cert` text,
  `ssl_action` varchar(255) default NULL,
  `web_ip` varchar(255) default NULL,
  `server_id` varchar(255) default NULL,
  `optionen_mysql_user` varchar(255) default NULL,
  `optionen_mysql_passwort` varchar(255) default NULL,
  `web_dns` char(1) default NULL,
  `web_userlimit` varchar(255) default NULL,
  `web_domainlimit` varchar(255) default NULL,
  `ssl_state` varchar(255) default NULL,
  `ssl_locality` varchar(255) default NULL,
  `ssl_organization` varchar(255) default NULL,
  `ssl_organization_unit` varchar(255) default NULL,
  `ssl_country` varchar(255) default NULL,
  `web_dns_mx` smallint(6) default NULL,
  `ssl_days` varchar(255) default NULL,
  `web_httpd_include` text,
  `web_anonftp` char(1) default NULL,
  `web_anonftplimit` varchar(255) default NULL,
  `optionen_frontpage_passwort` varchar(255) default NULL,
  `optionen_mysql_remote_access` smallint(6) default NULL,
  `optionen_local_mailserver` varchar(255) default '1',
  `web_faktura` varchar(255) default '1',
  `optionen_logsize` varchar(255) default '30%',
  `optionen_directory_index` text,
  `web_php_safe_mode` char(1) default NULL,
  `web_wap` char(1) default NULL,
  `web_standard_cgi` char(1) default NULL,
  `web_individual_error_pages` char(1) default NULL,
  `error_401` text,
  `error_404` text,
  `error_403` text,
  `error_500` text,
  `error_400` text,
  `error_405` text,
  `error_503` text,
  `web_mysql_anzahl_dbs` varchar(255) default NULL,
  `web_mailuser_login` smallint(6) default NULL,
  `web_traffic_ueberschreitung` varchar(255) default NULL,
  `web_traffic_status` varchar(255) default NULL,
  `webalizer_stats` char(1) default '1',
  `web_ruby` char(1) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_web`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_isp_web_template`
--

CREATE TABLE `isp_isp_web_template` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1030',
  `web_speicher` varchar(255) default NULL,
  `web_userlimit` varchar(255) default NULL,
  `web_domainlimit` varchar(255) default NULL,
  `web_shell` char(1) default NULL,
  `web_cgi` char(1) default NULL,
  `web_standard_cgi` char(1) default NULL,
  `web_php` char(1) default NULL,
  `web_php_safe_mode` char(1) default NULL,
  `web_ssi` char(1) default NULL,
  `web_ftp` char(1) default NULL,
  `web_frontpage` char(1) default NULL,
  `web_mysql` char(1) default NULL,
  `web_mysql_anzahl_dbs` varchar(255) default NULL,
  `web_ssl` char(1) default NULL,
  `web_anonftp` char(1) default NULL,
  `web_anonftplimit` varchar(255) default NULL,
  `web_wap` char(1) default NULL,
  `web_individual_error_pages` char(1) default NULL,
  `web_httpd_include` text,
  `web_mailuser_login` char(1) default NULL,
  `web_traffic` varchar(255) default NULL,
  `web_traffic_ueberschreitung` varchar(255) default NULL,
  `web_ruby` char(1) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_isp_web_template`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_monitor`
--

CREATE TABLE `isp_monitor` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1024',
  `dienst_monitor` varchar(255) default NULL,
  `dienst_host` varchar(255) default NULL,
  `dienst_port` varchar(255) default NULL,
  `dienst_typ` varchar(255) default NULL,
  `dienst_run_online` varchar(255) default NULL,
  `dienst_run_offline` varchar(255) default NULL,
  `status` char(1) default NULL,
  `dienst_name` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_monitor`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_nodes`
--

CREATE TABLE `isp_nodes` (
  `tree_id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `parent` varchar(100) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  `doctype_id` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '1',
  `icon` varchar(255) NOT NULL default '',
  `modul` varchar(255) NOT NULL default '',
  `doc_id` bigint(21) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tree_id`),
  UNIQUE KEY `tree_id_2` (`tree_id`),
  KEY `tree_id` (`tree_id`,`userid`,`groupid`),
  KEY `doc_id` (`doc_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `isp_nodes`
--

INSERT INTO `isp_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (1, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Anbieter');
INSERT INTO `isp_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (2, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Kunden');
INSERT INTO `isp_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (3, 1, 1, 'group1', 'n', 0, '1', '', '', 0, 'Webs');

-- --------------------------------------------------------

--
-- Table structure for table `isp_server`
--

CREATE TABLE `isp_server` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1010',
  `server_host` varchar(255) default NULL,
  `server_domain` varchar(255) default NULL,
  `server_ip` varchar(15) default NULL,
  `server_netzmaske` varchar(15) default NULL,
  `server_sprache` varchar(255) default NULL,
  `server_db_type` varchar(255) default NULL,
  `server_db_user` varchar(30) default NULL,
  `server_db_passwort` varchar(30) default NULL,
  `server_path_httpd_conf` varchar(255) default NULL,
  `server_path_httpd_root` varchar(255) default NULL,
  `server_httpd_user` varchar(255) default NULL,
  `server_httpd_group` varchar(30) default NULL,
  `server_path_frontpage` varchar(255) default NULL,
  `server_path_httpd_error` varchar(255) default NULL,
  `server_name` varchar(255) default NULL,
  `server_mta` varchar(255) default NULL,
  `server_sendmail_virtuser_datei` varchar(255) default NULL,
  `server_sendmail_cw` varchar(255) default NULL,
  `server_ftp_typ` varchar(255) default NULL,
  `server_proftpd_conf_datei` varchar(255) default NULL,
  `server_proftpd_log` varchar(255) default NULL,
  `server_bind_user` varchar(255) default NULL,
  `server_bind_group` varchar(255) default NULL,
  `server_bind_named_conf` varchar(255) default NULL,
  `server_bind_zonefile_dir` varchar(255) default NULL,
  `userid_von` varchar(10) default NULL,
  `groupid_von` varchar(10) default NULL,
  `passwd_datei` varchar(255) default NULL,
  `group_datei` varchar(255) default NULL,
  `server_ipliste` text,
  `shadow_datei` varchar(255) default NULL,
  `server_bind_ns1_default` varchar(255) default NULL,
  `server_bind_ns2_default` varchar(255) default NULL,
  `server_path_httpd_log` varchar(255) default NULL,
  `server_soap_ip` varchar(255) default NULL,
  `server_soap_port` varchar(255) default NULL,
  `server_soap_encoding` varchar(255) default NULL,
  `server_admin_email` varchar(255) default NULL,
  `server_bind_standard_mx` char(1) default NULL,
  `server_bind_adminmail_default` varchar(255) default NULL,
  `server_mail_log_save` char(1) default NULL,
  `server_ftp_log_save` char(1) default NULL,
  `server_httpd_suexec` char(1) default NULL,
  `dist` varchar(255) default NULL,
  `dist_init_scripts` varchar(255) default NULL,
  `dist_runlevel` varchar(255) default NULL,
  `dist_smrsh` varchar(255) default NULL,
  `dist_shells` varchar(255) default NULL,
  `dist_bind_init_script` varchar(255) default NULL,
  `dist_bind_pidfile` varchar(255) default NULL,
  `dist_bind_hintfile` varchar(255) default NULL,
  `dist_bind_localfile` varchar(255) default NULL,
  `dist_cron_daemon` varchar(255) default NULL,
  `dist_cron_tab` varchar(255) default NULL,
  `dist_mysql_group` varchar(255) default NULL,
  `dist_httpd_daemon` varchar(255) default NULL,
  `dist_pop3` varchar(255) default NULL,
  `dist_pop3_version` varchar(255) default NULL,
  `dist_ftp_version` varchar(255) default NULL,
  `dist_httpd_conf` varchar(255) default NULL,
  `dist_mail_log` varchar(255) default NULL,
  `use_maildir` char(1) default NULL,
  `virusadmin` varchar(255) NOT NULL default 'admispconfig@localhost',
  `spamfilter_enable` char(1) NOT NULL default '1',
  `server_enable_frontpage` varchar(255) default NULL,
  `client_salutatory_email_sender_email` varchar(255) default NULL,
  `client_salutatory_email_sender_name` varchar(255) default NULL,
  `client_salutatory_email_bcc` varchar(255) default NULL,
  `client_salutatory_email_subject` varchar(255) default NULL,
  `client_salutatory_email_message` text,
  `res_salutatory_email_sender_email` varchar(255) default NULL,
  `res_salutatory_email_sender_name` varchar(255) default NULL,
  `res_salutatory_email_bcc` varchar(255) default NULL,
  `res_salutatory_email_subject` varchar(255) default NULL,
  `res_salutatory_email_message` text,
  `standard_index` text,
  `user_standard_index` text,
  `traffic_suspension_sender_email` varchar(255) default NULL,
  `traffic_suspension_sender_name` varchar(255) default NULL,
  `traffic_suspension_email_bcc` varchar(255) default NULL,
  `traffic_suspension_email_subject` varchar(255) default NULL,
  `traffic_suspension_email_message` text,
  `traffic_notification_sender_email` varchar(255) default NULL,
  `traffic_notification_sender_name` varchar(255) default NULL,
  `traffic_notification_email_bcc` varchar(255) default NULL,
  `traffic_notification_email_subject` varchar(255) default NULL,
  `traffic_notification_email_message` text,
  `res_traffic_suspension_sender_email` varchar(255) default NULL,
  `res_traffic_suspension_sender_name` varchar(255) default NULL,
  `res_traffic_suspension_email_bcc` varchar(255) default NULL,
  `res_traffic_suspension_email_subject` varchar(255) default NULL,
  `res_traffic_suspension_email_message` text,
  `res_traffic_notification_sender_email` varchar(255) default NULL,
  `res_traffic_notification_sender_name` varchar(255) default NULL,
  `res_traffic_notification_email_bcc` varchar(255) default NULL,
  `res_traffic_notification_email_subject` varchar(255) default NULL,
  `res_traffic_notification_email_message` text,
  `global_stats_user` varchar(255) default NULL,
  `global_stats_password` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;


--
-- Dumping data for table `isp_server`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_server_ip`
--

CREATE TABLE `isp_server_ip` (
  `doc_id` int(11) NOT NULL auto_increment,
  `server_id` int(11) NOT NULL default '0',
  `server_ip` char(15) NOT NULL default '',
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id` (`doc_id`),
  KEY `server_id` (`server_id`,`server_ip`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_server_ip`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_serverstatus`
--

CREATE TABLE `isp_serverstatus` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1021',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_serverstatus`
--

INSERT INTO `isp_serverstatus` (`doc_id`, `doctype_id`) VALUES (1, 1021);

-- --------------------------------------------------------

--
-- Table structure for table `isp_traffic`
--

CREATE TABLE `isp_traffic` (
  `doc_id` int(11) NOT NULL auto_increment,
  `web_id` int(11) NOT NULL default '0',
  `monat` varchar(7) NOT NULL default '',
  `jahr` varchar(4) NOT NULL default '',
  `datum` bigint(20) NOT NULL default '0',
  `bytes_web` bigint(20) NOT NULL default '0',
  `bytes_ftp` bigint(20) NOT NULL default '0',
  `bytes_mail` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id` (`doc_id`),
  KEY `doc_id_2` (`doc_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_traffic`
--


-- --------------------------------------------------------

--
-- Table structure for table `isp_traffic_ip`
--

CREATE TABLE `isp_traffic_ip` (
  `doc_id` int(11) NOT NULL auto_increment,
  `monat` tinyint(4) NOT NULL default '0',
  `jahr` int(11) NOT NULL default '0',
  `datum` bigint(20) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `traffic_in` bigint(20) NOT NULL default '0',
  `traffic_out` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`doc_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `isp_traffic_ip`
--


-- --------------------------------------------------------

--
-- Table structure for table `listtype`
--

CREATE TABLE `listtype` (
  `listtype_id` int(10) unsigned NOT NULL auto_increment,
  `userid` bigint(21) NOT NULL default '0',
  `groupid` bigint(21) NOT NULL default '0',
  `listtype_modul` varchar(255) NOT NULL default '',
  `listtype_datatable` varchar(255) NOT NULL default '',
  `listtype_title` varchar(255) NOT NULL default '',
  `listtype_def` text NOT NULL,
  `listtype_doctype_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`listtype_id`),
  UNIQUE KEY `doctype_id_2` (`listtype_id`),
  KEY `doctype_id` (`listtype_id`,`userid`,`groupid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `listtype`
--

INSERT INTO `listtype` (`listtype_id`, `userid`, `groupid`, `listtype_modul`, `listtype_datatable`, `listtype_title`, `listtype_def`, `listtype_doctype_id`) VALUES (1, 1, 0, '0', '', 'Standard', 'O:5:"liste":15:{s:5:"limit";s:2:"30";s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:5:"modul";s:1:"0";s:5:"title";s:8:"Standard";s:10:"doctype_id";s:4:"1013";s:11:"description";s:0:"";s:5:"cache";N;s:5:"perms";s:2:"rw";s:5:"width";s:4:"100%";s:4:"icon";s:0:"";s:5:"query";s:0:"";s:10:"orderfield";s:10:"web_domain";s:9:"datatable";s:0:"";s:3:"row";a:1:{i:0;O:3:"row":4:{s:5:"title";s:2:"Z1";s:11:"edit_button";s:1:"1";s:13:"delete_button";s:1:"1";s:8:"elements";a:3:{i:0;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:6:"web_ip";s:5:"width";s:2:"20";s:4:"nobr";s:1:"1";s:9:"css_class";s:3:"t2b";s:9:"maxlength";s:0:"";}i:1;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:8:"web_host";s:5:"width";s:2:"10";s:4:"nobr";s:1:"1";s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";}i:2;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:10:"web_domain";s:5:"width";s:2:"70";s:4:"nobr";s:1:"1";s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";}}}}}', 1013);
INSERT INTO `listtype` (`listtype_id`, `userid`, `groupid`, `listtype_modul`, `listtype_datatable`, `listtype_title`, `listtype_def`, `listtype_doctype_id`) VALUES (2, 1, 0, '0', '', 'Standard', 'O:5:"liste":15:{s:5:"limit";s:2:"30";s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:5:"modul";s:1:"0";s:5:"title";s:8:"Standard";s:10:"doctype_id";s:4:"1012";s:11:"description";s:0:"";s:5:"cache";N;s:5:"perms";s:2:"rw";s:5:"width";s:4:"100%";s:4:"icon";s:0:"";s:5:"query";s:0:"";s:10:"orderfield";s:10:"kunde_name";s:9:"datatable";s:0:"";s:3:"row";a:1:{i:0;O:3:"row":4:{s:5:"title";s:2:"Z1";s:11:"edit_button";s:1:"1";s:13:"delete_button";s:1:"1";s:8:"elements";a:3:{i:0;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:11:"kunde_firma";s:5:"width";s:2:"30";s:4:"nobr";N;s:9:"css_class";s:3:"t2b";s:9:"maxlength";s:0:"";}i:1;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:13:"kunde_vorname";s:5:"width";s:2:"30";s:4:"nobr";N;s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";}i:2;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:10:"kunde_name";s:5:"width";s:2:"30";s:4:"nobr";N;s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";}}}}}', 1012);
INSERT INTO `listtype` (`listtype_id`, `userid`, `groupid`, `listtype_modul`, `listtype_datatable`, `listtype_title`, `listtype_def`, `listtype_doctype_id`) VALUES (3, 1, 1, '0', '', 'Standard', 'O:5:"liste":15:{s:5:"limit";s:2:"30";s:6:"userid";s:1:"1";s:7:"groupid";s:1:"1";s:5:"modul";s:1:"0";s:5:"title";s:8:"Standard";s:10:"doctype_id";s:4:"1022";s:11:"description";s:0:"";s:5:"cache";N;s:5:"perms";s:2:"rw";s:5:"width";s:4:"100%";s:4:"icon";s:0:"";s:5:"query";s:0:"";s:10:"orderfield";s:0:"";s:9:"datatable";s:0:"";s:3:"row";a:1:{i:0;O:3:"row":4:{s:5:"title";s:2:"R1";s:11:"edit_button";s:1:"1";s:13:"delete_button";s:1:"1";s:8:"elements";a:3:{i:0;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:5:"firma";s:5:"width";s:2:"30";s:4:"nobr";s:1:"1";s:9:"css_class";s:3:"t2b";s:9:"maxlength";s:0:"";}i:1;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:7:"vorname";s:5:"width";s:2:"30";s:4:"nobr";s:1:"1";s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";}i:2;O:4:"item":6:{s:4:"type";s:4:"text";s:4:"name";s:4:"name";s:5:"width";s:2:"30";s:4:"nobr";s:1:"1";s:9:"css_class";s:2:"t2";s:9:"maxlength";s:0:"";}}}}}', 1022);
INSERT INTO `listtype` (`listtype_id`, `userid`, `groupid`, `listtype_modul`, `listtype_datatable`, `listtype_title`, `listtype_def`, `listtype_doctype_id`) VALUES (4, 1, 0, '0', '', 'Standard', 'O:5:"liste":16:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:5:"modul";s:1:"0";s:10:"doctype_id";s:4:"1028";s:5:"limit";s:2:"30";s:5:"query";s:0:"";s:10:"orderfield";s:0:"";s:5:"title";s:8:"Standard";s:9:"min_perms";N;s:9:"datatable";s:0:"";s:5:"width";s:4:"100%";s:11:"description";s:0:"";s:5:"cache";N;s:3:"row";a:1:{i:0;O:3:"row":4:{s:5:"title";s:6:"reihe1";s:8:"elements";a:2:{i:0;O:4:"item":6:{s:4:"name";s:9:"master_ip";s:4:"type";s:4:"text";s:9:"maxlength";s:0:"";s:5:"width";s:2:"20";s:4:"nobr";N;s:9:"css_class";s:2:"t2";}i:1;O:4:"item":6:{s:4:"name";s:6:"domain";s:4:"type";s:4:"text";s:9:"maxlength";s:0:"";s:5:"width";s:2:"80";s:4:"nobr";N;s:9:"css_class";s:2:"t2";}}s:11:"edit_button";s:1:"1";s:13:"delete_button";s:1:"1";}}s:5:"perms";s:1:"r";s:4:"icon";s:0:"";}', 1028);
INSERT INTO `listtype` (`listtype_id`, `userid`, `groupid`, `listtype_modul`, `listtype_datatable`, `listtype_title`, `listtype_def`, `listtype_doctype_id`) VALUES (5, 1, 0, '0', '', 'Standard', 'O:5:"liste":16:{s:6:"userid";s:1:"1";s:7:"groupid";s:1:"0";s:5:"modul";s:1:"0";s:10:"doctype_id";s:4:"1016";s:5:"limit";s:2:"30";s:5:"query";s:0:"";s:10:"orderfield";s:7:"dns_soa";s:5:"title";s:8:"Standard";s:9:"min_perms";N;s:9:"datatable";s:0:"";s:5:"width";s:4:"100%";s:11:"description";s:0:"";s:5:"cache";N;s:3:"row";a:1:{i:0;O:3:"row":4:{s:5:"title";s:4:"ewdf";s:8:"elements";a:2:{i:0;O:4:"item":6:{s:4:"name";s:10:"dns_soa_ip";s:4:"type";s:4:"text";s:9:"maxlength";s:0:"";s:5:"width";s:2:"20";s:4:"nobr";N;s:9:"css_class";s:3:"t2b";}i:1;O:4:"item":6:{s:4:"name";s:7:"dns_soa";s:4:"type";s:4:"text";s:9:"maxlength";s:0:"";s:5:"width";s:2:"80";s:4:"nobr";N;s:9:"css_class";s:2:"t2";}}s:11:"edit_button";s:1:"1";s:13:"delete_button";s:1:"1";}}s:5:"perms";s:1:"r";s:4:"icon";s:0:"";}', 1016);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` bigint(21) NOT NULL auto_increment,
  `sessionid` varchar(100) NOT NULL default '',
  `userid` bigint(21) NOT NULL default '0',
  `von` bigint(21) NOT NULL default '0',
  `bis` bigint(21) NOT NULL default '0',
  `status` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`login_id`),
  UNIQUE KEY `login_id_2` (`login_id`),
  KEY `login_id` (`login_id`,`sessionid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `login`
--


-- --------------------------------------------------------

--
-- Table structure for table `multidoc_dep`
--

CREATE TABLE `multidoc_dep` (
  `dep_id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `parent_doc_id` int(10) unsigned NOT NULL default '0',
  `parent_doctype_id` int(10) unsigned NOT NULL default '0',
  `parent_tree_id` int(11) NOT NULL default '0',
  `child_doc_id` int(11) NOT NULL default '0',
  `child_doctype_id` int(11) NOT NULL default '0',
  `child_tree_id` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`dep_id`),
  UNIQUE KEY `dep_id_2` (`dep_id`),
  KEY `dep_id` (`dep_id`,`userid`,`groupid`,`parent_doc_id`,`parent_doctype_id`),
  KEY `tree_id` (`parent_tree_id`,`child_doc_id`,`child_doctype_id`,`child_tree_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `multidoc_dep`
--


-- --------------------------------------------------------

--
-- Table structure for table `multidoc_nodes`
--

CREATE TABLE `multidoc_nodes` (
  `tree_id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `parent` varchar(100) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  `doctype_id` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '1',
  `icon` varchar(255) NOT NULL default '',
  `modul` varchar(255) NOT NULL default '',
  `doc_id` bigint(21) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tree_id`),
  UNIQUE KEY `tree_id_2` (`tree_id`),
  KEY `tree_id` (`tree_id`,`userid`,`groupid`),
  KEY `doc_id` (`doc_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `multidoc_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `sessionid` varchar(200) NOT NULL default '',
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `design` varchar(10) default 'blau',
  `bookmark_order` char(1) NOT NULL default 'j',
  `ordner` varchar(255) NOT NULL default '',
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `bis` bigint(20) unsigned NOT NULL default '0',
  `news` char(1) NOT NULL default '',
  `modul` varchar(100) NOT NULL default '',
  `nav_color` varchar(6) NOT NULL default '',
  `box_color` varchar(6) NOT NULL default '',
  `site` varchar(100) NOT NULL default '',
  `domain` varchar(100) NOT NULL default '',
  `pid` bigint(21) NOT NULL default '0',
  `datas` mediumtext NOT NULL,
  `remote_addr` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `sessionid` (`sessionid`,`userid`),
  KEY `id` (`id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `session`
--


-- --------------------------------------------------------

--
-- Table structure for table `sys_config`
--

CREATE TABLE `sys_config` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1027',
  `kunde_nr_start` int(11) default NULL,
  `anbieter_nr_start` int(11) default NULL,
  `sys_farbe` varchar(255) default NULL,
  `user_prefix` varchar(255) default NULL,
  `rechnung_nr_start` int(11) default '0',
  `faktura_on` char(1) default '1',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `sys_config`
--

INSERT INTO `sys_config` (`doc_id`, `doctype_id`, `kunde_nr_start`, `anbieter_nr_start`, `sys_farbe`, `user_prefix`, `rechnung_nr_start`, `faktura_on`) VALUES (1, 1027, 250, 200, NULL, 'web[WEBID]_', 100, '1');

-- --------------------------------------------------------

--
-- Table structure for table `sys_dep`
--

CREATE TABLE `sys_dep` (
  `dep_id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `parent_doc_id` int(10) unsigned NOT NULL default '0',
  `parent_doctype_id` int(10) unsigned NOT NULL default '0',
  `parent_tree_id` int(11) NOT NULL default '0',
  `child_doc_id` int(11) NOT NULL default '0',
  `child_doctype_id` int(11) NOT NULL default '0',
  `child_tree_id` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`dep_id`),
  UNIQUE KEY `dep_id_2` (`dep_id`),
  KEY `dep_id` (`dep_id`,`userid`,`groupid`,`parent_doc_id`,`parent_doctype_id`),
  KEY `tree_id` (`parent_tree_id`,`child_doc_id`,`child_doctype_id`,`child_tree_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `sys_dep`
--

INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (4, 1, 0, 1, 1023, 15, 1, 1025, 24, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (5, 1, 0, 1, 1023, 15, 2, 1025, 25, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (6, 1, 0, 1, 1023, 15, 3, 1025, 26, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (7, 1, 0, 1, 1023, 15, 4, 1025, 27, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (8, 1, 0, 1, 1023, 15, 5, 1025, 28, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (9, 1, 0, 1, 1023, 15, 6, 1025, 29, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (10, 1, 0, 1, 1023, 15, 7, 1025, 30, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (11, 1, 0, 1, 1023, 15, 8, 1025, 31, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (12, 1, 0, 1, 1023, 15, 9, 1025, 32, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (13, 1, 0, 1, 1023, 15, 10, 1025, 33, 1);
INSERT INTO `sys_dep` (`dep_id`, `userid`, `groupid`, `parent_doc_id`, `parent_doctype_id`, `parent_tree_id`, `child_doc_id`, `child_doctype_id`, `child_tree_id`, `status`) VALUES (14, 1, 0, 1, 1023, 15, 11, 1025, 34, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_modules`
--

CREATE TABLE `sys_modules` (
  `doc_id` bigint(20) unsigned NOT NULL auto_increment,
  `doctype_id` int(10) unsigned NOT NULL default '2',
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `module_title` varchar(255) NOT NULL default '',
  `module_enabled` tinyint(4) NOT NULL default '0',
  `module_type` varchar(255) NOT NULL default '',
  `module_path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id_2` (`doc_id`),
  KEY `doc_id` (`doc_id`,`userid`,`groupid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `sys_modules`
--

INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (9, 2, 0, 0, 'bookmark', 'Bookmark', 1, 'p', 'bookmark');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (11, 2, 0, 0, 'messenger', 'Messenger', 1, 'p', 'messenger');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (13, 2, 0, 0, 'multidoc', 'IPR Manager', 1, 'p', 'multidoc');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (14, 2, 0, 0, 'help', 'Hilfe', 1, 'p', 'help');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (15, 2, 0, 0, 'tools', 'Tools', 1, 'p', 'tools');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (16, 2, 0, 0, 'sys', 'Administration', 1, 'p', 'admin');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (17, 2, 0, 0, 'isp', 'ISP Manager', 1, 'p', 'isp_manager');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (18, 2, 0, 0, 'dns', 'DNS Manager', 1, 'p', 'isp_dns');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (19, 2, 0, 0, 'isp_kunde', 'Web-Manager', 1, 'p', 'isp_kunde');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (20, 2, 0, 0, 'isp_fakt', 'ISP Faktura', 1, 'p', 'isp_fakt');
INSERT INTO `sys_modules` (`doc_id`, `doctype_id`, `userid`, `groupid`, `module_name`, `module_title`, `module_enabled`, `module_type`, `module_path`) VALUES (21, 2, 0, 0, 'isp_file', 'Web-FTP', 1, 'p', 'isp_file');

-- --------------------------------------------------------

--
-- Table structure for table `sys_news`
--

CREATE TABLE `sys_news` (
  `doc_id` bigint(20) unsigned NOT NULL auto_increment,
  `doctype_id` int(10) unsigned NOT NULL default '3',
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `titel` varchar(255) NOT NULL default '',
  `datum` bigint(21) NOT NULL default '0',
  `newstext` text NOT NULL,
  `visible` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id_2` (`doc_id`),
  KEY `doc_id` (`doc_id`,`userid`,`groupid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `sys_news`
--


-- --------------------------------------------------------

--
-- Table structure for table `sys_nodes`
--

CREATE TABLE `sys_nodes` (
  `tree_id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `parent` varchar(100) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  `doctype_id` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '1',
  `icon` varchar(255) NOT NULL default '',
  `modul` varchar(255) NOT NULL default '',
  `doc_id` bigint(21) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tree_id`),
  UNIQUE KEY `tree_id_2` (`tree_id`),
  KEY `tree_id` (`tree_id`,`userid`,`groupid`),
  KEY `doc_id` (`doc_id`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `sys_nodes`
--

INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (2, 1, 1, '', 'a', 2, '1', 'fenster.gif', '', 9, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (4, 1, 1, '', 'a', 2, '1', 'fenster.gif', '', 11, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (1, 1, 0, '', 'a', 1, '1', '', 'sys', 1, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (6, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 13, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (7, 1, 1, '', 'a', 2, '1', 'fenster.gif', '', 14, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (8, 1, 1, '', 'a', 2, '1', 'fenster.gif', '', 15, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (9, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 16, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (10, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 17, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (11, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 18, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (12, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 19, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (13, 1, 0, 'root', 'a', 1010, '1', 'server.gif', '', 1, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (14, 1, 1, 'root', 'i', 1021, '1', 'status.gif', '', 1, 'Server Status');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (15, 1, 0, 'root', 'a', 1023, '1', 'status.gif', '', 1, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (24, 1, 0, '', 'a', 1025, '1', '', '', 1, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (25, 1, 0, '', 'a', 1025, '1', '', '', 2, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (26, 1, 0, '', 'a', 1025, '1', '', '', 3, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (27, 1, 0, '', 'a', 1025, '1', '', '', 4, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (28, 1, 0, '', 'a', 1025, '1', '', '', 5, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (29, 1, 0, '', 'a', 1025, '1', '', '', 6, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (30, 1, 0, '', 'a', 1025, '1', '', '', 7, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (31, 1, 0, '', 'a', 1025, '1', '', '', 8, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (32, 1, 0, '', 'a', 1025, '1', '', '', 9, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (33, 1, 0, '', 'a', 1025, '1', '', '', 10, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (34, 1, 0, '', 'a', 1025, '1', '', '', 11, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (35, 1, 0, '', 'a', 1023, '1', '', '', 2, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (36, 1, 0, '', 'a', 1023, '1', '', '', 3, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (39, 1, 0, '', 'a', 1027, '1', '', '', 0, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (40, 1, 0, '', 'a', 1027, '1', '', '', 0, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (41, 1, 0, '', 'a', 1027, '1', '', '', 2, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (42, 1, 0, '', 'a', 1027, '1', '', '', 3, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (43, 1, 0, 'root', 'a', 1027, '1', '', '', 1, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (44, 1, 0, '', 'a', 1027, '1', '', '', 4, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (38, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 20, '');
INSERT INTO `sys_nodes` (`tree_id`, `userid`, `groupid`, `parent`, `type`, `doctype_id`, `status`, `icon`, `modul`, `doc_id`, `title`) VALUES (45, 1, 0, '', 'a', 2, '1', 'fenster.gif', '', 21, '');

-- --------------------------------------------------------

--
-- Table structure for table `sys_user`
--

CREATE TABLE `sys_user` (
  `doc_id` bigint(20) unsigned NOT NULL auto_increment,
  `doctype_id` int(10) unsigned NOT NULL default '1',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `passwort` varchar(255) default NULL,
  `pwcl` varchar(255) default NULL,
  `design` varchar(255) NOT NULL default 'blau',
  `gueltig` tinyint(4) NOT NULL default '0',
  `language` char(2) NOT NULL default '',
  `mailmax` int(11) NOT NULL default '0',
  `bookmark_order` tinyint(4) NOT NULL default '0',
  `email` varchar(30) NOT NULL default '',
  `news` tinyint(4) NOT NULL default '0',
  `modul` varchar(255) NOT NULL default '',
  `nav_color` varchar(6) NOT NULL default 'E4E4E4',
  `box_color` varchar(6) NOT NULL default 'E4E4E4',
  `site` varchar(50) NOT NULL default 'ISPConfig',
  `domain` varchar(255) NOT NULL default 'ispconfig.de',
  `perms` varchar(255) NOT NULL default '',
  `modules` varchar(255) NOT NULL default '',
  `anrede` varchar(255) NOT NULL default '',
  `vorname` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `strasse` varchar(255) default NULL,
  `plz` varchar(5) default NULL,
  `ort` varchar(255) default NULL,
  `telefon` varchar(255) NOT NULL default '',
  `fax` varchar(255) NOT NULL default '',
  `email_home` varchar(255) NOT NULL default '',
  `alter1` varchar(10) NOT NULL default '',
  `woher` varchar(255) NOT NULL default '',
  `land` varchar(255) NOT NULL default '',
  `passwortRecover` varchar(255) NOT NULL default '',
  `newsletter` smallint(6) default NULL,
  `userid` bigint(21) NOT NULL default '1',
  PRIMARY KEY  (`doc_id`),
  UNIQUE KEY `doc_id_2` (`doc_id`),
  KEY `doc_id` (`doc_id`,`groupid`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `sys_user`
--

INSERT INTO `sys_user` (`doc_id`, `doctype_id`, `groupid`, `username`, `passwort`, `pwcl`, `design`, `gueltig`, `language`, `mailmax`, `bookmark_order`, `email`, `news`, `modul`, `nav_color`, `box_color`, `site`, `domain`, `perms`, `modules`, `anrede`, `vorname`, `name`, `strasse`, `plz`, `ort`, `telefon`, `fax`, `email_home`, `alter1`, `woher`, `land`, `passwortRecover`, `newsletter`, `userid`) VALUES (1, 1, 0, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 'blau', 1, 'en', 5120000, 1, 'admin', 1, 'isp', '123456', 'E4E4E4', 'ISPConfig', 'ispconfig.de', 'rwa', 'sys,isp,dns,isp_file,isp_fakt,tools,help', 'Herr', '', '', '', '', '', '', '', '', '', '', 'Deutschland', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `usergroupid` bigint(21) NOT NULL auto_increment,
  `groupid` bigint(21) NOT NULL default '0',
  `userid` bigint(21) NOT NULL default '0',
  `perms` varchar(4) NOT NULL default '',
  `userstatus` tinyint(4) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `online` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`usergroupid`),
  UNIQUE KEY `usergroupid_2` (`usergroupid`),
  KEY `usergroupid` (`usergroupid`,`groupid`,`userid`,`perms`),
  KEY `online` (`online`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`usergroupid`, `groupid`, `userid`, `perms`, `userstatus`, `username`, `online`) VALUES (1, 1, 1, 'rwa', 1, 'admin', 0);