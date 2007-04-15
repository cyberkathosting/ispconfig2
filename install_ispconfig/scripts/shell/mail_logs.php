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

$FILE = "/root/ispconfig/scripts/shell/mail_logs.php";
$monat = date("m/Y");
$monat_kurz = date("M");
$jahr = date("Y");
$datum = date("d-m-y_H-i-s");
$current_time = time();
$web_doctype_id = 1013;
$user_doctype_id = 1014;
$domain_doctype_id = 1015;
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
$server_id = $mod->system->server_id;
$dist_mail_log = $mod->system->server_conf["dist_mail_log"];
$dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];

if(!is_file($dist_mail_log)) die();
$server = $mod->system->server_conf;

$path_httpd_root = stripslashes($server["server_path_httpd_root"]);
$dienst = $mod->db->queryOneRecord("SELECT * FROM isp_dienste");

if($dienst["dienst_smtp_status"] == "on") $mod->system->daemon_init($mod->system->server_conf["server_mta"], "stop");

$mod->log->caselog("cp -f $dist_mail_log $dist_mail_log.$datum", $FILE, __LINE__);
if($server["server_mail_log_save"]){
  $mod->log->caselog("touch $dist_mail_log.ispconfigsave", $FILE, __LINE__);
  $mod->log->caselog("cat $dist_mail_log >> $dist_mail_log.ispconfigsave", $FILE, __LINE__);
}

$fp = fopen($dist_mail_log, "w");
fwrite($fp, "");
fclose($fp);

if($dienst["dienst_smtp_status"] == "on") $mod->system->daemon_init($mod->system->server_conf["server_mta"], "start");


exec("uniq $dist_mail_log.$datum $dist_mail_log.$datum2"); //doppelte Zeilen löschen
exec("rm -f $dist_mail_log.$datum");
exec("mv $dist_mail_log.$datum2 $dist_mail_log.$datum");

$fp = fopen ($dist_mail_log.".".$datum, "r");
$mail_log_contents = fread($fp, filesize ($dist_mail_log.".".$datum));
fclose($fp);
while(strstr($mail_log_contents, "  ")){
  $mail_log_contents = str_replace("  ", " ", $mail_log_contents);
}
$fp = fopen ($dist_mail_log.".".$datum, "w");
fwrite($fp,$mail_log_contents);
fclose($fp);

$webs = $mod->db->queryAllRecords("select * from isp_nodes,isp_isp_web WHERE server_id = '$server_id' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$web_doctype_id."' AND isp_nodes.status = '1'");

foreach($webs as $web){
$vhost = $web["web_host"].".".$web["web_domain"];
$domain = $web["web_domain"];
$web_id = $web["doc_id"];
$gruppe = "web".$web_id;
$domain_arr[] = $domain;

exec("grep -iw ".$mod->system->server_conf["server_mta"]." $dist_mail_log.$datum | grep -iw from | grep -iw $domain | grep -iw $monat_kurz | cut -f2 -d, | cut -f2 -d= > $dist_mail_log.$vhost");

$codomains = $mod->db->queryAllRecords("SELECT isp_isp_domain.domain_domain from isp_dep,isp_isp_domain where isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id ='$domain_doctype_id' and isp_dep.parent_doctype_id = '$web_doctype_id' and isp_dep.parent_doc_id = '".$web["doc_id"]."' and isp_isp_domain.status != 'd'");

foreach($codomains as $codomain){
  if(!in_array($codomain["domain_domain"], $domain_arr)) exec("grep -iw ".$mod->system->server_conf["server_mta"]." $dist_mail_log.$datum | grep -iw from | grep -iw ".$codomain["domain_domain"]." | grep -iw $monat_kurz | cut -f2 -d, | cut -f2 -d= >> $dist_mail_log.$vhost");
  $domain_arr[] = $codomain["domain_domain"];
}
unset($domain_arr);
$traffic = 0;

$fd = fopen("$dist_mail_log.$vhost", "r");
if ($fd) {
	while(!feof($fd)){
		$buffer = trim(fgets($fd, 4096));
  		if(is_numeric($buffer)) $traffic += $buffer;
	}
}
fclose ($fd);

/////////// INCOMING MAILS ////////////
$users = $mod->db->queryAllRecords("SELECT * from isp_dep, isp_isp_user where isp_dep.parent_doc_id = $web_id and isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.child_doc_id = isp_isp_user.doc_id and isp_dep.child_doctype_id = $user_doctype_id");

if(!empty($users)){
  foreach($users as $user){
    if(is_file("/home/admispconfig/mailstats/".$user["user_username"])){
      $fd = fopen("/home/admispconfig/mailstats/".$user["user_username"], "r");
	  if ($fd) {
      	while(!feof($fd)){
        	$buffer = trim(fgets($fd, 4096));
        	if(is_numeric($buffer)) $traffic += $buffer;
      	}
	  }
      fclose ($fd);
      $mod->log->caselog("rm -f /home/admispconfig/mailstats/".$user["user_username"], $FILE, __LINE__);
    }
  }
}
/////////// INCOMING MAILS ENDE ///////


$verify = $mod->db->queryAllRecords("SELECT * FROM isp_traffic WHERE web_id = '$web_id' AND monat = '$monat'");
if(empty($verify)){
$mod->db->query("INSERT INTO isp_traffic (web_id, monat, jahr, bytes_mail, datum) VALUES ('$web_id','$monat','$jahr','$traffic','$current_time')");
} else {
$mod->db->query("UPDATE isp_traffic SET bytes_mail = bytes_mail + $traffic WHERE web_id = '$web_id' AND monat = '$monat'");
}
$mod->log->caselog("rm -f $dist_mail_log.$vhost", $FILE, __LINE__);
}
$mod->log->caselog("rm -f $dist_mail_log.$datum", $FILE, __LINE__);
?>