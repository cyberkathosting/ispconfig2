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

include("../../../../lib/config.inc.php");
$set_header = 0;
include("../../../../lib/session.inc.php");

if($go_info["server"]["mode"] == "demo") $go_api->errorMessage($go_api->lng("Backup-Funktionen im Demo-Modus nicht verfügbar."));

set_time_limit(1800);// 30 Minuten
//ignore_user_abort(1);

// importiere Variablen
$transfer = addslashes($HTTP_POST_VARS["transfer"]);

$ftp_server = addslashes($HTTP_POST_VARS["ftp_server"]);
$ftp_dir = addslashes($HTTP_POST_VARS["ftp_dir"]);
$ftp_user = addslashes($HTTP_POST_VARS["ftp_user"]);
$ftp_passwort = addslashes($HTTP_POST_VARS["ftp_passwort"]);

$daten_web = addslashes($HTTP_POST_VARS["daten_web"]);
$daten_user = addslashes($HTTP_POST_VARS["daten_user"]);
$daten_ftp = addslashes($HTTP_POST_VARS["daten_ftp"]);
$daten_mysql = addslashes($HTTP_POST_VARS["daten_mysql"]);

$webs = $HTTP_POST_VARS["webs"];
if(!is_array($webs)) $go_api->msg($go_api->lng("Es wurde kein Web ausgewählt."),$go_api->lng("Backup Status"));

// Überprüfe Variablen
if(!preg_match("/^[a-zA-Z0-9\-\.]{0,255}$/",$ftp_server)) $go_api->errorMessage($go_api->lng("Der Name des FTP-Servers <br>enthält ungültige Zeichen."));
if(strlen($ftp_user) < 1 and $transfer == 'ftp') $go_api->errorMessage($go_api->lng("Sie haben keinen FTP Benutzernamen angegeben."));

// Erstelle Namen für Backup Datei
$backup_file_name = "backup_".date("Y_m_d",time()).".zip";

// bestimme Web-Pfad
$server = $go_api->db->queryOneRecord("SELECT * from isp_server");
$httpd_root = $server["server_path_httpd_root"];
unset($server);

// erstelle temp verzeichnis
$tmp_dir = $go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].md5(uniqid (""));
mkdir( $tmp_dir, 0700) or $go_api->errorMessage($go_api->lng("tmp_dir_error"));

$zip = $go_info["tools"]["zip"];
$backup_txt = '';

function make_backup($web_id) {
        global $daten_web, $daten_user, $daten_ftp,$go_api,$go_info,$httpd_root,$tmp_dir,$zip,$backup_txt,$daten_mysql,$daten_log;

        $web_id = intval($web_id);
        $backup_txt .= $web_id .',';

        // Hole Web
        $web = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_web where isp_nodes.doc_id = '$web_id' and isp_nodes.doctype_id = 1013 and isp_nodes.doc_id = isp_isp_web.doc_id");

        // überprüfe Rechte
        if($web["userid"] = $go_info["user"]["userid"] or $go_api->groups->in_group($go_info["user"]["userid"],$web["groupid"])) {

                // erstelle ewb tar.gz
                if($daten_web == 1) {
                        $web_pfad = $httpd_root ."/web".$web_id."/web";
                        exec("cd $web_pfad; $zip -r $tmp_dir/web".$web_id."_web.zip *");
            //echo "cd $web_pfad; $zip -r $tmp_dir/web".$web_id."_web.zip *";
                        $backup_txt .= "web,";
                }
                // erstelle user tar.gz
                if($daten_user == 1) {
                        $user_pfad = $httpd_root."/web".$web_id."/user";
                        exec("cd $user_pfad; $zip -r  $tmp_dir/web".$web_id."_user.zip *");
                        $backup_txt .= "user,";
                }
                // erstelle log tar.gz
                if($daten_log == 1) {
                        $log_pfad = $httpd_root."/web".$web_id."/log";
                        exec("cd $log_pfad; $zip -r  $tmp_dir/web".$web_id."_log.zip *");
                        $backup_txt .= "log,";
                }
                // erstelle mySQL tar.gz
                if($daten_mysql == 1) {
                        //Wenn Web Datenbank hat
                        if($web["web_mysql"] == 1) {
                          if($datenbanken = $go_api->db->queryAllRecords("SELECT datenbankname FROM isp_isp_datenbank WHERE web_id = $web_id")){
                            foreach($datenbanken as $datenbank){
                                if($go_info["server"]["db_password"] != ""){
                                      exec("mysqldump -u ".$go_info["server"]["db_user"]." -p".$go_info["server"]["db_password"]." --opt ".$datenbank['datenbankname']." > $tmp_dir/".$datenbank['datenbankname'].".sql");
                                } else {
                                      exec("mysqldump -u ".$go_info["server"]["db_user"]." --opt ".$datenbank['datenbankname']." > $tmp_dir/".$datenbank['datenbankname'].".sql");
                                }
                            }
                          }
                        }
                        $backup_txt .= "db,";
                }
        }
        $backup_txt = substr($backup_txt,0,-1);
        $backup_txt .= "\r\n";
}


// erstelle Web-Backups
foreach($webs as $web_id => $val) {
        if($val == 1) make_backup($web_id);
}

// Schreibe backup.dat Inhaltsbeschreibung
$backup_desc_file = fopen ("$tmp_dir/backup.dat", "w");
fwrite($backup_desc_file,$backup_txt,strlen($backup_txt));
fclose($backup_desc_file);

// erstelle ein einziges zip aus Einzelwebs
$tgz_part1 = md5(uniqid ("")).".zip";
$tgz_name = $go_info["server"]["temp_dir"]."/".$tgz_part1;
exec("$zip -j $tgz_name $tmp_dir/*");
//die("$zip -j $tgz_name $tmp_dir/*");

if($transfer == "download") {

        // setze Header
        header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$backup_file_name."\";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($tgz_name));

        // gebe Daten aus
        echo file_get_contents($tgz_name);

        // lösche temp Verzeichnis
        if($tmp_dir != "" and stristr($tmp_dir,"/home/admispconfig/ispconfig/temp") and !stristr($tmp_dir,"../")) exec("rm -rf $tmp_dir");

        // lösche Temp Datei
        @unlink($tgz_name);

} elseif($transfer == "ftp") {

        // Herstellen der Basis-Verbindung
        $conn_id = @ftp_connect("$ftp_server");

        // Einloggen mit Benutzername und Kennwort
        $login_result = @ftp_login($conn_id, "$ftp_user", "$ftp_passwort");

        $msg = '';
        // Verbindung überprüfen
        if ((!$conn_id) || (!$login_result)) {
        $msg .= $go_api->lng("Ftp-Verbindung nicht hergestellt!")."<br>\r\n";
        $msg .= $go_api->lng("Verbindung mit:")." $ftp_server <br>".$go_api->lng("als Benutzer:")." $ftp_user ".$go_api->lng("nicht möglich.")."<br>\r\n";
        $go_api->errorMessage($msg);
    } else {
        $msg .= $go_api->lng("Verbindung mit:")." $ftp_server <br>".$go_api->lng("als Benutzer:")." $ftp_user <br>\r\n";
    }

        // wechsle Verzeichnis
        if(!@ftp_chdir($conn_id, $ftp_dir)) {
                $msg .= $go_api->lng("Konnte nicht in Verzeichnis:")." $ftp_dir ".$go_api->lng("wechseln.")."<br>\r\n";
                ftp_quit($conn_id);
                $go_api->errorMessage($msg);
        } else {
                $msg .= $go_api->lng("Wechsel in Verzeichnis:")." $ftp_dir <br>\r\n";
        }

        // Upload der Datei
        $upload = @ftp_put($conn_id, "$backup_file_name", "$tgz_name", FTP_BINARY);

        // Upload-Status überprüfen
        if (!$upload) {
        $msg .= $go_api->lng("Ftp upload war fehlerhaft!")." <br>\r\n";
    } else {
        $msg .= $go_api->lng("Upload erfolgreich.")."<br>\r\n";
    }

        // Schließen des FTP-Streams
        ftp_quit($conn_id);

        // lösche temp Verzeichnis
        if($tmp_dir != "" and stristr($tmp_dir,"/home/admispconfig/ispconfig/temp") and !stristr($tmp_dir,"../")) exec("rm -rf $tmp_dir");
        // lösche Temp Datei
        @unlink($tgz_name);
        $go_api->msg($msg,$go_api->lng("FTP-Upload Status"));
} else {
        $go_api->errorMessage($go_api->lng("Backup-Methode nicht unterstützt.")." <br>\r\n");
}

?>