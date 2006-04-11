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

set_time_limit(0);
// Überprüfung, ob parallel noch ein anderer Prozeß läuft
if(is_file("/root/ispconfig/.ispconfig_lock")){
  clearstatcache();
  for($i=0;$i<120;$i++){ // max. 120 Sek. warten, dann weitermachen
    if(is_file("/root/ispconfig/.ispconfig_lock")){
      sleep(1);
      clearstatcache();
    }
  }
}

@touch("/root/ispconfig/.ispconfig_lock");

$sendmail_restart = 0;
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/config.lib.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

$isp_web = new isp_web;

echo "start\n";

$old_web_root_file = "/root/ispconfig/.old_path_httpd_root";

/////////////// Web- u. Userdaten aus DB holen ////////////////
$w_ds = $mod->db->queryAllRecords("SELECT * FROM isp_isp_web");
if(!empty($w_ds)){
  foreach($w_ds as $w_d){
    $mod->system->data["isp_isp_web"][$w_d["doc_id"]] = $w_d;
  }
}

$u_ds = $mod->db->queryAllRecords("SELECT * FROM isp_isp_user");
if(!empty($u_ds)){
  foreach($u_ds as $u_d){
    $mod->system->data["isp_isp_user"][$u_d["doc_id"]] = $u_d;
  }
}

$mod->system->data["isp_server_ip"] = $mod->db->queryAllRecords("SELECT * FROM isp_server_ip WHERE server_id = '".$mod->system->server_id."' AND server_ip != ''");
$mod->system->data["isp_dienste"] = $mod->db->queryOneRecord("SELECT * FROM isp_dienste");
////////////////////////////////////////////////////////////////

$dienst = $mod->system->data["isp_dienste"];

if($dienst["dienst_www_status"] == "on"){
  $vhostcompare1 = md5_file($isp_web->vhost_conf);
  $sslcompare1 = md5($mod->system->grep($mod->system->cat($isp_web->vhost_conf), "SSL"));
}

if($dienst["dienst_ftp_status"] == "on"){
  if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
    $md5_ftp_conf_alt = md5_file($isp_web->ftp_conf);
  }
}

if($dienst["dienst_smtp_status"] == "on"){
  $old_local_host_names = $mod->file->rf($mod->system->server_conf["server_sendmail_cw"]);
  $old_virtusertable = $mod->file->rf($mod->system->server_conf["server_sendmail_virtuser_datei"]);
  $old_mta_config = md5($old_local_host_names.$old_virtusertable);
}

$check_web_path = $mod->db->queryAllRecords("SELECT * from isp_isp_web, isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->web_doctype_id."' and isp_isp_web.status = '' and isp_isp_web.server_id = '".$mod->system->server_id."' and isp_nodes.status = '1'");
if(!empty($check_web_path)){
  foreach($check_web_path as $web){
    $doc_id = $web["doc_id"];
    $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id;
    if(!is_dir($web_path)){
      $mod->db->query("UPDATE isp_isp_web SET status = 'u' WHERE doc_id = '$doc_id'");
      $mod->system->data["isp_isp_web"][$doc_id]["status"] = 'u';
    }
  }
}

$web_insert = $mod->db->queryAllRecords("SELECT * from isp_isp_web, isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->web_doctype_id."' and isp_isp_web.status = 'n' and isp_isp_web.server_id = '".$mod->system->server_id."' and isp_nodes.status = '1'");
if(!empty($web_insert)){
  foreach($web_insert as $web) {
    $doc_id = $web["doc_id"];
    $doctype_id = $web["doctype_id"];
    $isp_web->web_insert($doc_id,$doctype_id,$mod->system->server_id);
    echo "INSERT: ".$doc_id."\n";
  }
}

$web_update = $mod->db->queryAllRecords("SELECT * from isp_isp_web, isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->web_doctype_id."' and isp_isp_web.status = 'u' and isp_isp_web.server_id = '".$mod->system->server_id."' and isp_nodes.status = '1'");
$web_update_ssl_action = 0;
if(!empty($web_update)){
  foreach($web_update as $web) {
    $doc_id = $web["doc_id"];
    $doctype_id = $web["doctype_id"];
    $isp_web->web_update($doc_id,$doctype_id,$mod->system->server_id);
    echo "UPDATE: ".$doc_id."\n";
    if($web["ssl_action"] == "save") $web_update_ssl_action += 1;
  }
}

// TB: korrigiere unmögliche Zustände (Webs mit Status u oder n die im Papierkorb liegen)
$tmp_web = $mod->db->queryAllRecords("SELECT isp_isp_web.doc_id as doc_id from isp_isp_web, isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->web_doctype_id."' and (isp_isp_web.status = 'u' or isp_isp_web.status = 'n') and isp_isp_web.server_id = '".$mod->system->server_id."' and isp_nodes.status != '1'");
if(is_array($tmp_web)) {
        foreach($tmp_web as $tmp_item) {
                $mod->db->query("UPDATE isp_isp_web SET status = 'd' WHERE doc_id = ".$tmp_item["doc_id"]);
        }
}
unset($tmp_web);
unset($tmp_item);

$web_delete = $mod->db->queryAllRecords("SELECT * from isp_isp_web, isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->web_doctype_id."' and isp_isp_web.status = 'd' and isp_isp_web.server_id = '".$mod->system->server_id."'");
if(!empty($web_delete)){
  foreach($web_delete as $web) {
    $doc_id = $web["doc_id"];
    $doctype_id = $web["doctype_id"];
    $isp_web->web_delete($doc_id,$doctype_id,$mod->system->server_id);
    echo "DELETE: ".$doc_id."\n";
  }
}

// TB: Setze Status der Co-Domains zurück
$mod->db->query("UPDATE isp_isp_domain SET status = '' WHERE status != ''");

$user_insert = $mod->db->queryAllRecords("SELECT * from isp_isp_user, isp_nodes where isp_isp_user.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' and isp_isp_user.status = 'n' and isp_nodes.status = '1'");
if(!empty($user_insert)){
  foreach($user_insert as $user) {
    $doc_id = $user["doc_id"];
    $doctype_id = $user["doctype_id"];
    $isp_web->user_insert($doc_id,$doctype_id);
    echo "INSERT USER: ".$doc_id."\n";
  }
}

$user_update = $mod->db->queryAllRecords("SELECT * from isp_isp_user, isp_nodes where isp_isp_user.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' and isp_isp_user.status = 'u' and isp_nodes.status = '1'");
if(!empty($user_update)){
  foreach($user_update as $user) {
    $doc_id = $user["doc_id"];
    $doctype_id = $user["doctype_id"];
    $isp_web->user_update($doc_id,$doctype_id);
    echo "UPDATE USER: ".$doc_id."\n";
  }
}

// TB: korrigiere unmögliche Zustände (User mit Status u oder n die im Papierkorb liegen)
$tmp_user = $mod->db->queryAllRecords("SELECT isp_isp_user.doc_id as doc_id from isp_isp_user, isp_nodes where isp_isp_user.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' and (isp_isp_user.status = 'u' or isp_isp_user.status = 'n') and isp_nodes.status != '1'");
if(is_array($tmp_user)) {
        foreach($tmp_user as $tmp_item) {
                $mod->db->query("UPDATE isp_isp_user SET status = 'd' WHERE doc_id = ".$tmp_item["doc_id"]);
        }
}
unset($tmp_user);
unset($tmp_item);

$user_delete = $mod->db->queryAllRecords("SELECT * from isp_isp_user, isp_nodes where isp_isp_user.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' and isp_isp_user.status = 'd'");
if(!empty($user_delete)){
  foreach($user_delete as $user) {
    $doc_id = $user["doc_id"];
    $doctype_id = $user["doctype_id"];
    $isp_web->user_delete($doc_id,$doctype_id);
    echo "DELETE USER: ".$doc_id."\n";
  }
}
$mod->mail->make_local_host_names();
$mod->mail->make_virtusertable();

$isp_web->make_vhost($mod->system->server_id);

/////////// Web Root in httpd.conf schreiben /////////////
if(is_file($old_web_root_file)){
  $old_web_root = trim($mod->file->unix_nl($mod->file->rf($old_web_root_file)));
  $httpd_conf = $mod->file->rf($mod->system->server_conf["dist_httpd_conf"]);
  $httpd_conf = str_replace($old_web_root, $mod->system->server_conf["server_path_httpd_root"], $httpd_conf);
  $mod->file->wf($mod->system->server_conf["dist_httpd_conf"], $httpd_conf);
}
$mod->file->wf($old_web_root_file, $mod->system->server_conf["server_path_httpd_root"]);
////////////Web Root in httpd.conf schreiben ENDE /////////////

if($dienst["dienst_www_status"] == "on"){
  $vhostcompare2 = md5_file($isp_web->vhost_conf);
  $sslcompare2 = md5($mod->system->grep($mod->system->cat($isp_web->vhost_conf), "SSL"));

  if($vhostcompare1 != $vhostcompare2){

    if($sslcompare1 != $sslcompare2 || $web_update_ssl_action != 0){
      $isp_web->apache_restart();
      unset($web_update_ssl_action);
    } else {
      $isp_web->apache_reload();
    }
  }
}

if($dienst["dienst_smtp_status"] == "on"){
  $new_local_host_names = $mod->file->rf($mod->system->server_conf["server_sendmail_cw"]);
  $new_virtusertable = $mod->file->rf($mod->system->server_conf["server_sendmail_virtuser_datei"]);
  $new_mta_config = md5($new_local_host_names.$new_virtusertable);
  if($new_mta_config != $old_mta_config) $mod->mail->smtp_restart();
}

$dns_write = $mod->db->queryAllRecords("select dns_isp_dns.doc_id from dns_nodes,dns_isp_dns WHERE dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = '".$isp_web->dns_doctype_id."' AND dns_isp_dns.status != ''");
if(!empty($dns_write)){
  $bind_restart = 0;
  foreach($dns_write as $write){
    $doc_id = $write["doc_id"];
    $bind_restart += $mod->dns->make_zonefile($doc_id);
    $mod->db->query("UPDATE dns_isp_dns SET status = '' where doc_id = '$doc_id'");
  }
}

$slave_dnss = $mod->db->queryAllRecords("SELECT dns_secondary.* FROM dns_nodes,dns_secondary WHERE dns_nodes.doc_id = dns_secondary.doc_id AND dns_nodes.doctype_id = '".$isp_web->slave_doctype_id."' AND dns_secondary.status != ''");
if(!empty($dns_write) || !empty($slave_dnss)){
  $bind_restart += $mod->dns->make_named($mod->system->server_id);

  $bind_restart += $mod->dns->make_reverse_zonefile($mod->system->server_id);

  if($bind_restart >= 1 && $dienst["dienst_dns_status"] == "on"){
    $mod->dns->named_restart();
  }
  $mod->dns->del_file();
}

$isp_web->make_ftp($mod->system->server_id);
if($dienst["dienst_ftp_status"] == "on"){
  if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
    $md5_ftp_conf_neu = md5_file($isp_web->ftp_conf);
    if($md5_ftp_conf_alt != $md5_ftp_conf_neu) $isp_web->ftp_restart();
    unset($md5_ftp_conf_neu);
    unset($md5_ftp_conf_alt);
  }
}

//Firewall-Konfiguration
$isp_web->make_firewall();

//Monitor
$isp_web->monitor();

//Dienste
$isp_web->dienste();

//trashscan - Antivirus
$mod->system->make_trashscan();

// checken, ob Apache läuft
exec("ps ax | grep httpd | grep -v ispconfig | grep -v grep", $httpd_check, $ret_val1);
unset($httpd_check);
exec("ps ax | grep apache | grep -v ispconfig | grep -v grep", $apache_check, $ret_val2);
unset($apache_check);
if($ret_val1 != 0 && $ret_val2 != 0  && $dienst["dienst_www_status"] == "on") $isp_web->apache_restart();

///////////// isp_com ////////////
$coms = $mod->db->queryAllRecords("SELECT * FROM isp_com ORDER BY tstamp ASC");
if(!empty($coms)){
  foreach($coms as $com){
    $sc = md5($com["modul"].$com["funktion"].addslashes($com["data"]).$go_info["server"]["db_user"].$go_info["server"]["db_password"]);
    if($sc == $com["sc"] && $mod->system->server_id == $com["server_id"]){
      $com["data"] = unserialize($com["data"]);
      $mod->uses($com["modul"]);
      $mod->$com["modul"]->$com["funktion"]($com["data"]);
    }
  }
  $mod->db->query("DELETE FROM isp_com");
}
//////////// isp_com ENDE ////////

// Webs und User endgültig löschen
$isp_web->web_user_clean();

if($go_info["server"]["network_config"]) $mod->system->network_config();

$mod->log->logrotate();

exec("pwconv &> /dev/null");
exec("grpconv &> /dev/null");
//exec("/root/ispconfig/httpd/bin/apachectl graceful &> /dev/null");

echo "ende\n";

// lock-Datei löschen
@unlink("/root/ispconfig/.ispconfig_lock");
?>