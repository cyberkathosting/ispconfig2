<?php
/*
Copyright (c) 2007, projektfarm Gmbh, Till Brehm, Falko Timme
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors
      may be used to endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

set_time_limit(0);
require_once('/home/admispconfig/ispconfig/lib/config.inc.php');

/********************************************************************************************
	The purpose of this script is to prive a easy to use commandline backup
	solution for ISPConfig
********************************************************************************************/

/*
 Backup the ISPConfig directories /home/admispconfig, 
 /root/ispconfig and the ISPConfig mysql database
*/

$conf["backup_ispconfig"] = 'yes';

/*
 Clean the ISPConfig temp directory (/home/admispconfig/ispconfig/temp/ before making the backup.
*/

$conf["backup_clean_ispconfig_temp"] = 'yes';

/*
  Backup the passwords of the ISPConfig users from /etc/passwd
*/

$conf["backup_passwords"] = 'yes';

/*
  Backup the website data
*/

$conf["backup_websites"] = 'yes';

/*
  Backup the mysql databases as sql dumps
  
  Possible values are:
  
  no = Do not backup databasesa
  all = Backup all mysql databases
  web = Backup only the databases of the websites
  
*/

$conf["backup_databases"] = 'all';

/*
	Backup script settings
*/

$conf["backup_temp_dir"] = '/tmp'; // Temp directory which is sued to store data during backup
$conf["backup_mysqldump"] = '/usr/bin/mysqldump'; // path to the mysqldump binary
$conf["backup_tar"] = '/bin/tar'; // Path to the tar binary
$conf["backup_mv"] = '/bin/mv'; // Path to the mv binary
$conf["backup_tar_compression_method"] = 'gz'; // gz or bz2
$conf["backup_notification_email"] = 'root@localhost'; // Sent notifications to


// Connect to the ISPConfig database
$link = mysql_connect($go_info["server"]["db_host"], $go_info["server"]["db_user"], $go_info["server"]["db_password"]);
if (!$link) {
    die('Connect to database failed: ' . mysql_error());
}

// Use the ISPConfig database
$db_selected = mysql_select_db($go_info["server"]["db_name"], $link);
if (!$db_selected) {
    die ('Select of database failed : ' . mysql_error());
}

// Get the server configuration from the ISPConfig database
$result = mysql_query('SELECT * FROM isp_server LIMIT 0,1');

if (!$result) {
    echo "Unable to fetch server configuration from database: " . mysql_error();
    exit;
}
$server = mysql_fetch_assoc($result);

// Creating a temporary directory










?>