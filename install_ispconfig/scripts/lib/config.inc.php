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

include("/home/admispconfig/ispconfig/lib/config.inc.php");
$go_info["isp"]["httpd"]["use_old_conf_on_errors"] = 1; // if httpd syntax check gives back errors, use old, working configuration

//////////////// DO NOT EDIT BELOW !!! //////////////////
$go_info["isp"]["server_root"] = "/root/ispconfig";
$go_info["isp"]["include_root"] = $go_info["isp"]["server_root"] . $go_info["server"]["dir_trenner"] ."scripts".$go_info["server"]["dir_trenner"]."lib";
$go_info["isp"]["classes_root"] = $go_info["isp"]["include_root"] . $go_info["server"]["dir_trenner"] ."classes";
$go_info["isp"]["server_id"] = 1;

/**************************************
* Server Einstellungen
* V1.0 ISPConfig SERVER Modules
***************************************/
$link = @mysql_connect($go_info["server"]["db_host"], $go_info["server"]["db_user"], $go_info["server"]["db_password"])
or die("Could not connect to MySQL server!");
mysql_select_db($go_info["server"]["db_name"]);
$server_params = mysql_query("SELECT * FROM isp_server WHERE doc_id = '".$go_info["isp"]["server_id"]."'");
if(!$go_info["isp"]["server_conf"] = mysql_fetch_array($server_params)){
  die("No results found!");
} else {
  foreach($go_info["isp"]["server_conf"] as $key => $value) {
    $value = trim($value);
    while(strlen($value) > 1 && substr($value,-1) == "/") $value = substr($value,0,strlen($value)-1);
    $go_info["isp"]["server_conf"][$key] = $value;
  }
  $key = NULL;
  $value = NULL;
}
mysql_free_result($server_params);
$server_params = mysql_query("SELECT server_ip FROM isp_server_ip WHERE server_id = '".$go_info["isp"]["server_id"]."'");
while ($row = mysql_fetch_array($server_params)) {
  $ips[] = $row['server_ip'];
}
$go_info["isp"]["server_conf"]["ips"] = $ips;
unset($ips);
mysql_free_result($server_params);
mysql_close($link);

$go_info["modules"]["string"] = "string";
$go_info["modules"]["file"] = "file";
$go_info["modules"]["system"] = "system";
$go_info["modules"]["mail"] = $go_info["isp"]["server_conf"]["server_mta"];
$go_info["modules"]["procmail"] = "procmail";
$go_info["modules"]["dns"] = "bind";
$go_info["modules"]["cron"] = "cron";
?>