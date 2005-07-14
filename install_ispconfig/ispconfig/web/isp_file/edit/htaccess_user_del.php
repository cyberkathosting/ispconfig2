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

        $web_id = $HTTP_GET_VARS["web_id"];
        $ed = $HTTP_GET_VARS["ed"];
        $username = $HTTP_GET_VARS["username"];

    $file = $go_api->isp_webftp->webftp_check_params($ed);


        $htaccess = array();
        $users = array();

        // Lese .htpasswd Datei
        if($go_api->isp_webftp->webftp_file_changed($file."/.htpasswd") != -1) {
                $tmpfname = tempnam($go_info["server"]["temp_dir"], "hta_");
                if(!$go_api->isp_webftp->webftp_get($tmpfname,$file."/.htpasswd", FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to open")." $file/.htpasswd");
                $htpasswd = file($tmpfname);
                //print_r($htpasswd);
                //die();
                //if(empty($htpasswd)) $go_api->errorMessage($go_api->lng("Failed to open ").$tmpfname);
                if(!$htpasswd = file($tmpfname)) $go_api->errorMessage($go_api->lng("Failed to open ").$tmpfname);
                @unlink($tmpfname);
        }

        // Schreibe Datei in Array $users
        if(is_array($htpasswd)) {
                foreach($htpasswd as $line) {
                        list($usr,$pw) = explode(":",$line);
                        $users[$usr] = $pw;
                }
        }
        //print_r($users);

        // Schreibe Datei zurück
        $tmpfname = tempnam($go_info["server"]["temp_dir"], "hta_");
        $fp = @fopen($tmpfname,"w");
        foreach($users as $usr => $pw) {
                  $usr = trim($usr);
            $pw = trim($pw);
                if($usr != '' and $usr != trim($HTTP_GET_VARS["username"])) fwrite($fp,$usr.':'.$pw."\n");
        }
        fclose($fp);
        if(filesize($tmpfname) > 0){
          if(!$go_api->isp_webftp->webftp_put($tmpfname,$file."/.htpasswd", FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to write")." $file/.htpasswd");
          $weiter_passwortschutz = 1;
        } else {
          // Lösche .htpasswd und .htaccess Datei
          if(!$go_api->isp_webftp->webftp_delete($file."/.htpasswd")) $go_api->errorMessage($file."/.htpasswd ".$go_api->lng("konnte nicht gelöscht werden."));
          if(!$go_api->isp_webftp->webftp_delete($file."/.htaccess")) $go_api->errorMessage($file."/.htaccess ".$go_api->lng("konnte nicht gelöscht werden."));
          $weiter_passwortschutz = 0;
        }
        @unlink($tmpfname);

        if($weiter_passwortschutz){
          header("Location: htaccess_list.php?s=$s&ed=$ed&web_id=$web_id");
        } else {
          header("Location: ordner_edit.php?s=$s&ed=$ed&web_id=$web_id");
        }
?>