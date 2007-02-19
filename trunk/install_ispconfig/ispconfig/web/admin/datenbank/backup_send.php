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
$set_header = 0;
include("../../../lib/session.inc.php");

if($go_info["server"]["mode"] == 'demo') $go_api->errorMessage("Aktion im Demo-Modus nicht möglich.");

$go_api->auth->check_admin(0);
//$tables = $go_api->db->getTables($go_info["server"]["db_name"]);
set_time_limit(1800);// 30 Minuten

// Überprüfe Variablen
if(!preg_match("/^[a-zA-Z0-9\-\.]{0,255}$/",$ftp_server)) $go_api->errorMessage($go_api->lng("Der Name des FTP-Servers <br>enthält ungültige Zeichen."));
if(strlen($ftp_user) < 1 and $transfer == 'ftp') $go_api->errorMessage($go_api->lng("Sie haben keinen FTP Benutzernamen angegeben."));

// Erstelle Namen für Backup Datei
$backup_file_name = "sysdb_backup_".date("Y_m_d",time()).".zip";

// erstelle temp verzeichnis
$tmp_dir = $go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].md5(uniqid (""));
mkdir( $tmp_dir, 0700) or $go_api->errorMessage($go_api->lng("tmp_dir_error"));

$zip = $go_info["tools"]["zip"];

// erstelle Backup
if($go_info["server"]["os"] == "linux") {

    ////////////////////////////////
    //  Linux Version
    ////////////////////////////////

    // Slashes für mySQL ersetzen
    //$backup_dir = str_replace("\\","/",$tmp_dir);

    //foreach($tables as $table) {

    //$go_api->db->query("LOCK TABLES $table READ");
    //$go_api->db->query("FLUSH TABLES");
    //$response = $go_api->db->queryOneRecord("BACKUP TABLE $table TO '".$go_info["server"]["backup_dir"]."'");
        if($go_info["server"]["db_password"] != ""){
              exec("mysqldump -u ".$go_info["server"]["db_user"]." -p".$go_info["server"]["db_password"]." --opt ".$go_info["server"]["db_name"]." > $tmp_dir/sysdb.sql");
    } else {
              exec("mysqldump -u ".$go_info["server"]["db_user"]." --opt ".$go_info["server"]["db_name"]." > $tmp_dir/sysdb.sql");
    }
    //$go_api->db->query("UNLOCK TABLES");
    //echo "<b>".$go_api->lng("Backup von Tabelle").":</b> ".$response["Table"]." <b>Status:</b> ".$response["Msg_text"]."<br>";
    //}
    //exec("mv ".$go_info["server"]["backup_dir"]."/* ".$backup_dir."");
} else {

    ////////////////////////////////
    //  Windows Version
    ////////////////////////////////

    // Slashes für mySQL ersetzen
    $backup_dir = str_replace("\\","/",$tmp_dir);

    foreach($tables as $table) {

    $go_api->db->query("LOCK TABLES $table READ");
    $go_api->db->query("FLUSH TABLES");
    $response = $go_api->db->queryOneRecord("BACKUP TABLE $table TO '$backup_dir'");
    $go_api->db->query("UNLOCK TABLES");
    //echo "<b>Backup von Tabelle:</b> ".$response["Table"]." <b>Status:</b> ".$response["Msg_text"]."<br>";
    }
}

// Zippe Dateien
$tgz_part1 = md5(uniqid ("")).".zip";
$tgz_name = $go_info["server"]["temp_dir"]."/".$tgz_part1;
exec("$zip -j $tgz_name $tmp_dir/*");

if($transfer == "download") {

        /*
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$backup_file_name\"");
		*/
	
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
        if($go_info["server"]["os"] == "linux") {
                if($tmp_dir != "" and stristr($tmp_dir,"/home/admispconfig/ispconfig/temp") and !stristr($tmp_dir,"../")) exec("rm -rf $tmp_dir");
        } else {
                // Löschmethode Windows
        }

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
        if($go_info["server"]["os"] == "linux") {
                if($tmp_dir != "" and stristr($tmp_dir,"/home/admispconfig/ispconfig/temp") and !stristr($tmp_dir,"../")) exec("rm -rf $tmp_dir");
        } else {
                // Löschmethode Windows
        }

        // lösche Temp Datei
        @unlink($tgz_name);
        $go_api->msg($msg,$go_api->lng("FTP-Upload Status"));
} else {
        $go_api->errorMessage($go_api->lng("Backup-Methode nicht unterstützt.")." <br>\r\n");
}


?>