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
include("../../../lib/session.inc.php");

$go_api->auth->check_admin(0);
if($go_info["server"]["mode"] == "demo") $go_api->errorMessage("Update-Funktionen im Demo-Modus nicht verfügbar.");

$method = $HTTP_POST_VARS["method"];
$pkg_name = '';

// erstelle temp Verzeichnis für installation
$tmp_dir = $go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].md5(uniqid (""));
mkdir( $tmp_dir, 0700) or $go_api->errorMessage($go_api->lng("tmp_dir_error"));

// Hole Installer-Paket
if($method == "upload") {
    // File Upload
    if (is_uploaded_file($HTTP_POST_FILES['file']['tmp_name'])) {
        copy($HTTP_POST_FILES['file']['tmp_name'], $tmp_dir.$go_info["server"]["dir_trenner"].$HTTP_POST_FILES['file']['name']) or $go_api->errorMessage($go_api->lng("file_copy_error"));
        $pkg_name = $HTTP_POST_FILES['file']['name'];
    } else {
        $go_api->errorMessage($go_api->lng("file_copy_error"));
    }

} else {
    // URL Download
    $file_local = fopen ($tmp_dir.$go_info["server"]["dir_trenner"].'update.pkg', "wb");
    $file_remote = fopen ($HTTP_POST_VARS["url"], "rb");
    if (!$file_remote) {
        $go_api->errorMessage($go_api->lng("file_open_error"));
        exit;
    }
    while (!feof($file_remote)) {
        fwrite($file_local,fread($file_remote, 2048));
    }
    $pkg_name = 'update.pkg';
    fclose($file_remote);
    fclose($file_local);
}

// md5 hash überprüfen
if($HTTP_POST_VARS["md5"] != "") {
    if(md5_file($tmp_dir.$go_info["server"]["dir_trenner"].$pkg_name) != $HTTP_POST_VARS["md5"]) $go_api->errorMessage($go_api->lng("md5_error"));
}



// entpacke Installer Datei
$unzip = $go_info["tools"]["unzip"];
exec($unzip . " ". $tmp_dir.$go_info["server"]["dir_trenner"].$pkg_name." -d ".$tmp_dir.$go_info["server"]["dir_trenner"]);

$go_api->uses("package");

$go_api->package->parse($tmp_dir,"package.ins");

//print_r($go_api->package->inst);

$go_api->package->install($tmp_dir);

if($go_info["server"]["mode"] != "demo") exec("rm -rf ".escapeshellcmd($tmp_dir));

$go_api->msg("<center>".$go_api->lng("Installation OK")."</center>","Installation");

?>