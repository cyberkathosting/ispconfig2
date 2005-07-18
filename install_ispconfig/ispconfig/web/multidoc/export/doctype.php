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

include("../../../lib/config.inc.php");
$set_header = 0;
include("../../../lib/session.inc.php");

if(!$go_api->auth->check_admin(0,1)) die("Access not permitted.");


$go_api->uses("doc");

$doctype_id = addslashes($doctype_id);

if(!empty($doctype_id) and $go_api->auth->check_admin(0)) {


$doc = $go_api->doc->doctype_get($doctype_id);
$row = $go_api->db->queryOneRecord("SELECT * from doctype where doctype_id = '$doctype_id'");

$doctype_id = $row["doctype_id"];
$doctype_userid = $row["userid"];
$doctype_groupid = $row["groupid"];
$doctype_modul = $row["doctype_modul"];
$doctype_name = $row["doctype_name"];
$doctype_title = urlencode($row["doctype_title"]);
$doctype_tree = $row["doctype_tree"];

$tableDef = $go_api->db->tableInfo($doctype_modul.'_'.$doctype_name);

$filedata = "$doctype_id,$doctype_userid,$doctype_groupid,$doctype_modul,$doctype_name,$doctype_title,$doctype_tree#::#";
$filedata .= serialize($doc)."#::#";
$filedata .= serialize($tableDef);


header("Content-Disposition: attachment; filename=$doctype_name.dtd");
header("Content-Type: application/octet-stream");
echo $filedata;

/*
$fp = fopen ($go_info["server"]["files_dir"].$go_info["server"]["dir_trenner"].$doctype_name.".dtd", "w") or die("cannot open file in writemode");
fwrite($fp,$filedata);
fclose($fp);

############################################################
$doc = "";
$filename = $go_info["server"]["files_dir"].$go_info["server"]["dir_trenner"]."user.dtd";

// Datei Öffnen
$fp = fopen ($filename, "r") or die("cannot open file in readmode");
$filedata = fread ($fp, filesize ($filename));
fclose($fp);

$data = explode("#::#",$filedata);

if(!list( $doctype_id, $doctype_userid, $doctype_groupid, $doctype_modul, $doctype_name, $doctype_title, $doctype_tree ) = explode(",",$data[0])) die("File Corrupted");
$doctype_title = urldecode($doctype_title);
$doc = unserialize($data[1]) or die("DocumentType Definition Corrupted");

print_r($doc);

*/
}



?>