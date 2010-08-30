<?php
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

$FILE = "/root/ispconfig/scripts/shell/ftp_logs.php";
$monat = date("m/Y");
$monat_kurz = date("M");
$jahr = date("Y");
$tag = date("j");
$datum = date("d-m-y_H-i-s");
$current_time = time();
$web_doctype_id = 1013;
$user_doctype_id = 1014;
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
$server_id = $mod->system->server_id;

$server = $mod->system->server_conf;
$dist_ftp_version = $mod->system->server_conf["dist_ftp_version"];
$dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];

$ftp_log = $server["server_proftpd_log"];
if(!$mod->file->is_file_lfs($ftp_log)) die();
$path_httpd_root = stripslashes($server["server_path_httpd_root"]);
$dienst = $mod->db->queryOneRecord("SELECT * FROM isp_dienste");

if($dienst["dienst_ftp_status"] == "on"){
  if($dist_ftp_version == "standalone"){
    $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "stop");
  } else {
    $mod->system->daemon_init($dist_ftp_version, "stop");
  }
}

/*
$mod->log->caselog("cp -f $ftp_log $ftp_log.$datum", $FILE, __LINE__);
if($server["server_ftp_log_save"]){
  $mod->log->caselog("touch $ftp_log.ispconfigsave", $FILE, __LINE__);
  $mod->log->caselog("cat $ftp_log >> $ftp_log.ispconfigsave", $FILE, __LINE__);
}

$fp = fopen($ftp_log, "w");
fwrite($fp, "");
fclose($fp);
*/

if(is_file($ftp_log.".0")) exec("grep -iw \"".$monat_kurz.str_pad($tag,3,' ',STR_PAD_LEFT)."\" ".$ftp_log.".0 > ".$ftp_log.".".$datum);
exec("grep -iw \"".$monat_kurz.str_pad($tag,3,' ',STR_PAD_LEFT)."\" ".$ftp_log." >> ".$ftp_log.".".$datum);
if($server["server_ftp_log_save"]){
  $mod->log->caselog("touch $ftp_log.ispconfigsave", $FILE, __LINE__);
  $mod->log->caselog("cat $ftp_log.$datum >> $ftp_log.ispconfigsave", $FILE, __LINE__);
}

sleep(10);

if($dienst["dienst_ftp_status"] == "on"){
  if($dist_ftp_version == "standalone"){
    $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "start");
  } else {
    $mod->system->daemon_init($dist_ftp_version, "start");
  }
}

exec("uniq $ftp_log.$datum $ftp_log.$datum2"); //doppelte Zeilen löschen
exec("rm -f $ftp_log.$datum");
exec("mv $ftp_log.$datum2 $ftp_log.$datum");

$fp = fopen ($ftp_log.".".$datum, "r");
if(!$ftp_log_contents = @fread($fp, filesize ($ftp_log.".".$datum))) $ftp_log_contents = '';
fclose($fp);
while(strstr($ftp_log_contents, "  ")){
  $ftp_log_contents = str_replace("  ", " ", $ftp_log_contents);
}
$fp = fopen ($ftp_log.".".$datum, "w");
fwrite($fp,$ftp_log_contents);
fclose($fp);

$webs = $mod->db->queryAllRecords("SELECT * FROM isp_nodes,isp_isp_web WHERE server_id = '$server_id' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$web_doctype_id."' AND isp_nodes.status = '1'");
if(!empty($webs)){
  foreach($webs as $web){
    $vhost = $web["web_host"].".".$web["web_domain"];
    $web_id = $web["doc_id"];
    $gruppe = "web".$web_id;
    $mod->log->caselog("cat /dev/null > $ftp_log.$vhost", $FILE, __LINE__);
    $users = $mod->db->queryAllRecords("SELECT * FROM isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = $web_id AND isp_dep.parent_doctype_id = $web_doctype_id AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = $user_doctype_id");
    if(!empty($users)){
      foreach($users as $user){
        exec("grep -i ' ".$user["user_username"]." ' $ftp_log.$datum | grep -i $jahr | grep -i $monat_kurz | cut -f8 -d' ' >> $ftp_log.$vhost");
      }
    }
    unset($users);
    //exec("grep -i $path_httpd_root/web$web_id/ $ftp_log.$datum | grep -i $jahr | grep -i $monat_kurz | cut -f8 -d' ' > $ftp_log.$vhost");

    $traffic = 0;

    $fd = fopen("$ftp_log.$vhost", "r");
    while(!feof($fd)){
      $buffer = trim(fgets($fd, 4096));
      $traffic += $buffer;
    }
    fclose ($fd);

    $verify = $mod->db->queryAllRecords("SELECT * FROM isp_traffic WHERE web_id = '$web_id' AND monat = '$monat'");
    if(empty($verify)){
      $mod->db->query("INSERT INTO isp_traffic (web_id, monat, jahr, bytes_ftp, datum) VALUES ('$web_id','$monat','$jahr','$traffic','$current_time')");
    } else {
      $mod->db->query("UPDATE isp_traffic SET bytes_ftp = bytes_ftp + $traffic WHERE web_id = '$web_id' AND monat = '$monat'");
    }
    $mod->log->caselog("rm -f $ftp_log.$vhost", $FILE, __LINE__);
  }
}
$mod->log->caselog("rm -f $ftp_log.$datum", $FILE, __LINE__);
?>