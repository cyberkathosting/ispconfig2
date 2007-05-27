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

// name of the backup file to be created
$backup_file_name = "backup.tar.gz";

// Temporary directory to store the backup file
$backup_tmp = "/home/backup/";

// Directories to backup, separated by space
$backup_data = "/var/www /var/lib/mysql /etc";

// FTP login data
$ftp_server = "";
$ftp_user = "";
$ftp_password = "";
$backup_dest_file = "backup.tar.gz";

// Keep one copy of yesterdays backup on the server
$keep_yesterday_backup = 1;


$backup_file = $backup_tmp."/".$backup_file_name;

if(!is_dir($backup_tmp)) exec("mkdir -p $backup_tmp");

// If a backup shuld be kept
if($keep_yesterday_backup == 1) {
	exec("rm -f ".$backup_tmp."backup_yesterday.tar.gz");
	if(is_file($backup_tmp."backup_yesterday.tar.gz")) {
		exec("mv ".$backup_file." ".$backup_tmp."backup_yesterday.tar.gz");
	}
	exec("chmod 700 ".$backup_tmp."backup_yesterday.tar.gz");
}

// Create the backup
$umask_orig = umask();
umask("0077");
exec("tar --exclude=$backup_file -czf $backup_file $backup_data 2>/dev/null");
umask($umask_orig);

// Store backup on FTP server
$conn_id = ftp_connect($ftp_server) or die('Unable to connect to FTP server.');
$login_result = ftp_login ($conn_id, $ftp_user, $ftp_password) or die('Unable to login to FTP server.');
$upload = ftp_put ($conn_id, $backup_dest_file, $backup_file, FTP_BINARY) or die('Unable to upload file to FTP server.');
ftp_close($conn_id);



?>