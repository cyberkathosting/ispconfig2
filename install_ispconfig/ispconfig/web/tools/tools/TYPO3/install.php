<?

/*
Copyright (c) 2007, Typomedia ApS, Allan Jacobsen
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

if($go_info["server"]["mode"] == "demo") $go_api->errorMessage($go_api->lng("Packageinstaller im Demo-Modus nicht verfügbar."));

set_time_limit(1800);// 30 Minuten
//ignore_user_abort(1);

// import Variables
$mysql_server = addslashes($HTTP_POST_VARS["mysql_server"]);
$mysql_user = addslashes($HTTP_POST_VARS["mysql_user"]);
$mysql_passwort = addslashes($HTTP_POST_VARS["mysql_passwort"]);
$mysql_database = addslashes($HTTP_POST_VARS["mysql_db"]);
$install_passwort = addslashes($HTTP_POST_VARS["install_passwort"]);
$admin_passwort = addslashes($HTTP_POST_VARS["admin_passwort"]);


$webs = $HTTP_POST_VARS["webs"];
if(!is_string($webs)) $go_api->msg($go_api->lng("No web was selected."),$go_api->lng("Install Status"));

$T3version = $HTTP_POST_VARS["T3version"];
if(!is_string($T3version)) $go_api->msg($go_api->lng("No TYPO3 version was selected."));


// check mysql server hostname
if(!preg_match("/^[a-zA-Z0-9\-\.]{0,255}$/",$mysql_server)) $go_api->errorMessage($go_api->lng("The hostname of the Mysql Server <br>is invalid."));

// find Web Path
$server = $go_api->db->queryOneRecord("SELECT * from isp_server");
$httpd_root = $server["server_path_httpd_root"];
unset($server);

// check connection to mysql server
$link = @mysql_connect($mysql_server, $mysql_user, $mysql_passwort);
if (!$link) $go_api->errorMessage($go_api->lng("Could not connect") . mysql_error());

$res =  @mysql_select_db($mysql_database);
if (!$res) $go_api->errorMessage($go_api->lng("Could not select database"));
 
// check webpath exists
$path = $httpd_root.'/web'.substr($webs,4).'/web/';
if (!is_dir($path)) $go_api->errorMessage($go_api->lng("Install path does not exist:").$path.':.');
// fill database table with data
$install_data = array (
  'crdate' => time(),
  'arg1' => $mysql_server,
  'arg2' => $mysql_user,
  'arg3'   => $mysql_passwort,
  'arg4'   => $mysql_database,
  'arg5' => $install_passwort,
  'arg6'   => $admin_passwort,
  'arg7'    => substr($webs,4),
  'arg8'   => $T3version,
  'pending'   => 1
);

$go_api->db->insert('isp_isp_web_package',$install_data);
// touch .run file
touch('/home/admispconfig/ispconfig/.run');

$go_api->msg($go_api->lng("TYPO3 install started."));



?>