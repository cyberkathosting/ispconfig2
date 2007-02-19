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
include("../lib/config.inc.php");
include("../lib/session.inc.php");

// Variable holen
$app = $HTTP_GET_VARS["app"];
if(!preg_match("/^[a-zA-Z0-9_]*$/",$app) or $app == "") $go_api->errorMessage("Fehler in Applikationsnamen");

//Applikation überprüfen
$is_myapp = 0;
foreach($go_info["session"]["modules"] as $myapp) {
    if($myapp["name"] == $app) $is_myapp = 1;
}

$module_row = $go_api->db->queryOneRecord("SELECT * FROM sys_modules where module_name = '".addslashes($app)."' and module_enabled = '1'");

// Lizenz für Modul überprüfen
$lz_app = $app;
// Bei virt. Modulen, setze zu checkendes Modul auf multidoc
if($module_row["module_type"] == 'v') $lz_app = 'multidoc';

if(is_array($module_row) and $is_myapp == 1) {
$go_info["modul"]["name"]              = $module_row["module_name"];
$go_info["modul"]["title"]             = $module_row["module_title"];
$go_info["modul"]["path"]              = $module_row["module_path"];
$go_info["modul"]["table_name"]        = $module_row["module_name"];
$go_info["temp"] = '';
$go_api->session->save();
} else {
$go_api->errorMessage("Das Modul '$app' wurde nicht gefunden oder ist in Ihrem Account nicht aktiviert.");
}

header("Location: index.php?s=$s");
exit;
?>