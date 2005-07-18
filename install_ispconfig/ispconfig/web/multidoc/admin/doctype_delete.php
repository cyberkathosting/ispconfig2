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

if(!$go_api->auth->check_admin(0,1)) die("Access not permitted.");

if ($groupid = "") $groupid = 0;
$go_api->uses("doc");

$doctype_id = intval($_REQUEST["doctype_id"]);

if($go_api->auth->check_admin(0)) {

    if($doc = $go_api->doc->doctype_get($doctype_id)){
        
        // löschen des Dokumententyps
        $go_api->db->query("DELETE FROM doctype where doctype_id = '$doctype_id'");
        
        // löschen der Datentabelle
        $go_api->db->dropTable($doc->storage_path);
        
        // löschen der nodes
        $go_api->db->query("DELETE FROM ".$doc->modul."_nodes where doctype_id = '$doctype_id'");
        
        // löschen der Dokumenten abhängigkeiten
        $go_api->db->query("DELETE FROM ".$doc->modul."_dep where parent_doctype_id = '$doctype_id' or child_doctype_id = '$doctype_id'");
        
        // löschen der Datei abhängigkeiten
        $go_api->db->query("DELETE FROM file_nodes where parent_doctype_id = '$doctype_id'");
        
        // löschen der Termin abhängigkeiten
        $go_api->db->query("DELETE FROM termin_event where parent_doctype_id = '$doctype_id'");

    } else {
    $go_api->errorMessage("Doctype not found.");
    }

}

header("Location: ../../index.php?$session");
exit;
?>






