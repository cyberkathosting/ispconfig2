<?
/*
Copyright (c) 2005, projektfarm Gmbh, Till Brehm, Falko Timme
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
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");

	$go_api->uses("isp_web,isp_webftp");

	$web_id = $HTTP_POST_VARS["web_id"];
	$ed = $HTTP_POST_VARS["ed"];
	$username = $HTTP_POST_VARS["username"];

    $file = $go_api->isp_webftp->webftp_check_params($ed);
	
	//if(substr($file,0,1) == "/") $file = substr($file,1);
	
	$htaccess = array();
	$users = array();
	
	// Lese .htpasswd Datei
	if($go_api->isp_webftp->webftp_file_changed($file."/.htpasswd") != -1) {
		$tmpfname = tempnam($go_info["server"]["temp_dir"], "hta_");
		if(!$go_api->isp_webftp->webftp_get($tmpfname,$file."/.htpasswd", FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to open")." $file/.htpasswd");
		if(!$htpasswd = file($tmpfname)) $go_api->errorMessage($go_api->lng("Failed to open")." ".$tmpfname);
		@unlink($tmpfname);
	}
	
	// Schreibe Datei in Array $users
	if(is_array($htpasswd)) {
		foreach($htpasswd as $line) {
			list($usr,$pw) = explode(":",$line);
			$users[$usr] = $pw;
		}
	}
	
	// Erstelle oder Update User + Passwort
	$usr = $HTTP_POST_VARS["username"];
	$users[$usr] = crypt($HTTP_POST_VARS["passwort"],substr($HTTP_POST_VARS["passwort"],0,2));
	
	// Schreibe Datei zurück
	$tmpfname = tempnam($go_info["server"]["temp_dir"], "hta_");
	$fp = @fopen($tmpfname,"w");
	foreach($users as $usr => $pw) {
  	$usr = trim($usr);
    $pw = trim($pw);
		if($usr != '') fwrite($fp,$usr.':'.$pw."\n");
	}
	fclose($fp);
	if(!$go_api->isp_webftp->webftp_put($tmpfname,$file."/.htpasswd", FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to write")." $file/.htpasswd");
	@unlink($tmpfname);
	
	
	// Erstelle .htaccess Datei, falls nicht vorhanden
	if($go_api->isp_webftp->webftp_file_changed($file."/.htaccess") == -1) {
		$tmpfname = tempnam($go_info["server"]["temp_dir"], "hta_");
		
		// Hole document root
    	if(!$row = $go_api->db->queryOneRecord("SELECT * from isp_server")) $go_api->errorMessage($go_api->lng("Konnte server_path_httpd_root nicht ermitteln."));
    	$dir_root = $row["server_path_httpd_root"]."/web$web_id";
		
		$fp = fopen($tmpfname,"w");
		fwrite($fp,"AuthType Basic\nAuthName \"Members Only\"\nAuthUserFile ".$dir_root.$file."/.htpasswd\n<limit GET PUT POST>\nrequire valid-user\n</limit>");
		fclose($fp);
		if(!$go_api->isp_webftp->webftp_put($tmpfname,$file."/.htaccess", FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to write")." $file/.htaccess");
		unlink($tmpfname);
	}
	
	header("Location: htaccess_list.php?s=$s&ed=$ed&web_id=$web_id");

?>