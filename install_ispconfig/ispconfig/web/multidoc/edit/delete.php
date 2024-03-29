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

$go_api->auth->check_write($gid);

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

$go_api->uses("tree,doc");
$go_api->tree->set_table($go_info["modul"]["table_name"]);

if($action == "") $action = "do";
// versuche Tree_id zu bestimmen
if($tree_id < 1 and !empty($doc_id) and !empty($doctype_id)) {
$tree_row = $go_api->db->queryOneRecord("SELECT tree_id from ".$go_info["modul"]["table_name"]."_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
if(!empty($tree_row["tree_id"])) $tree_id = $tree_row["tree_id"];
unset($tree_row);
}

if($tree_id < 1) $go_api->errorMessage("Fehler beim L�schen des Dokumentes, tree_id konnte nicht ermittelt werden");
if($type == "n") {
$go_api->tree->node_delete($tree_id,$gid,$action);
} else {
$go_api->tree->item_delete($tree_id,$gid,$action);
}

if($go_info["modul"]["sidenav"] == 'flat') {
    header("Location: ../frame_start.php?$session");
} else {
    header("Location: ../../index.php?$session");
}

exit;
?>






