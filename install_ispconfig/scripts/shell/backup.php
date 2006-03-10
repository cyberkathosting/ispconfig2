<?
/*
Copyright (c) 2005, Tribal-Dolphin
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
/**************************
*
* ISPConfig Automatic Backup Script
* Version 1.0
*
***************************/
set_time_limit(0);

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

if($go_info["server"]["do_automated_backups"] != 1) die();

// Erstelle Namen für Backup Datei
$backup_file_name = "backup_".date("Y_m_d",time()).".zip";

// bestimme Web-Pfad
$server = $mod->system->server_conf;
$httpd_root = stripslashes($server["server_path_httpd_root"]);
unset($server);

$zip = $go_info["tools"]["zip"];

// Function for making backup
function do_backup($web_id) {
        global $mod,$go_info,$httpd_root,$zip,$backup_file_name;

                // erstelle temp verzeichnis
                $tmp_dir = $go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].md5(uniqid (""));
                mkdir( $tmp_dir, 0700) or $go_api->errorMessage($go_api->lng("tmp_dir_error"));

        $web_id = intval($web_id);

                $backup_txt = '';
        $backup_txt .= $web_id .',';

        // Hole Web
        $web = $mod->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_web where isp_nodes.doc_id = '$web_id' and isp_nodes.doctype_id = 1013 and isp_nodes.doc_id = isp_isp_web.doc_id");

        // erstelle ewb tar.gz
        $web_pfad = $httpd_root ."/web".$web_id."/web";
        exec("cd $web_pfad; $zip -r $tmp_dir/web".$web_id."_web.zip *");
        $backup_txt .= "web,";

                // erstelle user tar.gz
        $user_pfad = $httpd_root."/web".$web_id."/user";
        exec("cd $user_pfad; $zip -r  $tmp_dir/web".$web_id."_user.zip *");
        $backup_txt .= "user,";

                // erstelle log tar.gz
        $log_pfad = $httpd_root."/web".$web_id."/log";
        exec("cd $log_pfad; $zip -r  $tmp_dir/web".$web_id."_log.zip *");
        $backup_txt .= "log,";

                // erstelle mySQL tar.gz
                //Wenn Web Datenbank hat
        if($web["web_mysql"] == 1) {
          if($datenbanken = $mod->db->queryAllRecords("SELECT datenbankname FROM isp_isp_datenbank WHERE web_id = $web_id")){
            foreach($datenbanken as $datenbank){
              if($go_info["server"]["db_password"] != ""){
                exec("mysqldump -u ".$go_info["server"]["db_user"]." -p".$go_info["server"]["db_password"]." --opt ".$datenbank['datenbankname']." > $tmp_dir/".$datenbank['datenbankname'].".sql");
              } else {
                exec("mysqldump -u ".$go_info["server"]["db_user"]." --opt ".$datenbank['datenbankname']." > $tmp_dir/".$datenbank['datenbankname'].".sql");
              }
            }
          }
          $backup_txt .= "db,";
        }
        $backup_txt = substr($backup_txt,0,-1);
        $backup_txt .= "\r\n";

                // Schreibe backup.dat Inhaltsbeschreibung
                $backup_desc_file = fopen ("$tmp_dir/backup.dat", "w");
                fwrite($backup_desc_file,$backup_txt,strlen($backup_txt));
                fclose($backup_desc_file);

                // erstelle ein einziges zip aus Einzelwebs
                $tgz_part1 = md5(uniqid ("")).".zip";
                $tgz_name = $go_info["server"]["temp_dir"]."/".$tgz_part1;
                exec("$zip -j $tgz_name $tmp_dir/*");

                // Move file in /backup directory
                $backup_dir = $httpd_root ."/web".$web_id."/backup";
                $web_user = fileowner($web_pfad);
                $web_group = filegroup($web_pfad);
                if(!@is_dir($backup_dir)) {
                  mkdir($backup_dir,0755);
                } else {
                  exec("rm -rf $backup_dir/*");
                }
                if(@fileowner($backup_dir) != $web_user) {
                  chown($backup_dir,$web_user);
                }
                if(@filegroup($backup_dir) != $web_group) {
                  chgrp($backup_dir,$web_group);
                }

                exec("mv $tgz_name $backup_dir/$backup_file_name");
                chown("$backup_dir/$backup_file_name",$web_user);
                chgrp("$backup_dir/$backup_file_name",$web_group);

                // Delete temp file
                exec("rm -rf $tmp_dir");

}

// All web site
$webs = $mod->db->queryAllRecords("SELECT * FROM isp_isp_web");
if(!empty($webs)){
  foreach($webs as $web){
    do_backup($web['doc_id']);
  }
}

?>