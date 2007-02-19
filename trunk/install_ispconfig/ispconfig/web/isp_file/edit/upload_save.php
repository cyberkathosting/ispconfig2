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
	
	$go_api->uses("isp_web,isp_webftp");

    $file_new = $go_api->isp_webftp->webftp_check_params($HTTP_POST_VARS["ordner"]);
	// Datei kopieren, falls Sie verschoben oder umbenannt wurde
	if(stristr($file_new,"..")) $go_api->errorMessage($go_api->lng("Der Pfad enthält ungültige Zeichen."));
	//$dir_root = $row["server_path_httpd_root"]."/web$web_id";
	
	/////////////////////////////////////////////////////
	// Save File Upload
	/////////////////////////////////////////////////////
	
	$updir = $file_new; //absolute path to where files are uploaded, no trailing slash
	$sizelim = "yes"; //do you want size limitations yes or no
	$size = "250000"; //if you want size limited how many bytes
	$certtype = "no"; //do you want certain type of file, no recommended
	$type = ""; //what type of file would you like
  
  	$file = $HTTP_POST_FILES["file"];

	//error if no file is selected
	if ($file['name'] == "") {
		$go_api->errorMessage($go_api->lng("Es wurde keine Datei zum Upload ausgewählt."));
	}
	
	//error if file exists
	if (file_exists("$updir/".$file['name'])) {
		$go_api->errorMessage($go_api->lng("Die Datei existiert bereits in diesem Verzeichnis."));
	}
	
	//error if file is to big
	if ($sizelim == "yes") {
		if ($file['size'] > $size) {
			//die("The file you are trying to upload is too big.");
		}
	} 
	//error if file isn't certain type
	if ($certtype == "yes") {
		if ($type != $file['type']) {
			$go_api->errorMessage("The file you are trying to upload is wrong type");
		}
	}
	
	// Datei kopieren
	if (is_uploaded_file($file['tmp_name'])) { 
   		//if(!@copy($file['tmp_name'], "$updir/".$file['name'])) $go_api->errorMessage($go_api->lng("Konnte Datei nicht kopieren: ".$file['tmp_name']." => "."$updir/".$file['name']));
		if(!$go_api->isp_webftp->webftp_put($file['tmp_name'],$updir."/".$file['name'], FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to write")." $file/.htaccess");
	} else { 
   		$go_api->errorMessage($go_api->lng("Möglicher Dateiupload Angriff.")); 
	}
	
	// Rechte der Date ändern
	$user = 0;
	$group = 0;
	$other = 0;
	
	if($HTTP_POST_VARS["perm_user_read"] == 1) $user += 4;
	if($HTTP_POST_VARS["perm_user_write"] == 1) $user += 2;
	if($HTTP_POST_VARS["perm_user_exec"] == 1) $user += 1;
	
	if($HTTP_POST_VARS["perm_group_read"] == 1) $group += 4;
	if($HTTP_POST_VARS["perm_group_write"] == 1) $group += 2;
	if($HTTP_POST_VARS["perm_group_exec"] == 1) $group += 1;
	
	if($HTTP_POST_VARS["perm_other_read"] == 1) $other += 4;
	if($HTTP_POST_VARS["perm_other_write"] == 1) $other += 2;
	if($HTTP_POST_VARS["perm_other_exec"] == 1) $other += 1;
	
	$chmod_str = "0$user$group$other";
	if(!$go_api->isp_webftp->webftp_chmod($chmod_str,$updir."/".$file['name'])) $go_api->errorMessage($go_api->lng("chmod failed."));
	
	header("Location: ordner.php?s=$s&ordner=$ordner&web_id=$web_id");

?>