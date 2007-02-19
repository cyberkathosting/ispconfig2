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

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

$dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
$dist_httpd_daemon = $mod->system->server_conf["dist_httpd_daemon"];
$dist_bind_init_script = $mod->system->server_conf["dist_bind_init_script"];
$dist_ftp_version = $mod->system->server_conf["dist_ftp_version"];

$mod->system->data["isp_server_ip"] = $mod->db->queryAllRecords("SELECT * FROM isp_server_ip WHERE server_id = '".$mod->system->server_id."' AND server_ip != ''");
if($go_info["server"]["network_config"]) $mod->system->network_config();

$dienst = $mod->db->queryOneRecord("SELECT * FROM isp_dienste");
if($dienst["dienst_firewall_status"] != "off"){
  $mod->system->daemon_init("bastille-firewall", "restart");
} else {
  if(is_file("/var/lock/subsys/bastille-firewall")) $mod->system->daemon_init("bastille-firewall", "stop");
}

if($dienst["dienst_www_status"] != "off"){
  $mod->system->daemon_init($dist_httpd_daemon, "restart");
} else {
  $mod->system->daemon_init($dist_httpd_daemon, "stop");
}

if($dienst["dienst_smtp_status"] != "off"){
  $mod->system->daemon_init($mod->system->server_conf["server_mta"], "stop");
  $mod->system->daemon_init($mod->system->server_conf["server_mta"], "start");
} else {
  $mod->system->daemon_init($mod->system->server_conf["server_mta"], "stop");
}

if($dienst["dienst_dns_status"] != "off"){
  $mod->system->daemon_init($dist_bind_init_script, "restart");
} else {
  $mod->system->daemon_init($dist_bind_init_script, "stop");
}

if($dienst["dienst_ftp_status"] != "off"){
  if($dist_ftp_version == "standalone"){
    $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "restart");
  } else {
    $mod->system->daemon_init($dist_ftp_version, "restart");
  }
} else {
  if($dist_ftp_version == "standalone"){
    $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "stop");
  } else {
    $mod->system->daemon_init($dist_ftp_version, "stop");
  }
}
?>