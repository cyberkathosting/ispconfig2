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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

if(isset($_REQUEST["go_info"])) die('Variable not allowed as REQUEST parameter!');
if(!defined('SERVER_ROOT')) die('Include file is missing. Please run the setup script as described in the installation manual.');


######################################################
#
# hole Infos aus Datenbank
#
######################################################

$user_row = $go_api->db->queryOneRecord("select * from sys_user where doc_id = " . $go_api->auth->userid);

######################################################
#
# User Informationen
#
######################################################

$go_info["user"]["userid"]         = $go_api->auth->userid;
$go_info["user"]["username"]       = $user_row["username"];
$go_info["user"]["private_dir"]    = "";
$go_info["user"]["firstname"]      = $user_row["vorname"];
$go_info["user"]["lastname"]       = $user_row["name"];
$go_info["user"]["groups"]         = "";
$go_info["user"]["status"]         = $go_api->auth->status;
$go_info["user"]["perms"]          = $user_row["perms"];
$go_info["user"]["language"]       = $user_row["language"];
$go_info["user"]["email"]          = $user_row["email"] . "@" . $user_row["domain"];
$go_info["user"]["email_home"]     = $user_row["email_home"];
$go_info["user"]["mail_server"]    = "";
$go_info["user"]["mail_user"]      = "";
$go_info["user"]["mail_password"]  = "";

if($user_row["language"] != '') {
	$go_info["user"]["language"] = $user_row["language"];
} else {
	$go_info["user"]["language"] = $go_info["server"]["lang"];
}

if($go_info["user"]["userid"] != 1) {
  if(is_array($go_api->groups->myGroups())) {
    $info_row = $go_api->db->queryOneRecord("SELECT vorname,name,email FROM isp_isp_reseller WHERE reseller_userid = " . $go_info["user"]["userid"]);
    $go_info["user"]["firstname"]      = $info_row["vorname"];
    $go_info["user"]["lastname"]       = $info_row["name"];
    $go_info["user"]["email"]          = $info_row["email"];
  } else {
    $info_row = $go_api->db->queryOneRecord("SELECT kunde_vorname,kunde_name,kunde_email FROM isp_isp_kunde WHERE webadmin_userid = " . $go_info["user"]["userid"]);
    $go_info["user"]["firstname"]      = $info_row["kunde_vorname"];
    $go_info["user"]["lastname"]       = $info_row["kunde_name"];
    $go_info["user"]["email"]          = $info_row["kunde_email"];
  }
}

#####################################################
#
# Group Information (actual selected Group)
#
#####################################################

$go_info["group"]["groupid"]       = "";
$go_info["group"]["perms"]         = "";
$go_info["group"]["status"]        = "";
$go_info["group"]["title"]         = "";
$go_info["group"]["members"]       = "";
$go_info["group"]["description"]   = "";
$go_info["group"]["group_dir"]     = "";


######################################################
#
# Session Informationen
#
######################################################

$go_info["session"]["id"]            = $s;
$go_info["session"]["approved"]      = true;
$go_info["session"]["site"]          = $user_row["site"];
if($go_info["theme"]["sitename"] == '') {$go_info["session"]["site"] = $user_row["site"];} else {$go_info["session"]["site"] = $go_info["theme"]["sitename"];};
$go_info["session"]["domain"]        = $user_row["domain"];
$go_info["session"]["screen_height"] = 100;
$go_info["session"]["screen_width"]  = 100;

if($go_info["server"]["session_timeout"] > 0) {
	$go_info["session"]["timeout"] = intval($go_info["server"]["session_timeout"]);
} else {
	$go_info["session"]["timeout"]       = 1800; // 1800 Sec = 30 Minuten
}


$modules = explode(",", $user_row["modules"]);
    while (list($key, $val) = each($modules)) {
      	$module_row = $go_api->db->queryOneRecord("SELECT * FROM sys_modules where module_name = '$val' and module_enabled = '1'");
        $go_info["session"]["modules"][] = array(name => $module_row["module_name"],
                                                 title => $module_row["module_title"],
                                                 path => $module_row["module_path"],
                                                 type => $module_row["module_type"]);
		}

#######################################################
#
# themes (design einstellungen)
#
######################################################

$go_info["theme"]["title"]             = $user_row["design"];
// $go_info["theme"]["path"]              = "design/".$go_info["theme"]["title"]."_".$go_info["session"]["screen_width"]."x".$go_info["session"]["screen_height"]."/".$user_row["modul"];
$go_info["theme"]["path"]              = "design/default";
$go_info["theme"]["style_path"]        = "design/".$go_info["theme"]["title"]."_".$go_info["session"]["screen_width"]."x".$go_info["session"]["screen_height"];
$go_info["theme"]["width"]             = "100%";
$go_info["theme"]["modus"]             = "full";
$go_info["theme"]["buttons"]           = "default"; //$user_row["design"];
$go_info["theme"]["page"]["bg_color"]  = "FFFFFF";
if($go_info["theme"]["page"]["box_color"] == '') $go_info["theme"]["page"]["box_color"] = "E4E4E4";
if($go_info["theme"]["page"]["nav_color"] == '') $go_info["theme"]["page"]["nav_color"] = "025CCA";
#######################################################
#
# modul Informationen
#
#######################################################

$sql = "SELECT * FROM sys_modules where module_name = '".$user_row["modul"]."' and module_enabled = '1'";
$module_row = $go_api->db->queryOneRecord($sql);
//print_r($module_row);
//die($sql);
$go_info["modul"]["name"]              = $module_row["module_name"];
$go_info["modul"]["title"]             = $module_row["module_title"];
$go_info["modul"]["path"]              = $module_row["module_path"];
$go_info["modul"]["table_name"]        = $module_row["module_name"];
$go_info["modul"]["include_dir"]       = INCLUDE_ROOT;
$go_info["modul"]["template_dir"]      = $go_info["server"]["template_root"];
$go_info["modul"]["item_order"]        = $user_row["bookmark_order"];
$go_info["modul"]["news"]              = $user_row["news"];

?>