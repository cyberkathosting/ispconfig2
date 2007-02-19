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

	$web_id = $_REQUEST["web_id"];
	$ed 	= $_REQUEST["ed"];
	
	$ed = $go_api->isp_webftp->webftp_check_params($ed);
	
	// Datei kopieren, falls Sie verschoben oder umbenannt wurde
	$ed_new = $HTTP_POST_VARS["ordner"].":".$HTTP_POST_VARS["filename"];
	// ed_new aufschlüsseln
	$edstr_new = split(":",$ed_new);
	$filename_new = $edstr_new[count($edstr_new)-1];
	$file_new = str_replace(":","/",$ed_new);
	$file_new = str_replace("root",$dir_root,$file_new);
	if(stristr($file_new,"..")) $go_api->errorMessage($go_api->lng("Der Pfad enthält ungültige Zeichen."));
	
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
		
    if($ed !=  $ed_new) {
        if($go_info["server"]["os"] != "win32") $file_new = escapeshellcmd($file_new);

		if(!$go_api->isp_webftp->webftp_mv($ed,$file_new)) $go_api->errorMessage($go_api->lng("Das Verschieben der Datei ist fehlgeschlagen."));
		if(!$go_api->isp_webftp->webftp_chmod($chmod_str,$file_new)) $go_api->errorMessage($go_api->lng("chmod failed."));
						
		// Cache löschen, besser später mal eine cache modifikation schreiben
		$go_info["webftp"]["dir_tree"] = '';
		$go_api->session->save();
						
		$ed = $ed_new;
        $file = $file_new;
    }

	header("Location: ordner.php?s=$s&ordner=$ordner&web_id=$web_id");

?>