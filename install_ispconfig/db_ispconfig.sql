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

INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (3, 1, 1, 'sys', 'news', 'News', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1, 1, 1, 'sys', 'user', 'User Manager', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1000, 1, 0, '', 'dummy', 'dummy', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (10, 1, 0, 'help', 'documents', 'Online Hilfe', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (2, 1, 0, 'sys', 'modules', 'Modul Manager', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1010, 1, 1, 'sys', 'server', 'ISP Server', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1011, 1, 1, 'isp', 'isp_actions', 'ISP Aktionen', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1012, 1, 0, 'isp', 'isp_kunde', 'ISP Kunde', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1013, 1, 0, 'isp', 'isp_web', 'ISP Web', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1014, 1, 0, 'isp', 'isp_user', 'ISP User', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1015, 1, 1, 'isp', 'isp_domain', 'ISP Domain', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1016, 1, 0, 'dns', 'isp_dns', 'DNS Eintrag', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1019, 1, 0, 'dns', 'cname', 'CNAME Record', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1018, 1, 0, 'dns', 'a', 'A Record', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1020, 1, 0, 'dns', 'mx', 'MX Record', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1021, 1, 1, 'sys', 'serverstatus', 'ISP Server Status', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1022, 1, 1, 'isp', 'isp_reseller', 'ISP Anbieter', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1023, 1, 1, 'sys', 'dienste', 'ISP Dienste', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1024, 1, 1, 'sys', 'monitor', 'ISP Überwachung', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1025, 1, 1, 'sys', 'firewall', 'ISP Firewall', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1026, 1, 1, 'isp_fakt', 'artikel', 'Artikel', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1027, 1, 1, 'sys', 'config', 'System Config', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1028, 1, 0, 'dns', 'secondary', 'Slave Zone', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1029, 1, 0, 'isp', 'isp_datenbank', 'ISP Datenbank', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1030, 1, 0, 'isp', 'isp_web_template', 'ISP Web-Vorlage', '', 1);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1031, 1, 0, 'dns', 'spf', 'SPF Record', '', 0);
INSERT INTO `doctype` (`doctype_id`, `userid`, `groupid`, `doctype_modul`, `doctype_name`, `doctype_title`, `doctype_def`, `doctype_tree`) VALUES (1033, 1, 0, 'isp', 'isp_list', 'ISP List', '', 0);

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

INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (2, 1026, 'de0001', 'Domain', 'Domain .tld', '0', 'Stück', '0', 1, '', '', '16%', NULL, NULL, NULL);
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (3, 1026, 'wp0001', 'Web', 'Web Package 1', '12', 'Stück', '0', 1, 'Demo Web-Package', '', '16%', NULL, NULL, NULL);
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (4, 1026, 'SV0001', 'Dienstleistung', 'Service Vertrag Basis', '0', 'Stück', '0', 1, '', '', '16%', NULL, NULL, NULL);
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (5, 1026, 'TF17782', 'Traffic', 'Traffic 5 GB', '1', 'GB', '0', 5, '', '', '16%', 'GB', 1, '0');
INSERT INTO isp_fakt_artikel (doc_id, doctype_id, artikelnummer, artikeltyp, artikelbezeichnung, verrechnung, artikeleinheit, artikelpreis, abpackung, beschreibung, artikelgroup, steuersatz, weitere_artikeleinheit, weitere_abpackung, weitere_artikelpreis) VALUES (6, 1026, 'EM0001', 'Email', 'Email Adresse', '1', 'Stück', '0', 1, 'Demo Email Acoount', '', '16%', NULL, NULL, NULL);
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
  `dienst_port` varchar(10) default NULL,
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
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (9, 1025, 'SSL (www)', '443', 'ja', 'tcp', NULL);
INSERT INTO `isp_firewall` (`doc_id`, `doctype_id`, `dienst_name`, `dienst_port`, `dienst_aktiv`, `dienst_typ`, `status`) VALUES (10, 1025, 'Webmin', '10000', 'ja', 'tcp', NULL);

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
-- Table structure for table `isp_isp_list`
-- 

CREATE TABLE `isp_isp_list` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `doctype_id` bigint(20) NOT NULL default '1033',
  `list_alias` varchar(255) default NULL,
  `list_name` varchar(255) default NULL,
  `listadmin_addr` varchar(255) default NULL,
  `list_admin_passwd` varchar(255) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `isp_isp_list`
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
  `limit_list` char(1) default NULL,
  `limit_listlimit` varchar(255) default '-1',
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
  `client_salutatory_email_message` text NOT NULL default '',
  `standard_index` text NOT NULL default '',
  `user_standard_index` text NOT NULL default '',
  `traffic_suspension_sender_email` varchar(255) default '',
  `traffic_suspension_sender_name` varchar(255) default '',
  `traffic_suspension_email_bcc` varchar(255) default '',
  `traffic_suspension_email_subject` varchar(255) default '',
  `traffic_suspension_email_message` text NOT NULL default '',
  `traffic_notification_sender_email` varchar(255) default '',
  `traffic_notification_sender_name` varchar(255) default '',
  `traffic_notification_email_bcc` varchar(255) default '',
  `traffic_notification_email_subject` varchar(255) default '',
  `traffic_notification_email_message` text NOT NULL default '',
  `limit_traffic` varchar(255) default '-1',
  `limit_traffic_ueberschreitung` varchar(255) default '1',
  `limit_cgi_mod_perl` smallint(6) default NULL,
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
  `user_emailweiterleitung_no_scan` char(1) default NULL,
  `user_ftp` char(1) default NULL,
  `user_emaildomain` text NOT NULL,
  `spam_whitelist` text,
  `spam_blacklist` text,
  `use_uribl` char(1) default '0',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) ENGINE=MyISAM;

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
  `web_list` char(1) default NULL,
  `web_listlimit` varchar(255) default NULL,
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
  `web_mysql_quota` varchar(255) default NULL,
  `web_mysql_quota_exceeded` enum('N','Y') NOT NULL default 'N',
  `web_mysql_quota_used_fract` float NOT NULL default '0',
  `web_mailquota` varchar(10) default NULL,
  `web_cgi_mod_perl` varchar(255) default NULL,
  `web_stats` varchar(255) default 'none',
  PRIMARY KEY  (`doc_id`),
  KEY `doctype_id` (`doctype_id`)
) ENGINE=MyISAM;

--
-- Dumping data for table `isp_isp_web`
--

--
-- Table structure for table `isp_isp_web_package`
--


CREATE TABLE `isp_isp_web_package` (
  `doc_id` bigint(20) NOT NULL auto_increment,
  `mysqlhost` varchar(255) default NULL,
  `mysqluser` varchar(255) default NULL,
  `mysqlpw` varchar(255) default NULL,
  `mysqldb` varchar(255) default NULL,
  `installpw` varchar(255) default NULL,
  `adminpw` varchar(255) default NULL,
  `web_id` varchar(255) default NULL,
  `pending` int(1) default NULL,
  `result` varchar(255) default NULL,
  PRIMARY KEY  (`doc_id`),
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
  `web_mysql_quota` varchar(255) default NULL,
  `web_list` char(1) default NULL,
  `web_listlimit` varchar(255) default NULL,
  `web_ssl` char(1) default NULL,
  `web_anonftp` char(1) default NULL,
  `web_anonftplimit` varchar(255) default NULL,
  `web_wap` char(1) default NULL,
  `web_individual_error_pages` char(1) default NULL,
  `web_httpd_include` text,
  `web_mailuser_login` char(1) default NULL,
  `web_traffic` varchar(255) default NULL,
  `web_traffic_ueberschreitung` varchar(255) default NULL,
  `web_mailquota` varchar(255) default NULL,
  `web_cgi_mod_perl` varchar(255) default NULL,
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
  `cyrus_admin` varchar(255) default NULL,
  `cyrus_password` varchar(255) default NULL,
  `server_httpd_mod_perl` smallint(6) default NULL,
  `server_mail_check_mx` char(1) default NULL,
  `server_mailman_domain` varchar(255) default NULL,
  `typo3_script_repository` varchar(255) default NULL,
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
  `datas` text NOT NULL,
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

CREATE TABLE remote_session (
  sid varchar(255) NOT NULL default '',
  ruserid int(11) NOT NULL default '0',
  data text NOT NULL,
  tstamp int(11) unsigned NOT NULL default '0',
  KEY rsid (sid)
) TYPE=MyISAM;

CREATE TABLE remote_user (
  ruserid int(11) NOT NULL auto_increment,
  username varchar(255) NOT NULL default '',
  passwort varchar(255) NOT NULL default '',
  ip varchar(100) NOT NULL default '',
  dns_query tinyint(4) NOT NULL default '0',
  dns_insert tinyint(4) NOT NULL default '0',
  dns_update tinyint(4) NOT NULL default '0',
  dns_delete tinyint(4) NOT NULL default '0',
  slave_query tinyint(4) NOT NULL default '0',
  slave_insert tinyint(4) NOT NULL default '0',
  slave_update tinyint(4) NOT NULL default '0',
  slave_delete tinyint(4) NOT NULL default '0',
  reseller_query tinyint(4) NOT NULL default '0',
  reseller_insert tinyint(4) NOT NULL default '0',
  reseller_update tinyint(4) NOT NULL default '0',
  reseller_delete tinyint(4) NOT NULL default '0',
  kunde_query tinyint(4) NOT NULL default '0',
  kunde_insert tinyint(4) NOT NULL default '0',
  kunde_update tinyint(4) NOT NULL default '0',
  kunde_delete tinyint(4) NOT NULL default '0',
  web_query tinyint(4) NOT NULL default '0',
  web_insert tinyint(4) NOT NULL default '0',
  web_update tinyint(4) NOT NULL default '0',
  web_delete tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (ruserid)
) TYPE=MyISAM;